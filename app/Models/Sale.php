<?php

namespace App\Models;

use App\Models\SalesItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Sale extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'order_no',
        'sale_type',
        'customer_id',
        'client_id',
        'project_id',
        'product_id',
        'price',
        'qty',
        'total',
        'payble',
        'bill',
        'advanced_payment',
        'due_payment',
        'discount',
        'sales_by',
        'status',
        'vat', 
        'tax',
        'delivery_charge',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($sale) {
    //         $sale->due_payment = $sale->payble - $sale->advanced_payment;

    //         // Auto-update payment_status
    //         if ($sale->advanced_payment == 0) {
    //             $sale->payment_status = 'pending';
    //         } elseif ($sale->advanced_payment > 0 && $sale->advanced_payment < $sale->payble) {
    //             $sale->payment_status = 'partial';
    //         } elseif ($sale->advanced_payment >= $sale->payble) {
    //             $sale->payment_status = 'paid';
    //         }
    //     });
    // }
    public function items()
    {
        return $this->hasMany(SalesItem::class, 'order_id');
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function client()
{
    return $this->belongsTo(Client::class, 'client_id');
}

public function salesBy()
{
    return $this->belongsTo(User::class, 'sales_by'); 
}

    /**
     * Relationship with Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relationship with User (sales_by)
     */
    public function salesPerson()
    {
        return $this->belongsTo(User::class, 'sales_by');
    }
}