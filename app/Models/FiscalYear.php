<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    use HasFactory;

    protected $table = 'fiscal_years';

    protected $fillable = [
        'year_name',
        'start_date',
        'end_date',
        'is_active',
        'is_closed',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    // Journal entries linked to this fiscal year
    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'fiscal_year_id');
    }

    // User who closed the fiscal year
    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_closed', false);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    /**
     * Check if a specific date falls within this fiscal year.
     */
    public function containsDate(string $date): bool
    {
        return $date >= $this->start_date->format('Y-m-d') && $date <= $this->end_date->format('Y-m-d');
    }
}
