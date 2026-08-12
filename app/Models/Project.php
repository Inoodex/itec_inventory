<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Project extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'project_name',
        'client_id',
        'budget',
        'sub_total',
        'discount',
        'grand_total',
        'advanced_payment',
        'due_payment',
        'description',
        'status',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'project_items' => 'array',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($project) {
    //         // Calculate totals if not set
    //         if (!$project->total) {
    //             $project->total = $project->selling_price * $project->quantity;
    //         }
    //         if (!$project->grand_total) {
    //             $project->grand_total = $project->sub_total - $project->discount;
    //         }
    //         if (!$project->due_payment) {
    //             $project->due_payment = $project->grand_total - $project->advanced_payment;
    //         }
    //     });
    // }

    // public function getProfitMarginAttribute()
    // {
    //     return $this->selling_price - $this->unit_price;
    // }

    // // Accessor for profit percentage
    // public function getProfitPercentageAttribute()
    // {
    //     if ($this->unit_price > 0) {
    //         return (($this->selling_price - $this->unit_price) / $this->unit_price) * 100;
    //     }
    //     return 0;
    // }

    // // Accessor for total profit
    // public function getTotalProfitAttribute()
    // {
    //     return $this->profit_margin * $this->quantity;
    // }

     public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

   public function client()
    {
        return $this->belongsTo(Client::class);
    }

public function items()
{
    return $this->hasMany(ProjectItem::class, 'project_id');
}

 public function projectItems()
{
    return $this->hasMany(ProjectItem::class); 
}

    public function costs()
{
    return $this->hasMany(ProjectCost::class);
}

public function totalCost()
{
    return $this->costs()->sum('amount');
}

}