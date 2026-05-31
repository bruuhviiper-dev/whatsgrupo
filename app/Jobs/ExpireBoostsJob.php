<?php

namespace App\Jobs;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job responsável por expirar os impulsos VIP de grupos cujo prazo já venceu.
 * Executado a cada 5 minutos via agendador do Laravel.
 */
class ExpireBoostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Executa o job de expiração de VIPs.
     * Busca todos os grupos com is_vip=true e vip_expires_at <= agora,
     * e reverte o status VIP deles.
     */
    public function handle(): void
    {
        // Busca grupos que ainda estão marcados como VIP mas já estão expirados
        $expiredGroups = Group::where('is_vip', true)
            ->where('vip_expires_at', '<=', now())
            ->get();

        $count = $expiredGroups->count();

        if ($count === 0) {
            return;
        }

        // Atualiza todos os grupos expirados de uma só vez para eficiência
        Group::where('is_vip', true)
            ->where('vip_expires_at', '<=', now())
            ->update([
                'is_vip' => false,
            ]);

        // Registra a operação nos logs do Laravel para monitoramento
        Log::info("[ExpireBoostsJob] {$count} grupo(s) VIP expirado(s) e revertidos com sucesso.");
    }
}
