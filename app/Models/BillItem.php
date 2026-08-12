<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    protected $fillable = [
      'bill_id', 'description', 'quantity', 'unit_price', 'total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(ProjectBill::class, 'project_bill_id');
    }
}