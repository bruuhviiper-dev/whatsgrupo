<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model para os Grupos com Selo de Verificação (Selo Azul).
 */
class VerifiedGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
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
     * Grupo de WhatsApp vinculado a esta verificação.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Escopo para carregar verificações ativas atualmente.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('starts_at', '<=', now())
                     ->where('ends_at', '>=', now());
    }
}
