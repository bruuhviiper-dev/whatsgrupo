<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model para as Categorias Patrocinadas (Sponsorship banners).
 */
class SponsoredCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sponsor_name',
        'banner_path',
        'link_url',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Categoria vinculada a este patrocínio.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Escopo para carregar patrocínios ativos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('starts_at', '<=', now())
                     ->where('ends_at', '>=', now());
    }
}
