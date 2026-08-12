<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContraEntry extends Model
{
    use HasFactory;

    protected $table = 'contra_entries';

    protected $fillable = [
        'contra_no',
        'from_account_id',
        'to_account_id',
        'amount',
        'date',
        'description',
        'journal_entry_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateContraNo(?string $date = null): string
    {
        $dateStr = $date ? date('Ymd', strtotime($date)) : date('Ymd');
        $prefix = "CN-{$dateStr}-";

        $lastEntry = self::where('contra_no', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEntry) {
            $lastSeq = (int) substr($lastEntry->contra_no, strlen($prefix));
            $nextSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return $prefix . $nextSeq;
    }
}
