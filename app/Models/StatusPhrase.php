<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model para as Frases de Status.
 */
class StatusPhrase extends Model
{
    use HasFactory;

    protected $fillable = [
        'hash',
        'phrase',
        'author',
        'category',
        'likes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->hash)) {
                $model->hash = \Illuminate\Support\Str::random(10);
            }
        });
    }

    protected $casts = [
        'likes' => 'integer',
    ];

    /**
     * Retorna a representação legível da categoria.
     */
    public function getCategoryLabelAttribute(): string
    {
        $labels = [
            'amor'      => 'Amor',
            'amizade'   => 'Amizade',
            'motivacao' => 'Motivação',
            'engracado' => 'Engraçado',
            'reflexao'  => 'Reflexão',
        ];

        return $labels[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'hash';
    }
}
