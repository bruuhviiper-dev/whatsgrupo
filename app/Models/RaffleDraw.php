<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RaffleDraw extends Model
{
    protected $table = 'raffle_draws';

    protected $fillable = [
        'uuid',
        'title',
        'participants',
        'winners',
        'winner_count',
        'mode',
        'total_participants',
    ];

    protected $casts = [
        'participants' => 'array',
        'winners'      => 'array',
    ];

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
