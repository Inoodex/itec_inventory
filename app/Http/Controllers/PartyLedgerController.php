<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartyLedgerController extends Controller
{
    /**
     * Display the combined Customer & Vendor ledger with net balance.
     */
    public function show(Request $request, string $type, int $id)
    {
        $customer = null;
        $vendor = null;

        if ($type === 'customer') {
            $customer = Customer::findOrFail($id);
            // Find linked vendor by phone or name, or request override
            if ($request->filled('vendor_id')) {
                $vendor = Vendor::find($request->vendor_id);
            } else {
                $vendor = Vendor::where('phone', $customer->phone)
                    ->orWhere('name', $customer->name)
                    ->first();
            }
        } elseif ($type === 'vendor') {
            $vendor = Vendor::findOrFail($id);
            // Find linked customer by phone or name, or request override
            if ($request->filled('customer_id')) {
                $customer = Customer::find($request->customer_id);
            } else {
                $customer = Customer::where('phone', $vendor->phone)
                    ->orWhere('name', $vendor->name)
                    ->first();
            }
        }

        // All vendors and customers for manual link dropdown if needed
        $allVendors = Vendor::orderBy('name')->get();
        $allCustomers = Customer::orderBy('name')->get();

        // 1. Customer Transactions (Receivable side)
        $customerSales = collect();
        $customerPayments = collect();
        $customerDue = 0;
        $customerTotalSales = 0;
        $customerTotalPaid = 0;

        if ($customer) {
            $customerSales = Sale::where('customer_id', $customer->id)
                ->where('sale_type', 'retail')
                ->latest()
                ->get();

            $customerDue = (float) $customerSales->sum('due_payment');
            $customerTotalSales = (float) $customerSales->sum('payble');
            $customerTotalPaid = (float) $customerSales->sum('advanced_payment');

            $customerPayments = Payment::where('customer_id', $customer->id)
                ->where('payment_for', 2)
                ->latest()
                ->get();
        }

        // 2. Vendor Transactions (Payable side)
        $vendorPurchases = collect();
        $vendorPayments = collect();
        $vendorDue = 0;
        $vendorTotalPurchases = 0;
        $vendorTotalPaid = 0;

        if ($vendor) {
            $vendorPurchases = Purchase::with('product')
                ->where('vendor_id', $vendor->id)
                ->latest()
                ->get();

            $vendorDue = (float) $vendorPurchases->sum('due');
            $vendorTotalPurchases = (float) $vendorPurchases->sum('total_price');
            $vendorTotalPaid = (float) $vendorPurchases->sum('payment');
        }

        // 3. Net Balance Calculation
        // Net Balance = Customer Due (they owe us) - Vendor Due (we owe them)
        // > 0: Net Receivable (they owe us)
        // < 0: Net Payable (we owe them)
        // = 0: Balanced
        $netBalance = $customerDue - $vendorDue;

        // 4. Combined Chronological Ledger
        $ledgerEntries = collect();

        // Add Customer Sales (Debit - increases what they owe us)
        foreach ($customerSales as $sale) {
            $ledgerEntries->push((object)[
                'date'           => $sale->created_at,
                'party_role'     => 'Customer',
                'type'           => 'Sale Invoice',
                'ref_no'         => $sale->order_no,
                'link'           => route('sales.invoice.pdf', $sale->id),
                'description'    => "Retail Sale Order #{$sale->order_no}",
                'amount'         => (float) $sale->payble,
                'debit'          => (float) $sale->payble,
                'credit'         => 0.00,
                'customer_due'   => (float) $sale->due_payment,
                'vendor_due'     => 0.00,
                'status'         => $sale->due_payment <= 0 ? 'Paid' : ($sale->advanced_payment > 0 ? 'Partial' : 'Unpaid'),
            ]);
        }

        // Add Customer Payments (Credit - reduces what they owe us)
        foreach ($customerPayments as $pay) {
            $ledgerEntries->push((object)[
                'date'           => Carbon::parse($pay->payment_date ?? $pay->created_at),
                'party_role'     => 'Customer',
                'type'           => 'Customer Receipt',
                'ref_no'         => 'PAY-' . $pay->id,
                'link'           => null,
                'description'    => "Customer payment received via " . ucfirst($pay->payment_method) . ($pay->notes ? " [{$pay->notes}]" : ''),
                'amount'         => (float) $pay->amount,
                'debit'          => 0.00,
                'credit'         => (float) $pay->amount,
                'customer_due'   => 0.00,
                'vendor_due'     => 0.00,
                'status'         => 'Completed',
            ]);
        }

        // Add Vendor Purchases (Credit - increases what we owe them)
        foreach ($vendorPurchases as $purchase) {
            $prodName = $purchase->product->name ?? 'Purchase item';
            $ledgerEntries->push((object)[
                'date'           => $purchase->created_at,
                'party_role'     => 'Vendor',
                'type'           => 'Purchase Bill',
                'ref_no'         => 'PUR-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT),
                'link'           => null,
                'description'    => "Procurement: {$prodName} (Qty: {$purchase->quantity})",
                'amount'         => (float) $purchase->total_price,
                'debit'          => 0.00,
                'credit'         => (float) $purchase->total_price,
                'customer_due'   => 0.00,
                'vendor_due'     => (float) $purchase->due,
                'status'         => $purchase->due <= 0 ? 'Paid' : ($purchase->payment > 0 ? 'Partial' : 'Unpaid'),
            ]);
        }

        // Sort unified ledger chronologically (oldest to newest)
        $ledgerEntries = $ledgerEntries->sortBy('date')->values();

        // Max contra settlable amount
        $maxContraSettle = min($customerDue, $vendorDue);

        return view('frontend.pages.party-ledger.show', compact(
            'customer',
            'vendor',
            'type',
            'customerDue',
            'customerTotalSales',
            'customerTotalPaid',
            'vendorDue',
            'vendorTotalPurchases',
            'vendorTotalPaid',
            'netBalance',
            'ledgerEntries',
            'maxContraSettle',
            'allVendors',
            'allCustomers'
        ));
    }

    /**
     * Settle contra balance between customer due and vendor due (Netting offset).
     */
    public function contraSettle(Request $request)
    {
        $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'vendor_id'         => 'required|exists:vendors,id',
            'settlement_amount' => 'required|numeric|min:0.01',
            'settlement_date'   => 'nullable|date',
            'notes'             => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $customer = Customer::findOrFail($request->customer_id);
            $vendor = Vendor::findOrFail($request->vendor_id);
            $settleAmount = (float) $request->settlement_amount;
            $settleDate = $request->settlement_date ?: now()->toDateString();

            // Calculate current dues
            $unpaidSales = Sale::where('customer_id', $customer->id)
                ->where('sale_type', 'retail')
                ->where('due_payment', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            $unpaidPurchases = Purchase::where('vendor_id', $vendor->id)
                ->where('due', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            $custTotalDue = (float) $unpaidSales->sum('due_payment');
            $vendTotalDue = (float) $unpaidPurchases->sum('due');
            $maxAllowed = min($custTotalDue, $vendTotalDue);

            if ($settleAmount > ($maxAllowed + 0.01)) {
                return redirect()->back()->with('error', 'Contra settlement amount (৳' . number_format($settleAmount, 2) . ') cannot exceed the maximum common offset amount of ৳' . number_format($maxAllowed, 2) . '!');
            }

            // 1. Apply FIFO against Customer Unpaid Sales
            $remCust = $settleAmount;
            foreach ($unpaidSales as $sale) {
                if ($remCust <= 0.0001) break;
                $allocated = min($remCust, (float) $sale->due_payment);
                $newAdv = (float) $sale->advanced_payment + $allocated;
                $newDue = max(0, (float) $sale->payble - $newAdv);

                $sale->update([
                    'advanced_payment' => $newAdv,
                    'due_payment'      => $newDue,
                    'payment_status'   => 1,
                ]);

                Payment::create([
                    'customer_id'        => $customer->id,
                    'sale_id'            => $sale->id,
                    'amount'             => $allocated,
                    'due_before_payment' => (float) $sale->due_payment,
                    'due_after_payment'  => $newDue,
                    'payment_method'     => 'bank_transfer',
                    'payment_date'       => $settleDate,
                    'notes'              => 'Contra Netting Offset with Vendor #' . $vendor->id . ' (' . $vendor->name . ')' . ($request->notes ? ' - ' . $request->notes : ''),
                    'payment_for'        => 2,
                ]);

                $remCust -= $allocated;
            }

            // 2. Apply FIFO against Vendor Unpaid Purchases
            $remVend = $settleAmount;
            foreach ($unpaidPurchases as $purchase) {
                if ($remVend <= 0.0001) break;
                $allocated = min($remVend, (float) $purchase->due);
                $newPaid = (float) $purchase->payment + $allocated;
                $newDue = max(0, (float) $purchase->total_price - $newPaid);

                $purchase->update([
                    'payment'    => $newPaid,
                    'due'        => $newDue,
                    'updated_by' => Auth::id(),
                ]);

                Payment::create([
                    'payment_for'    => 1,
                    'customer_id'    => 0,
                    'sale_id'        => 0,
                    'payment_method' => 'bank_transfer',
                    'amount'         => $allocated,
                    'remarks'        => 'Contra Netting Offset with Customer #' . $customer->id . ' (' . $customer->name . ')' . ($request->notes ? ' - ' . $request->notes : ''),
                    'status'         => 1,
                    'created_by'     => Auth::id() ?? 1,
                    'updated_by'     => Auth::id() ?? 1,
                ]);

                $remVend -= $allocated;
            }

            // 3. Post Double-Entry Journal Voucher:
            // Debit: Accounts Payable (2110) - reducing our liability to vendor
            // Credit: Accounts Receivable (1130) - reducing customer's debt to us
            try {
                $arAcc = ChartOfAccount::where('account_code', '1130')->first();
                $apAcc = ChartOfAccount::where('account_code', '2110')->first();

                if ($arAcc && $apAcc && $settleAmount > 0) {
                    postJournalEntry([
                        'entry_date'     => date('Y-m-d', strtotime($settleDate)),
                        'reference_type' => 'contra_settlement',
                        'reference_id'   => $customer->id,
                        'description'    => "Contra Netting Offset between Customer [{$customer->name}] and Vendor [{$vendor->name}]" . ($request->notes ? " [{$request->notes}]" : ''),
                        'items'          => [
                            [
                                'account_id'  => $apAcc->id,
                                'debit'       => $settleAmount,
                                'credit'      => 0.00,
                                'description' => "Contra offset: Accounts Payable reduced for {$vendor->name}",
                            ],
                            [
                                'account_id'  => $arAcc->id,
                                'debit'       => 0.00,
                                'credit'      => $settleAmount,
                                'description' => "Contra offset: Accounts Receivable reduced for {$customer->name}",
                            ],
                        ],
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Contra settlement journal notice: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Contra netting offset of ৳' . number_format($settleAmount, 2) . ' applied successfully! Both customer receivable and vendor payable have been adjusted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error applying contra settlement: ' . $e->getMessage());
        }
    }
}
