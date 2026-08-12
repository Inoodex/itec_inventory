<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyExpense extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'user_id', 
        'employee_id',
        'expense_category_id',   
        'remarks',
        'amount',
        'spend_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id');
}

    
}