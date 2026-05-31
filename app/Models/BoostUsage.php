<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoostUsage extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'boost_order_id',
        'group_id',
        'applied_at',
        'expires_at',
    ];

    // Converte os tipos de dados do banco para tipos nativos do PHP
    protected $casts = [
        'applied_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relacionamento com Pedido de Impulso (Um uso pertence a um pedido de impulsos).
     */
    public function boostOrder(): BelongsTo
    {
        return $this->belongsTo(BoostOrder::class);
    }

    /**
     * Relacionamento com Grupo (Um uso é aplicado a um grupo de WhatsApp).
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
