<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

     public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get categories by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Create or find category
     */
    public static function findOrCreate($name, $type = 'direct')
    {
        return static::firstOrCreate(
            ['name' => $name],
            ['type' => $type, 'is_active' => true]
        );
    }

}