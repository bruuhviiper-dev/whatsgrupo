<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoostPackage extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'name',
        'slug',
        'boosts_count',
        'price',
        'original_price',
        'savings_percent',
        'duration_hours',
        'is_popular',
        'is_active',
    ];

    // Converte os tipos de dados do banco para tipos nativos do PHP
    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com Pedidos de Impulsos (Um pacote possui muitos pedidos).
     */
    public function boostOrders(): HasMany
    {
        return $this->hasMany(BoostOrder::class);
    }

    /**
     * Accessor: Formata o preço em Real Brasileiro.
     * Retorna ex: "R$ 14,90"
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    /**
     * Accessor: Formata o preço original em Real Brasileiro.
     * Retorna ex: "R$ 34,93"
     */
    public function getFormattedOriginalPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->original_price, 2, ',', '.');
    }

    /**
     * Accessor: Gera o rótulo de desconto.
     * Retorna ex: "Economize 40%"
     */
    public function getDiscountLabelAttribute(): string
    {
        return $this->savings_percent > 0 ? "Economize {$this->savings_percent}%" : "";
    }
}
