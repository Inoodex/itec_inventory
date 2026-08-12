<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;

class InventoryService
{
    /**
     * Increment stock when a purchase is made.
     * Creates the inventory record if it doesn't exist.
     */
    public function incrementStock(int $productId, int $quantity, string $notes = 'Stock added via purchase'): Inventory
    {
        $inventory = Inventory::where('product_id', $productId)->first();

        if ($inventory) {
            $inventory->current_stock += $quantity;
            $inventory->save();
        } else {
            $inventory = Inventory::create([
                'product_id'    => $productId,
                'current_stock' => $quantity,
                'opening_stock' => $quantity,
                'notes'         => $notes,
            ]);
        }

        return $inventory;
    }

    /**
     * Decrement stock when a sale is made.
     * Throws an exception if stock is insufficient.
     */
    public function decrementStock(int $productId, int $quantity): Inventory
    {
        $product   = Product::findOrFail($productId);
        $inventory = Inventory::where('product_id', $productId)->first();

        if (!$inventory) {
            throw new \RuntimeException("Inventory not found for product: {$product->name}.");
        }

        if ($inventory->current_stock < $quantity) {
            throw new \RuntimeException(
                "Insufficient stock for product: {$product->name}. " .
                "Available: {$inventory->current_stock}, Requested: {$quantity}."
            );
        }

        $inventory->decrement('current_stock', $quantity);
        $freshInventory = $inventory->fresh();

        // Dispatch low-stock alert if stock drops to 5 or below
        if ($freshInventory->current_stock <= 5) {
            event(new \App\Events\LowStockAlertEvent($product, $freshInventory->current_stock));
        }

        return $freshInventory;
    }

    /**
     * Restore stock when a return is completed.
     * Creates inventory record if somehow missing.
     */
    public function restoreStock(int $productId, int $quantity): Inventory
    {
        $inventory = Inventory::where('product_id', $productId)->first();

        if ($inventory) {
            $inventory->increment('current_stock', $quantity);
        } else {
            $inventory = Inventory::create([
                'product_id'    => $productId,
                'current_stock' => $quantity,
                'opening_stock' => 0,
                'notes'         => 'Restored via product return',
            ]);
        }

        return $inventory->fresh();
    }

    /**
     * Get current stock level for a product.
     */
    public function getStock(int $productId): int
    {
        $inventory = Inventory::where('product_id', $productId)->first();
        return $inventory ? (int) $inventory->current_stock : 0;
    }

    /**
     * Handle serial number registration for serialized products on purchase.
     */
    public function registerSerials(int $productId, int $purchaseId, array $serials, int $limit): void
    {
        $serials = array_filter(array_map('trim', $serials));
        $serials = array_slice($serials, 0, $limit);

        foreach ($serials as $serial) {
            ProductSerial::firstOrCreate(
                ['serial_number' => $serial],
                [
                    'product_id'  => $productId,
                    'purchase_id' => $purchaseId,
                    'status'      => 'available',
                ]
            );
        }
    }

    /**
     * Parse bulk serial input (newline or comma separated) into an array.
     */
    public function parseBulkSerials(string $bulk): array
    {
        return array_filter(
            array_map('trim', preg_split('/[\n,]+/', $bulk))
        );
    }
}
