<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'product_id',
        'unit_price',
        'quantity',
        'total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationship with Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Calculate total automatically
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total = $item->unit_price * $item->quantity;
        });
    }
}