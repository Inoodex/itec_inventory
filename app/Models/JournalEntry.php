<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $table = 'journal_entries';

    protected $fillable = [
        'journal_no',
        'entry_date',
        'fiscal_year_id',
        'reference_type',
        'reference_id',
        'description',
        'total_debit',
        'total_credit',
        'status',
        'reversed_entry_id',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Items / lines in this voucher
    public function items()
    {
        return $this->hasMany(JournalEntryItem::class, 'journal_entry_id');
    }

    // Linked fiscal year
    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    // User who created the entry
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // User who approved the entry
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Reversal entry relationship (if this entry was reversed)
    public function reversedEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_entry_id');
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->whereIn('status', ['posted', 'approved']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('entry_date', [$from, $to]);
    }

    /**
     * Generate unique journal voucher number: JV-YYYYMMDD-0001
     */
    public static function generateJournalNo(?string $date = null): string
    {
        $dateStr = $date ? date('Ymd', strtotime($date)) : date('Ymd');
        $prefix = "JV-{$dateStr}-";

        $lastEntry = self::where('journal_no', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEntry) {
            $lastSeq = (int) substr($lastEntry->journal_no, strlen($prefix));
            $nextSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return $prefix . $nextSeq;
    }
}
