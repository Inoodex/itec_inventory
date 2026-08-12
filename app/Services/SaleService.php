<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(private InventoryService $inventoryService) {}

    /**
     * Create a full sale — customer, sale record, items, inventory deduction.
     * Wrapped in a DB transaction by the caller or internally.
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {

            // 1. Resolve customer (new or existing)
            $customer = $this->resolveCustomer($data);

            // 2. Calculate financials
            $financials = $this->calculateFinancials($data);

            // 3. Generate invoice number
            $invoiceNumber = 'INV-' . strtoupper(uniqid());

            // 4. Create Sale record
            $sale = Sale::create([
                'order_no'         => $invoiceNumber,
                'customer_id'      => $customer->id,
                'product_id'       => $data['product'][0],
                'qty'              => array_sum($data['qty']),
                'total'            => $financials['total'],
                'payble'           => $financials['payble'],
                'bill'             => $financials['total'],
                'discount'         => $financials['discount'],
                'advanced_payment' => $financials['advanced_payment'],
                'due_payment'      => $financials['due_payment'],
                'sales_by'         => Auth::id(),
                'status'           => $financials['status'],
                'vat'              => $data['vat'] ?? 0,
                'tax'              => $data['tax'] ?? 0,
                'delivery_charge'  => $data['delivery_charge'] ?? 0,
            ]);

            // 5. Create line items and deduct inventory
            $this->createSaleItems($sale, $data);

            // 6. Auto-post double-entry journal voucher for Sale
            try {
                $arAcc = \App\Models\ChartOfAccount::where('account_code', '1130')->first();
                $cashAcc = \App\Models\ChartOfAccount::where('account_code', '1110')->first();
                $revAcc = \App\Models\ChartOfAccount::where('account_code', '4110')->first();

                if ($arAcc && $revAcc) {
                    $items = [];
                    $grandTotal = (float) $sale->payble;
                    $paid = (float) $sale->advanced_payment;
                    $due = (float) $sale->due_payment;

                    if ($paid > 0 && $cashAcc) {
                        $items[] = ['account_id' => $cashAcc->id, 'debit' => $paid, 'credit' => 0.00, 'description' => 'Cash/Bank collected for Sale ' . $sale->order_no];
                    }
                    if ($due > 0) {
                        $items[] = ['account_id' => $arAcc->id, 'debit' => $due, 'credit' => 0.00, 'description' => 'Receivable due for Sale ' . $sale->order_no];
                    }
                    $items[] = ['account_id' => $revAcc->id, 'debit' => 0.00, 'credit' => $grandTotal, 'description' => 'Sales revenue recognized'];

                    postJournalEntry([
                        'entry_date' => date('Y-m-d'),
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'description' => 'Invoice ' . $sale->order_no . ' — Customer #' . $sale->customer_id,
                        'items' => $items
                    ]);
                }
            } catch (\Throwable $e) {}

            // 7. Broadcast real-time Pusher event
            event(new \App\Events\SaleCreatedEvent($sale));

            return $sale;
        });
    }

    /**
     * Resolve or create a customer based on client_type.
     */
    private function resolveCustomer(array $data): Customer
    {
        if ($data['client_type'] === 'new') {
            return Customer::create([
                'name'    => $data['name'],
                'phone'   => $data['phone'],
                'address' => $data['address'] ?? null,
            ]);
        }

        return Customer::findOrFail($data['existing_client_id']);
    }

    /**
     * Calculate discount, payable, due, and payment status.
     */
    public function calculateFinancials(array $data): array
    {
        $total    = $data['subTotal'];
        $discount = min($data['discount'] ?? 0, $total);
        $payble   = $data['grandTotal'];

        $advancedPayment = $data['advanced_payment'] ?? 0;
        if ($advancedPayment > $payble) {
            $advancedPayment = $payble;
        }

        $duePayment = $data['duePayment'] ?? ($payble - $advancedPayment);

        $status = match(true) {
            $duePayment <= 0          => 'paid',
            $advancedPayment > 0      => 'partial',
            default                   => 'credit',
        };

        return compact('total', 'discount', 'payble', 'advancedPayment', 'duePayment', 'status')
            + ['advanced_payment' => $advancedPayment, 'due_payment' => $duePayment];
    }

    /**
     * Create SalesItem records and deduct inventory for each product.
     */
    private function createSaleItems(Sale $sale, array $data): void
    {
        $warranties = Product::whereIn('id', $data['product'])->pluck('warranty', 'id');

        foreach ($data['product'] as $index => $productId) {
            $qty       = $data['qty'][$index];
            $unitPrice = $data['unit_price'][$index];
            $total     = $unitPrice * $qty;

            // Profit calculation
            $product       = Product::with('latestPurchase')->find($productId);
            $purchasePrice = $product?->latestPurchase?->unit_price ?? 0;
            $itemProfit    = ($unitPrice - $purchasePrice) * $qty;

            $salesItem = SalesItem::create([
                'order_id'       => $sale->id,
                'product_id'     => $productId,
                'unit_price'     => $unitPrice,
                'qty'            => $qty,
                'total_price'    => $total,
                'warranty'       => $warranties[$productId] ?? 0,
                'purchase_price' => $purchasePrice,
                'profit'         => $itemProfit,
            ]);

            // Link sold serial numbers if provided
            if (!empty($data['item_serials'][$productId])) {
                $serialsToMark = (array) $data['item_serials'][$productId];
                \App\Models\ProductSerial::where('product_id', $productId)
                    ->whereIn('serial_number', $serialsToMark)
                    ->where('status', 'available')
                    ->update([
                        'status' => 'sold',
                        'sales_item_id' => $salesItem->id,
                    ]);
            }

            // Deduct stock — throws RuntimeException if insufficient
            $this->inventoryService->decrementStock($productId, $qty);
        }
    }

    /**
     * Record a payment against a sale.
     */
    public function recordPayment(Sale $sale, float $amount, string $method = 'cash', ?int $userId = null): Payment
    {
        $payment = Payment::create([
            'sale_id'        => $sale->id,
            'customer_id'    => $sale->customer_id,
            'payment_for'    => 1,
            'payment_method' => $method,
            'amount'         => $amount,
            'status'         => 1,
            'created_by'     => $userId ?? Auth::id(),
            'updated_by'     => $userId ?? Auth::id(),
        ]);

        // Update due payment on sale
        $newDue = max(0, $sale->due_payment - $amount);
        $sale->update([
            'due_payment'      => $newDue,
            'advanced_payment' => $sale->advanced_payment + $amount,
            'status'           => $newDue <= 0 ? 'paid' : 'partial',
        ]);

        return $payment;
    }
}
