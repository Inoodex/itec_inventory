<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'product_id',
        'unit_price',
        'qty',
        'total_price',
        'warranty',
        'returned_qty',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'order_id');
    }
}
