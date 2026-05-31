<?php

namespace App\Services;

use App\Mail\FreeBoostEarnedMail;
use App\Models\Group;
use App\Models\ReferralCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Serviço responsável pelo controle de indicações (referrals) de grupos de WhatsApp.
 */
class ReferralService
{
    /**
     * Gera ou busca o código de indicações único para um grupo de WhatsApp.
     */
    public function generateForGroup(Group $group): ReferralCode
    {
        return ReferralCode::firstOrCreate(
            ['group_id' => $group->id],
            ['code' => $this->generateUniqueCode()]
        );
    }
    
    /**
     * Registra o clique em um link de indicação, prevenindo abusos por sessão.
     */
    public function registerClick(string $code, Request $request): ?ReferralCode
    {
        $referral = ReferralCode::where('code', strtoupper($code))->first();
        
        if (!$referral) {
            return null;
        }
        
        // Evita contagem duplicada na mesma sessão do navegador
        $sessionKey = "referral_clicked_{$code}";
        
        if (!session($sessionKey)) {
            $referral->increment('clicks');
            session([$sessionKey => true]);
        }
        
        return $referral;
    }
    
    /**
     * Registra a conversão de entrada via indicação.
     * Caso o grupo atinja 5 conversões, concede 6 horas de destaque VIP gratuito.
     */
    public function registerConversion(string $code): void
    {
        $referral = ReferralCode::where('code', strtoupper($code))->first();
        
        if (!$referral) {
            return;
        }
        
        // Incrementa a conversão
        $referral->increment('conversions');
        $referral->refresh();
        
        // Concede o boost gratuito de 6h se bater a meta de 5 conversões
        if ($referral->needsBoost()) {
            $group = $referral->group;
            $durationHours = 6;
            
            // Calcula expiração (estende se já tiver VIP)
            $expiresAt = now()->addHours($durationHours);
            if ($group->is_vip && $group->vip_expires_at && $group->vip_expires_at->isFuture()) {
                $expiresAt = $group->vip_expires_at->addHours($durationHours);
            }
            
            // Concede o VIP no grupo
            $group->update([
                'is_vip'         => true,
                'vip_expires_at' => $expiresAt
            ]);
            
            // Marca o boost como concedido
            $referral->update([
                'boost_granted' => true
            ]);
            
            // Envia o e-mail de recompensa se houver e-mail de contato cadastrado
            if ($group->submitter_email) {
                try {
                    Mail::to($group->submitter_email)->send(new FreeBoostEarnedMail($group));
                } catch (\Exception $e) {
                    Log::warning("[ReferralService] Falha ao enviar e-mail de boost grátis para {$group->submitter_email}: " . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Gera uma hash alfanumérica única e segura de 8 caracteres.
     */
    private function generateUniqueCode(): string
    {
        do {
            // Evita caracteres ambíguos como I, O, 0, 1
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8));
        } while (ReferralCode::where('code', $code)->exists());
        
        return $code;
    }
}
