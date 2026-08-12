<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'sale_id',
        'customer_id',
        'return_date',
        'total_refund_amount',
        'status',
        'reason',
        'notes',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'return_date' => 'date',
        'processed_at' => 'datetime',
        'total_refund_amount' => 'decimal:2'
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Calculate total refund amount from items
    public function calculateTotalRefund()
    {
        return $this->items->sum('total_price');
    }

    // Approve return and update stock
    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'processed_by' => $userId,
            'processed_at' => now()
        ]);
    }

    // Complete return, update stock, adjust sale amounts, create refund payment
    public function complete($userId)
    {
        $this->load(['sale', 'items.salesItem']);

        $this->update([
            'status' => 'completed',
            'processed_by' => $userId,
            'processed_at' => now(),
            'total_refund_amount' => $this->calculateTotalRefund()
        ]);

        // Update stock for each item
        foreach ($this->items as $item) {
            $this->addToStock($item);
        }

        // Update sale amounts
        $this->updateSaleAmounts();

        // Create refund payment record
        $this->createRefundPayment($userId);

        // Update sales_items returned_qty
        $this->updateSalesItemsReturnedQty();
    }

    // Update sale payable and total amounts
    private function updateSaleAmounts()
    {
        $sale = $this->sale;
        $refundAmount = $this->total_refund_amount;

        $newPayble = $sale->payble - $refundAmount;
        $newTotal = $sale->total - $refundAmount;
        $newDuePayment = max(0, $sale->payble - $sale->advanced_payment - $refundAmount);

        $sale->update([
            'payble' => max(0, $newPayble),
            'total' => max(0, $newTotal),
            'due_payment' => $newDuePayment,
        ]);
    }

    // Create refund payment record
    private function createRefundPayment($userId)
    {
        Payment::create([
            'sale_id' => $this->sale_id,
            'customer_id' => $this->customer_id,
            'payment_for' => 3, // 3 = refund
            'payment_method' => 'cash',
            'amount' => -$this->total_refund_amount, // Negative amount for refund
            'remarks' => "Refund for Return #{$this->id}",
            'status' => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    // Update returned_qty for each sales_item
    private function updateSalesItemsReturnedQty()
    {
        foreach ($this->items as $returnItem) {
            if ($returnItem->sales_item_id) {
                $salesItem = SalesItem::find($returnItem->sales_item_id);
                if ($salesItem) {
                    $salesItem->increment('returned_qty', $returnItem->quantity);
                }
            }
        }
    }

    // Add returned item back to stock
    private function addToStock($item)
    {
        $inventory = \App\Models\Inventory::where('product_id', $item->product_id)->first();

        if ($inventory) {
            $inventory->increment('current_stock', $item->quantity);
        } else {
            \App\Models\Inventory::create([
                'product_id' => $item->product_id,
                'opening_stock' => 0,
                'current_stock' => $item->quantity
            ]);
        }

        // If specific serial numbers are linked to the sale item, restore them
        if ($item->sales_item_id) {
            $newStatus = ($item->condition === 'good') ? 'available' : 'damaged';
            \App\Models\ProductSerial::where('sales_item_id', $item->sales_item_id)
                ->where('status', 'sold')
                ->limit($item->quantity)
                ->update([
                    'status' => $newStatus,
                    'sales_item_id' => null, // detach from sold sale item
                ]);
        }
    }

    // Reject return
    public function reject($userId, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $userId,
            'processed_at' => now(),
            'notes' => $reason ?? $this->notes
        ]);
    }
}
