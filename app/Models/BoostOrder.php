<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BoostOrder extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'boost_package_id',
        'buyer_name',
        'buyer_email',
        'payment_method',
        'payment_status',
        'payment_id',
        'pix_qr_code',
        'pix_copy_paste',
        'boost_code',
        'boosts_total',
        'boosts_used',
        'amount',
    ];

    // Converte os tipos de dados do banco para tipos nativos do PHP
    protected $casts = [
        'amount' => 'decimal:2',
        'boosts_total' => 'integer',
        'boosts_used' => 'integer',
    ];

    /**
     * Relacionamento com o Pacote VIP (Um pedido pertence a um pacote).
     */
    public function boostPackage(): BelongsTo
    {
        return $this->belongsTo(BoostPackage::class);
    }

    /**
     * Relacionamento com os Usos do Impulso (Um pedido de impulsos possui muitos usos).
     */
    public function boostUsages(): HasMany
    {
        return $this->hasMany(BoostUsage::class);
    }

    /**
     * Accessor: calcula quantos impulsos ainda estão disponíveis para uso neste pedido.
     * Retorna integer (ex: boosts_total - boosts_used)
     */
    public function getRemainingBoostsAttribute(): int
    {
        return max(0, $this->boosts_total - $this->boosts_used);
    }

    /**
     * Método Estático: gera uma chave de 12 caracteres alfanuméricos maiúsculos única
     * para ser utilizada como cupom de ativação de impulsos.
     */
    public static function generateCode(): string
    {
        do {
            // Gera um código aleatório alfanumérico e força letras maiúsculas
            $code = strtoupper(Str::random(12));
        } while (static::where('boost_code', $code)->exists()); // Garante unicidade

        return $code;
    }
}
