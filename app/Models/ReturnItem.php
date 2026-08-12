<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'product_id',
        'sales_item_id',
        'quantity',
        'unit_price',
        'total_price',
        'return_reason',
        'condition',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    // Relationships
    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function salesItem()
    {
        return $this->belongsTo(SalesItem::class, 'sales_item_id');
    }

    // Calculate total before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });
    }

    // Return reason labels
    public static function reasonLabels()
    {
        return [
            'damaged' => 'Damaged',
            'wrong_item' => 'Wrong Item',
            'customer_changed_mind' => 'Customer Changed Mind',
            'defective' => 'Defective',
            'expired' => 'Expired',
            'other' => 'Other'
        ];
    }

    // Condition labels
    public static function conditionLabels()
    {
        return [
            'good' => 'Good',
            'damaged' => 'Damaged',
            'defective' => 'Defective'
        ];
    }

    public function getReasonLabelAttribute()
    {
        return self::reasonLabels()[$this->return_reason] ?? $this->return_reason;
    }

    public function getConditionLabelAttribute()
    {
        return self::conditionLabels()[$this->condition] ?? $this->condition;
    }
}
