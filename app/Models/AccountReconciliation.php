<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountReconciliation extends Model
{
    use HasFactory;

    protected $table = 'account_reconciliations';

    protected $fillable = [
        'account_id',
        'bank_statement_date',
        'statement_balance',
        'book_balance',
        'difference',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'bank_statement_date' => 'date',
        'statement_balance' => 'decimal:2',
        'book_balance' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
