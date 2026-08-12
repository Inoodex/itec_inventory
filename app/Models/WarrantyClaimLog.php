<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarrantyClaimLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_claim_id',
        'user_id',
        'status',
        'note',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(WarrantyClaim::class, 'warranty_claim_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
