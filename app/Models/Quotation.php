<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number',
        'client_id',
        'client_name',
        'client_designation',
        'client_address',
        'client_phone',
        'client_email',
        'attention_to',
        'body_content',
        'terms_conditions',
        'subject',
        'company_name',
        'signatory_name',
        'signatory_designation',
        'company_phone',
        'company_email',
        'company_website',
        'additional_enclosed',
        'quotation_date',
        'expiry_date',
        'notes',
        'sub_total',
        'discount_amount',
        'total_amount',
        'status',
        'show_signature',
        'show_seal',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'sub_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'show_signature' => 'boolean',
        'show_seal' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            $quotation->quotation_number = static::generateQuotationNumber();
        });
    }

    // public static function generateQuotationNumber()
    // {
    //     $prefix = 'QT';
    //     $year = date('Y');
    //     $month = date('m');
        
    //     $lastQuotation = static::where('quotation_number', 'like', "{$prefix}{$year}{$month}%")
    //         ->orderBy('id', 'desc')
    //         ->first();

    //     $sequence = $lastQuotation ? (int)substr($lastQuotation->quotation_number, -4) + 1 : 1;

    //     return "{$prefix}{$year}{$month}" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    // }

    public static function generateQuotationNumber()
{
    $prefix = 'QT';
    $date = date('Ymd'); // 20251126
    
    // Search last quotation for today
    $lastQuotation = static::where('quotation_number', 'like', "{$prefix}-{$date}-%")
        ->orderBy('id', 'desc')
        ->first();

    // Extract last 4 digits sequence
    $sequence = $lastQuotation 
        ? (int)substr($lastQuotation->quotation_number, -4) + 1 
        : 1;

    // Format: QT-20251126-0001
    return "{$prefix}-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}

}