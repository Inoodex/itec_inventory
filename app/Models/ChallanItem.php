<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'challan_id',
        'description',
        'quantity',
        'unit',
    ];

    public function challan()
    {
        return $this->belongsTo(Challan::class);
    }
}