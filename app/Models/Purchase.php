<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Purchase extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    // Optional: define table name if not following conventions
    protected $table = 'purchases';

    // Mass assignable fields
    protected $fillable = [
        'product_id',
        'vendor_id',
        'quantity',
        'unit_price',
        'sub_price',
        'total_price',
        'payment',
        'due',
        'created_by',
        'updated_by',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
