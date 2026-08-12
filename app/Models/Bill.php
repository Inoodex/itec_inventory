<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Bill extends Model
{
    protected $fillable = [
        'bill_number', 'reference_number', 'sale_id', 'customer_id', 'client_id', 'vendor_id', 
        'project_id', 'purchase_id', 'work_order_number', 'bill_date',
        'subtotal', 'total_amount', 'amount_in_words', 'status', 'notes',  
        'bank_detail_id', 'company_detail_id', 'terms_conditions', 'subject',
        'attention_to', 'designation', 'type', 'show_signature', 'show_seal',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'show_signature' => 'boolean',
        'show_seal' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

      public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

      public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function bankDetail()
    {
        return $this->belongsTo(BankDetail::class, 'bank_detail_id');
    }

    public function companyDetail()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_detail_id');
    }

    // Generate bill number based on type
    public static function generateBillNumber($type = 'general')
    {
        $prefixes = [
            'project' => 'PRO',
            'sale' => 'SAL', 
            'purchase' => 'PUR',
            'vendor' => 'VEN',
            'general' => 'BIL'
        ];

        $prefix = $prefixes[$type] ?? 'BIL';
        
        // Get the last bill of this type
        $lastBill = static::where('bill_number', 'like', $prefix . '-%')->latest()->first();
        
        if ($lastBill) {
            // Extract sequence number and increment
            $parts = explode('-', $lastBill->bill_number);
            $lastSequence = end($parts);
            $sequence = intval($lastSequence) + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . '-' . now()->format('Ymd') . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // public function getTypeAttribute()
    // {
    //     if ($this->project_id) return 'project';
    //     if ($this->purchase_id) return 'purchase';
    //     if ($this->vendor_id) return 'vendor';
    //     if ($this->client_id) return 'sale';
    //     return 'general';
    // }

    // Get related entity name for display
    public function getRelatedToAttribute()
    {
        if ($this->project) return $this->project->name;
        if ($this->client) return $this->client->name;
        if ($this->vendor) return $this->vendor->name;
        if ($this->purchase) return $this->purchase->purchase_number;
        return 'General Bill';
    }

    // Check if bill is overdue
    public function getIsOverdueAttribute()
    {
        return $this->status !== 'paid' && $this->bill_date->diffInDays(now()) > 30;
    }

    // Scope methods
    public function scopeProjectBills($query)
    {
        return $query->whereNotNull('project_id');
    }

    public function scopeSaleBills($query)
    {
        return $query->whereNotNull('client_id')->whereNull('project_id');
    }

    public function scopePurchaseBills($query)
    {
        return $query->whereNotNull('purchase_id')->orWhereNotNull('vendor_id');
    }

    public function scopeDraft($query) { return $query->where('status', 'draft'); }
    public function scopeSent($query) { return $query->where('status', 'sent'); }
    public function scopePaid($query) { return $query->where('status', 'paid'); }
    public function scopeOverdue($query) { return $query->where('status', 'overdue'); }
}