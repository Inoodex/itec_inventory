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
    ];

    public function getMonthNameAttribute()
    {
        return date("F", mktime(0, 0, 0, $this->month, 10));
    }

    public function getFormattedProfitAttribute()
    {
        return number_format($this->net_profit, 2);
    }
}