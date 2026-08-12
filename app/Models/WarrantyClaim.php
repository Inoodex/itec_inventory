<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class WarrantyClaim extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'claim_no',
        'sale_id',
        'sales_item_id',
        'product_id',
        'customer_id',
        'serial_number',
        'claim_date',
        'warranty_expiry_date',
        'problem_description',
        'condition_notes',
        'status',
        'action_taken',
        'replacement_serial_number',
        'received_by',
        'resolved_at',
        'remarks',
    ];

    protected $casts = [
        'claim_date'           => 'date',
        'warranty_expiry_date' => 'date',
        'resolved_at'          => 'datetime',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function salesItem(): BelongsTo
    {
        return $this->belongsTo(SalesItem::class, 'sales_item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WarrantyClaimLog::class, 'warranty_claim_id')->latest();
    }

    /**
     * Check if claim's warranty was valid at time of claim.
     */
    public function getIsValidWarrantyAttribute(): bool
    {
        return $this->claim_date->lte($this->warranty_expiry_date);
    }
}
