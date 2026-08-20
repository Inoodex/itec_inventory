<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class VendorDueController extends Controller
{
    /**
     * Display a listing of all outstanding vendor dues.
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['vendor', 'product'])
            ->where('due', '>', 0);

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $purchases = $query->latest()->get();
        $vendors = Vendor::orderBy('name')->get();

        // Calculate statistics
        $totalDueAmount = $purchases->sum('due');
        $totalBillAmount = $purchases->sum('total_price');
        $totalPaidAmount = $purchases->sum('payment');
        $pendingBillsCount = $purchases->count();
        $uniqueVendorsCount = $purchases->pluck('vendor_id')->unique()->count();

        $paymentAccounts = getPaymentAccounts();
        $paymentMethods = getPaymentMethodList();

        return view('frontend.pages.vendor.vendor-due', compact(
            'purchases',
            'vendors',
            'totalDueAmount',
            'totalBillAmount',
            'totalPaidAmount',
            'pendingBillsCount',
            'uniqueVendorsCount',
            'paymentAccounts',
            'paymentMethods',
            'request'
        ));
    }

    /**
     * Process a due payment to a vendor for a purchase bill.
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'purchase_id'    => 'required|exists:purchases,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'account_id'     => 'nullable|exists:chart_of_accounts,id',
            'payment_date'   => 'nullable|date',
            'notes'          => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::with('vendor', 'product')->findOrFail($request->purchase_id);
            $paymentAmount = (float) $request->payment_amount;

            if ($paymentAmount > (float) $purchase->due) {
                return redirect()->back()->with('error', 'Payment amount (৳' . number_format($paymentAmount, 2) . ') cannot exceed the outstanding due amount (৳' . number_format($purchase->due, 2) . ')!');
            }

            $dueBeforePayment = (float) $purchase->due;
            $newPaid = (float) $purchase->payment + $paymentAmount;
            $newDue = max(0, (float) $purchase->total_price - $newPaid);

            // Update purchase record
            $purchase->update([
                'payment'    => $newPaid,
                'due'        => $newDue,
                'updated_by' => Auth::id(),
            ]);

            // Create payment log record
            Payment::create([
                'payment_for'    => 1, // Purchase / Vendor
                'customer_id'    => 0,
                'sale_id'        => 0,
                'payment_method' => $request->payment_method,
                'amount'         => $paymentAmount,
                'remarks'        => $request->notes,
                'status'         => 1,
                'created_by'     => Auth::id() ?? 1,
                'updated_by'     => Auth::id() ?? 1,
            ]);

            // Auto-post double-entry journal voucher
            try {
                $sourceAccount = null;
                if (!empty($request->account_id)) {
                    $sourceAccount = ChartOfAccount::find($request->account_id);
                }
                if (!$sourceAccount) {
                    $sourceAccount = ChartOfAccount::where('account_code', '1110')->first();
                }

                $apAcc = ChartOfAccount::where('account_code', '2110')->first();

                if ($sourceAccount && $apAcc && $paymentAmount > 0) {
                    $accLabel = $sourceAccount->account_name . ' (' . $sourceAccount->account_code . ')';
                    $vendorName = $purchase->vendor ? $purchase->vendor->name : ('Vendor #' . $purchase->vendor_id);
                    $productName = $purchase->product ? $purchase->product->name : '';

                    postJournalEntry([
                        'entry_date'     => $request->payment_date ? date('Y-m-d', strtotime($request->payment_date)) : date('Y-m-d'),
                        'reference_type' => 'purchase',
                        'reference_id'   => $purchase->id,
                        'description'    => "Vendor due paid to {$vendorName} for Purchase #{$purchase->id}" . ($productName ? " ({$productName})" : '') . ($request->notes ? " [Ref: {$request->notes}]" : ''),
                        'items'          => [
                            [
                                'account_id'  => $apAcc->id,
                                'debit'       => $paymentAmount,
                                'credit'      => 0.00,
                                'description' => "Accounts Payable reduced for {$vendorName} (Purchase #{$purchase->id})",
                            ],
                            [
                                'account_id'  => $sourceAccount->id,
                                'debit'       => 0.00,
                                'credit'      => $paymentAmount,
                                'description' => "Funds disbursed via {$accLabel} to {$vendorName}",
                            ],
                        ],
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Vendor due payment journal posting notice: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Payment of ৳' . number_format($paymentAmount, 2) . ' to ' . ($purchase->vendor->name ?? 'vendor') . ' recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing vendor payment: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF summary of outstanding vendor dues.
     */
    public function downloadPdf(Request $request)
    {
        $query = Purchase::with(['vendor', 'product'])
            ->where('due', '>', 0);

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $purchases = $query->latest()->get();
        $totalDueAmount = $purchases->sum('due');
        $totalBillAmount = $purchases->sum('total_price');
        $totalPaidAmount = $purchases->sum('payment');
        $selectedVendor = $request->filled('vendor_id') ? Vendor::find($request->vendor_id) : null;

        $html = view('frontend.pages.vendor.vendor-due-pdf', compact(
            'purchases',
            'totalDueAmount',
            'totalBillAmount',
            'totalPaidAmount',
            'selectedVendor',
            'request'
        ))->render();

        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'default_font' => 'Helvetica',
            'margin_left'   => 12,
            'margin_right'  => 12,
            'margin_top'    => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('vendor-due-report-' . date('Y-m-d') . '.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
