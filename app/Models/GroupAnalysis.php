<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GroupAnalysis extends Model
{
    use HasFactory;

    protected $table = 'group_analyses';

    protected $fillable = [
        'uuid',
        'group_name',
        'category',
        'engagement_level',
        'engagement_percent',
        'msgs_per_day',
        'peak_time',
        'growth_trend',
        'health_score',
        'pros',
        'cons',
        'public_summary',
    ];

    protected $casts = [
        'pros' => 'array',
        'cons' => 'array',
        'health_score' => 'float',
    ];

    /**
     * Boot function to automatically generate UUID.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
