<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

      protected $casts = [
        'photos' => 'array',
    ];

    protected $fillable = [
        'name',
        'category_id',
        'brand_id',
        'model',
        'barcode',
        'photos',
        'status',
        'warranty',
        'is_serialized',
    ];

    public function serials()
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function availableSerials()
    {
        return $this->hasMany(ProductSerial::class)->where('status', 'available');
    }

    /**
     * Generate unique SKU/Barcode for products without a vendor barcode
     */
    public static function generateBarcode(): string
    {
        do {
            $code = 'ITP-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('barcode', $code)->exists());

        return $code;
    }
    public function latestPurchase()
    {
        return $this->hasOne(Purchase::class)->latestOfMany();
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
{
    return $this->hasOne(Inventory::class);
}

public function purchases()
{
    return $this->hasMany(Purchase::class);
}
}