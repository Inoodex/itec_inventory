<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'purchase_id',
        'sales_item_id',
        'serial_number',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function salesItem()
    {
        return $this->belongsTo(SalesItem::class, 'sales_item_id');
    }
}
