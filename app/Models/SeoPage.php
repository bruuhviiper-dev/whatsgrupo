<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model representando as páginas de SEO de cauda longa.
 */
class SeoPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'h1',
        'meta_description',
        'category_id',
        'keyword',
        'state',
        'city',
        'extra_term',
        'content',
        'is_active',
        'views',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'views'     => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relacionamentos
    // -------------------------------------------------------------------------

    /**
     * Categoria vinculada a esta página SEO.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // -------------------------------------------------------------------------
    // Scopes (Escopos de Busca)
    // -------------------------------------------------------------------------

    /**
     * Filtra apenas páginas de SEO ativas.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
