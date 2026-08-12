<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaDa extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'user_id',
        'date',
        'amount',
        'used_amount',
        'remaining_amount',
        'purpose',
        'type',
        'payment_type',
    ];
public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id', 'id');
}


//     public function employee()
// {
//     return $this->belongsTo(User::class, 'user_id');
// }


    public function user()
{
    return $this->belongsTo(User::class);
}

}