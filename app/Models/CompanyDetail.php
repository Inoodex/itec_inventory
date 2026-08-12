<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'signatory_name',
        'signatory_designation',
        'signature_image',
        'seal_image',
        'phone',
        'email',
        'website',
        'address',
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

    // Scope for active company details
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for default company detail
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}