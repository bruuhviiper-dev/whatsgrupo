<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model representando o código de indicações (referrals) de um grupo de WhatsApp.
 */
class ReferralCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'code',
        'clicks',
        'conversions',
        'boost_granted',
    ];

    protected $casts = [
        'clicks'        => 'integer',
        'conversions'   => 'integer',
        'boost_granted' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relacionamentos
    // -------------------------------------------------------------------------

    /**
     * Grupo de WhatsApp vinculado a este referral.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // -------------------------------------------------------------------------
    // Métodos Customizados
    // -------------------------------------------------------------------------

    /**
     * Determina se o grupo atingiu o critério para ganhar um boost VIP gratuito (5 conversões).
     */
    public function needsBoost(): bool
    {
        return $this->conversions >= 5 && !$this->boost_granted;
    }
}
