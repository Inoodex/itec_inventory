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
        return response($mpdf->Output('Monthly_Revenue_Report_' . now()->format('Y_m_d_His') . '.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function generate()
    {
        $month = now()->month;
        $year = now()->year;
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        $totalSales = Sale::whereBetween('created_at', [$start, $end])->sum('payble');
        $totalPurchases = Purchase::whereBetween('created_at', [$start, $end])->sum('total_price');
        $totalExpenses = DailyExpense::whereBetween('created_at', [$start, $end])->sum('amount');

        Revenue::updateOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'total_sales' => $totalSales,
                'total_purchases' => $totalPurchases,
                'total_expenses' => $totalExpenses,
            ]
        );

        return redirect()->route('revenues.index')
            ->with('success', 'Monthly revenue summary updated successfully!');
    }

     public function export($id)
    {
        $revenue = Revenue::findOrFail($id);
        $html = view('frontend.pages.revenue.pdf', compact('revenue'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        $filename = "Revenue_Report_{$revenue->month_name}_{$revenue->year}.pdf";

        return response($mpdf->Output($filename, 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}