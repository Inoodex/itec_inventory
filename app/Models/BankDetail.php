<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'bank_name',
        'branch',
        'account_number',
        'account_type',
        'routing_number',
        'is_default',
        'is_active'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Relationship with bills
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    // Scope for active bank details
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for default bank detail
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}