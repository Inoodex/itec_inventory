<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'country_code',
        'phone',
        'email',
        'address',
        'product_id',
        'product_name',
        'product_number',
        'details',
        'total',
        'bill',
        'discount',
        'paid_amount',
        'due_amount',
        'remarks',
        'warranty_duration',
        'repaired_by',
        'status',
        'complated_date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusTextAttribute()
    {
        // status 0 = pending, 1 = completed
        if ((string) $this->status === '1') {
            return 'Completed';
        }

        return 'Pending';
    }

    public function getStatusBadgeAttribute()
    {
        $text = $this->status_text;
        $class = (string) $this->status === '1' ? 'success' : 'warning';

        return "<span class='badge bg-{$class}'>{$text}</span>";
    }
}
