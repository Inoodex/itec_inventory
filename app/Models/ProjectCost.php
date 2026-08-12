<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'cost_category_id',
        'description',
        'amount',
        'cost_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cost_date' => 'date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return $this->belongsTo(CostCategory::class, 'cost_category_id');
    }

    // Scope for project costs
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    // Scope by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Get status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'estimated' => 'bg-warning',
            'confirmed' => 'bg-info',
            'paid' => 'bg-success'
        ];

        return '<span class="badge ' . ($badges[$this->status] ?? 'bg-secondary') . '">' . ucfirst($this->status) . '</span>';
    }
}