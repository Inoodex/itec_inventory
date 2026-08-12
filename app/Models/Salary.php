<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'employee_id',
        'month',
        'basic_salary',
        'advance',
        'allowance',
        'deduction',
        'net_salary',
        'payment_status',
        'payment_date',
        'note',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}