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
        'status',
        'motivo_rejeicao',
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

    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovado');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeRejeitadas($query)
    {
        return $query->where('status', 'rejeitado');
    }

    public static function allCategories(): array
    {
        return [
            'amor'       => 'Amor',
            'amizade'    => 'Amizade',
            'motivacao'  => 'Motivação',
            'engracado'  => 'Engraçado',
            'reflexao'   => 'Reflexão',
            'academia'   => 'Academia',
            'boa-noite'  => 'Boa Noite',
            'bom-dia'    => 'Bom Dia',
            'curtas'     => 'Curtas',
            'deus'       => 'Deus',
            'evangelica' => 'Evangélica',
            'gratidao'   => 'Gratidão',
            'indiretas'  => 'Indiretas',
            'musicas'    => 'Músicas',
            'sozinha'    => 'Sozinha',
            'tristes'    => 'Tristes',
            'visao'      => 'Visão',
            'familia'    => 'Família',
            'falsidade'  => 'Falsidade',
            'maloka'     => 'Maloka',
        ];
    }
}
