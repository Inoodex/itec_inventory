<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(private InventoryService $inventoryService) {}

    /**
     * Create a purchase, register serials, and update inventory — all in one transaction.
     */
    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            // 1. Create the purchase record
            $purchase = Purchase::create([
                'product_id'  => $data['product_id'],
                'vendor_id'   => $data['vendor_id'],
                'quantity'    => $data['quantity'],
                'unit_price'  => $data['unit_price'],
                'sub_price'   => $data['sub_price'] ?? ($data['quantity'] * $data['unit_price']),
                'total_price' => $data['total_price'],
                'payment'     => $data['payment'],
                'due'         => $data['due'],
                'created_by'  => Auth::id(),
            ]);

            // 2. Register serial numbers if product is serialized
            $product = Product::find($data['product_id']);
            if ($product && $product->is_serialized) {
                $serials = $this->collectSerials($data);
                $this->inventoryService->registerSerials(
                    $product->id,
                    $purchase->id,
                    $serials,
                    $data['quantity']
                );
            }

            // 3. Increment inventory stock
            $this->inventoryService->incrementStock(
                $data['product_id'],
                $data['quantity'],
                'Stock added via purchase #' . $purchase->id
            );

            // 4. Auto-post double-entry journal voucher for Purchase
            try {
                $invAcc = \App\Models\ChartOfAccount::where('account_code', '1140')->first();
                $apAcc = \App\Models\ChartOfAccount::where('account_code', '2110')->first();
                $paid = (float) $purchase->payment;
                $due = (float) $purchase->due;
                $total = (float) $purchase->total_price;
                $paymentRef = $data['payment_ref'] ?? null;

                // Resolve Source Payment Account (chosen account or default Cash in Hand 1110)
                $sourceAccount = null;
                if (!empty($data['account_id'])) {
                    $sourceAccount = \App\Models\ChartOfAccount::find($data['account_id']);
                }
                if (!$sourceAccount) {
                    $sourceAccount = \App\Models\ChartOfAccount::where('account_code', '1110')->first();
                }

                if ($invAcc && ($sourceAccount || $apAcc)) {
                    $items = [];

                    $items[] = [
                        'account_id' => $invAcc->id,
                        'debit' => $total,
                        'credit' => 0.00,
                        'description' => 'Inventory procurement item #' . $purchase->product_id . ' (Qty: ' . $purchase->quantity . ')'
                    ];

                    if ($paid > 0 && $sourceAccount) {
                        $accLabel = $sourceAccount->account_name . ' (' . $sourceAccount->account_code . ')';
                        $items[] = [
                            'account_id' => $sourceAccount->id,
                            'debit' => 0.00,
                            'credit' => $paid,
                            'description' => "Payment disbursed via {$accLabel} to vendor #" . $purchase->vendor_id . ($paymentRef ? " [Ref: {$paymentRef}]" : '')
                        ];
                    }

                    if ($due > 0 && $apAcc) {
                        $items[] = [
                            'account_id' => $apAcc->id,
                            'debit' => 0.00,
                            'credit' => $due,
                            'description' => 'Accounts payable due to vendor #' . $purchase->vendor_id
                        ];
                    }

                    postJournalEntry([
                        'entry_date' => date('Y-m-d'),
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'description' => 'Procurement #' . $purchase->id . ' — Product #' . $purchase->product_id . ' (Qty: ' . $purchase->quantity . ')' . ($paymentRef ? " (Ref: {$paymentRef})" : ''),
                        'items' => $items
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Purchase auto-journal posting notice: ' . $e->getMessage());
            }

            return $purchase;
        });
    }

    /**
     * Collect all serial numbers from both individual inputs and bulk textarea.
     */
    private function collectSerials(array $data): array
    {
        $serials = [];

        if (!empty($data['serial_numbers'])) {
            $serials = array_merge($serials, (array) $data['serial_numbers']);
        }

        if (!empty($data['serial_bulk'])) {
            $bulk    = $this->inventoryService->parseBulkSerials($data['serial_bulk']);
            $serials = array_merge($serials, $bulk);
        }

        return $serials;
    }
}
