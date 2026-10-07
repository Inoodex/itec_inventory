<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'total_sales',
        'total_purchases',
        'total_expenses',
        'net_profit',
        'remarks',
    ];

    protected $casts = [
        'total_sales' => 'float',
        'total_purchases' => 'float',
        'total_expenses' => 'float',
        'net_profit' => 'float',
    ];

    public function getNetProfitAttribute($value)
    {
        if ($value !== null) {
            return (float) $value;
        }
        return (float) (($this->total_sales ?? 0) - ($this->total_purchases ?? 0) - ($this->total_expenses ?? 0));
    }

    public function getMonthNameAttribute()
    {
        return date("F", mktime(0, 0, 0, $this->month, 10));
    }

    public function getFormattedProfitAttribute()
    {
        return number_format($this->net_profit, 2);
    }
}