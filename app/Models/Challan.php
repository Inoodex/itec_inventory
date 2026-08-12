<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    use HasFactory;

    protected $fillable = [
        'challan_number',
        'reference_number',
        'challan_date',
        'type',
        'sale_id',
        'project_id',
        'customer_id',
        'client_id',
        'recipient_organization',
        'recipient_designation',
        'recipient_address',
        'attention_to',
        'designation',
        'subject',
        'notes',
        'company_name',
        'signatory_name',
        'signatory_designation',
        'company_phone',
        'company_email',
        'company_website',
        'show_signature',
        'show_seal',
    ];

    protected $casts = [
        'challan_date' => 'date',
        'show_signature' => 'boolean',
        'show_seal' => 'boolean',
    ];

    public function challanItems()
    {
        return $this->hasMany(ChallanItem::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}