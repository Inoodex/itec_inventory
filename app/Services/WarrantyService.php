<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\WarrantyClaim;
use App\Models\WarrantyClaimLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarrantyService
{
    /**
     * Generate unique Warranty Claim Number (e.g., WC-20260730-0001).
     */
    public function generateClaimNumber(): string
    {
        $prefix = 'WC-' . date('Ymd') . '-';
        $latest = WarrantyClaim::withTrashed()
            ->where('claim_no', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $latest ? ((int) substr($latest->claim_no, -4)) + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Search sales items eligible for warranty lookup.
     */
    public function searchEligibleSaleItems(string $search): Collection
    {
        $search = trim($search);

        // Find sale items by serial number, order_no, customer, or product barcode
        $serialMatches = \App\Models\ProductSerial::where('serial_number', $search)
            ->whereNotNull('sales_item_id')
            ->pluck('sales_item_id');

        return SalesItem::with(['sale.customer', 'product'])
            ->where(function ($q) use ($search, $serialMatches) {
                if ($serialMatches->isNotEmpty()) {
                    $q->whereIn('id', $serialMatches);
                } else {
                    $q->whereHas('sale', function ($query) use ($search) {
                        $query->where('order_no', 'LIKE', "%{$search}%")
                            ->orWhereHas('customer', function ($cq) use ($search) {
                                $cq->where('phone', 'LIKE', "%{$search}%")
                                  ->orWhere('name', 'LIKE', "%{$search}%");
                            });
                    })
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('barcode', $search);
                    })
                    ->orWhere('id', $search);
                }
            })
            ->latest()
            ->get()
            ->map(function ($item) use ($search) {
                $saleDate = Carbon::parse($item->sale?->created_at ?? now());
                $warrantyDays = (int) ($item->warranty ?? 0);
                $expiryDate = $saleDate->copy()->addDays($warrantyDays);
                $daysRemaining = now()->diffInDays($expiryDate, false);

                $item->warranty_start_date = $saleDate->toDateString();
                $item->warranty_expiry_date = $expiryDate->toDateString();
                $item->warranty_days_remaining = (int) ceil($daysRemaining);
                $item->is_expired = $expiryDate->isPast();

                // If searched by serial, attach matched serial
                if (\App\Models\ProductSerial::where('serial_number', $search)->where('sales_item_id', $item->id)->exists()) {
                    $item->matched_serial = $search;
                }

                return $item;
            });
    }

    /**
     * Create a new Warranty Claim record.
     */
    public function createClaim(array $data): WarrantyClaim
    {
        return DB::transaction(function () use ($data) {
            $salesItem = SalesItem::with('sale')->findOrFail($data['sales_item_id']);
            $saleDate  = Carbon::parse($salesItem->sale?->created_at ?? now());
            $warrantyDays = (int) ($salesItem->warranty ?? 0);
            $expiryDate = $saleDate->copy()->addDays($warrantyDays);

            if ($expiryDate->isPast()) {
                throw new \Exception("Warranty for this item expired on " . $expiryDate->format('d M Y') . ". Cannot claim warranty.");
            }

            $claim = WarrantyClaim::create([
                'claim_no'            => $this->generateClaimNumber(),
                'sale_id'             => $salesItem->order_id,
                'sales_item_id'       => $salesItem->id,
                'product_id'          => $salesItem->product_id,
                'customer_id'         => $salesItem->sale?->customer_id,
                'serial_number'       => $data['serial_number'] ?? null,
                'claim_date'          => $data['claim_date'] ?? now()->toDateString(),
                'warranty_expiry_date'=> $expiryDate->toDateString(),
                'problem_description' => $data['problem_description'],
                'condition_notes'     => $data['condition_notes'] ?? null,
                'status'              => 'pending',
                'received_by'         => Auth::id(),
                'remarks'             => $data['remarks'] ?? null,
            ]);

            // Log initial status creation
            WarrantyClaimLog::create([
                'warranty_claim_id' => $claim->id,
                'user_id'           => Auth::id(),
                'status'            => 'pending',
                'note'              => 'Warranty claim registered.',
            ]);

            return $claim;
        });
    }

    /**
     * Update existing Warranty Claim status & action taken.
     */
    public function updateClaimStatus(WarrantyClaim $claim, array $data): WarrantyClaim
    {
        return DB::transaction(function () use ($claim, $data) {
            $newStatus = $data['status'];
            $isResolved = in_array($newStatus, ['repaired', 'replaced', 'completed', 'rejected']);

            $claim->update([
                'status'                    => $newStatus,
                'action_taken'              => $data['action_taken'] ?? $claim->action_taken,
                'replacement_serial_number' => $data['replacement_serial_number'] ?? $claim->replacement_serial_number,
                'remarks'                   => $data['remarks'] ?? $claim->remarks,
                'resolved_at'               => $isResolved ? ($claim->resolved_at ?? now()) : null,
            ]);

            // Log status transition
            WarrantyClaimLog::create([
                'warranty_claim_id' => $claim->id,
                'user_id'           => Auth::id(),
                'status'            => $newStatus,
                'note'              => $data['note'] ?? "Status updated to " . ucfirst(str_replace('_', ' ', $newStatus)),
            ]);

            return $claim->fresh();
        });
    }
}
