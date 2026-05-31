<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'rules',
        'image_path',
        'whatsapp_link',
        'invite_hash',
        'submitter_email',
        'status',
        'is_vip',
        'vip_expires_at',
        'is_gambling',
        'views',
        'clicks',
        'score',
    ];

    // Converte os tipos de dados do banco para tipos nativos do PHP
    protected $casts = [
        'is_vip'      => 'boolean',
        'is_gambling' => 'boolean',
        'vip_expires_at' => 'datetime',
        'views'  => 'integer',
        'clicks' => 'integer',
        'score'  => 'float',
    ];

    /**
     * Registro de eventos do modelo (Boot).
     * Responsável por gerar um slug único automaticamente ao criar o grupo.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            $originalSlug = Str::slug($group->name);
            $slug = $originalSlug;
            $count = 1;

            // Garante que o slug seja único no banco de dados
            while (static::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $group->slug = $slug;
        });
    }

    /**
     * Relacionamento com Categoria (Um grupo pertence a uma categoria).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relacionamento com o Histórico de Impulsos (Um grupo possui muitos usos de impulsos).
     */
    public function boostUsages(): HasMany
    {
        return $this->hasMany(BoostUsage::class);
    }

    /**
     * Relacionamento com indicações (referral).
     */
    public function referralCode()
    {
        return $this->hasOne(ReferralCode::class);
    }

    /**
     * Relacionamento com selo de verificação mensal.
     */
    public function verifiedGroup()
    {
        return $this->hasOne(VerifiedGroup::class);
    }

    /**
     * Accessor: verifica se o grupo está com selo de verificação ativo no momento.
     */
    public function getIsVerifiedAttribute(): bool
    {
        return $this->verifiedGroup && $this->verifiedGroup->is_active && 
               $this->verifiedGroup->starts_at->isPast() && 
               $this->verifiedGroup->ends_at->isFuture();
    }

    /**
     * Escopo para carregar apenas grupos aprovados.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Escopo para carregar grupos marcados como VIP.
     */
    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    /**
     * Escopo para carregar apenas grupos VIP com prazo de validade ativo (não expirado).
     */
    public function scopeNotExpiredVip($query)
    {
        return $query->where('is_vip', true)
                     ->where('vip_expires_at', '>', now());
    }

    /**
     * Accessor: verifica se o grupo está ativo no VIP atualmente.
     * Retorna booleano combinando a flag e o prazo do VIP no Carbon.
     */
    public function getIsCurrentlyVipAttribute(): bool
    {
        return $this->is_vip && $this->vip_expires_at && $this->vip_expires_at->isFuture();
    }

    /**
     * Escopo para filtrar grupos de apostas/gambling.
     */
    public function scopeGambling($query)
    {
        return $query->where('is_gambling', true);
    }

    /**
     * Escopo para filtrar grupos que NÃO são de apostas.
     */
    public function scopeNotGambling($query)
    {
        return $query->where('is_gambling', false);
    }

    /**
     * Accessor: verifica se o grupo pode ser impulsionado (boost/VIP).
     * Grupos de apostas NUNCA podem ser impulsionados — regra de negócio
     * para conformidade com gateways de pagamento.
     */
    public function getCanBoostAttribute(): bool
    {
        return ! $this->is_gambling;
    }

    /**
     * Detecta automaticamente se o grupo é de apostas/gambling
     * com base nas palavras-chave de nome e descrição.
     *
     * Estratégia de matching:
     *   - Palavras curtas (≤ 4 chars) como "bet", "odd", "lay" usam word-boundary
     *     para evitar falsos positivos (ex: "abet", "betão").
     *   - Expressões com espaço (multi-word) usam substring simples.
     *   - Palavras longas (> 4 chars) também usam substring simples.
     */
    public static function detectGambling(string $name, string $description = ''): bool
    {
        $keywords = config('prohibited_words.gambling', []);

        // Remove acentos e normaliza para comparação
        $text = mb_strtolower($name . ' ' . $description);
        $text = self::removeAccents($text);

        foreach ($keywords as $keyword) {
            $kw = mb_strtolower($keyword);
            $kwNorm = self::removeAccents($kw);

            // Keyword multi-word ou longa: substring match
            if (str_contains($kw, ' ') || mb_strlen($kw) > 4) {
                if (str_contains($text, $kwNorm)) {
                    return true;
                }
                continue;
            }

            // Keyword curta (≤ 4 chars): word-boundary match para evitar falsos positivos
            // Aceita: separadores comuns no português (espaço, hífen, ponto, vírgula, parêntese, número+palavra)
            $pattern = '/(?<![a-z0-9])' . preg_quote($kwNorm, '/') . '(?![a-z0-9])/u';
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove acentos/diacríticos para comparação case-insensitive sem acento.
     */
    private static function removeAccents(string $str): string
    {
        $map = [
            'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','ñ'=>'n',
        ];
        return strtr($str, $map);
    }
}
