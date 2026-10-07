<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\DailyExpense;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class RevenueController extends Controller
{
    public function index()
    {
        $revenues = Revenue::orderByDesc('year')->orderByDesc('month')->get();
        return view('frontend.pages.revenue.index', compact('revenues'));
    }

    public function downloadPdf()
    {
        ini_set('memory_limit', '512M');
        $revenues = Revenue::orderByDesc('year')->orderByDesc('month')->get();
        $html = view('pdf.revenue', compact('revenues'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        $fileName = 'Monthly_Revenue_Report_' . now()->format('Y_m_d_His') . '.pdf';
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function generate(Request $request)
    {
        // 1. Find earliest transaction date from Sales, Purchases, and DailyExpenses
        $earliestSale = Sale::min('created_at');
        $earliestPurchase = Purchase::min('created_at');
        $earliestExpense = DailyExpense::min('date') ?? DailyExpense::min('created_at');

        $dates = array_filter([$earliestSale, $earliestPurchase, $earliestExpense]);

        $startDate = !empty($dates)
            ? Carbon::parse(min($dates))->startOfMonth()
            : now()->startOfMonth();

        $endDate = now()->endOfMonth();

        $current = $startDate->copy();
        $generatedCount = 0;

        while ($current->lte($endDate)) {
            $this->calculateAndSaveRevenue($current->year, $current->month);
            $generatedCount++;
            $current->addMonth();
        }

        $fromLabel = $startDate->format('M Y');
        $toLabel = now()->format('M Y');

        return redirect()->route('revenues.index')
            ->with('success', "Revenue synchronized successfully from {$fromLabel} to {$toLabel} ({$generatedCount} months updated)!");
    }

    private function calculateAndSaveRevenue(int $year, int $month): Revenue
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        $totalSales = (float) Sale::whereBetween('created_at', [$start, $end])->sum('payble');
        $totalPurchases = (float) Purchase::whereBetween('created_at', [$start, $end])->sum('total_price');
        $totalExpenses = (float) DailyExpense::where(function ($q) use ($start, $end) {
            $q->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
              ->orWhere(function ($sub) use ($start, $end) {
                  $sub->whereNull('date')->whereBetween('created_at', [$start, $end]);
              });
        })->sum('amount');

        $netProfit = $totalSales - $totalPurchases - $totalExpenses;

        return Revenue::updateOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'total_sales' => $totalSales,
                'total_purchases' => $totalPurchases,
                'total_expenses' => $totalExpenses,
                'net_profit' => $netProfit,
            ]
        );
    }

     public function export($id)
    {
        $revenue = Revenue::findOrFail($id);
        ini_set('memory_limit', '512M');
        $html = view('frontend.pages.revenue.pdf', compact('revenue'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        $filename = "Revenue_Report_{$revenue->month_name}_{$revenue->year}.pdf";
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}