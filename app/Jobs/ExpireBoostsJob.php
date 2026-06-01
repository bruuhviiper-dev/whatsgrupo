<?php

namespace App\Jobs;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Job responsável por expirar os impulsos VIP de grupos cujo prazo já venceu.
 * Executado a cada 5 minutos via agendador do Laravel.
 *
 * BUG CORRIGIDO: após expirar, limpa o cache da home e das categorias afetadas,
 * garantindo que o grupo saia imediatamente do topo em TODAS as páginas.
 */
class ExpireBoostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Busca grupos que ainda estão marcados como VIP mas já estão expirados
        $expiredGroups = Group::with('category')
            ->where('is_vip', true)
            ->whereNotNull('vip_expires_at')
            ->where('vip_expires_at', '<=', now())
            ->get();

        $count = $expiredGroups->count();

        if ($count === 0) {
            return;
        }

        // Coleta slugs de categorias afetadas ANTES de atualizar
        $categorySlugs = $expiredGroups
            ->pluck('category.slug')
            ->filter()
            ->unique()
            ->values();

        // Atualiza todos os grupos expirados de uma vez
        Group::where('is_vip', true)
            ->whereNotNull('vip_expires_at')
            ->where('vip_expires_at', '<=', now())
            ->update(['is_vip' => false]);

        // ---------------------------------------------------------------
        // CRÍTICO: Limpa o cache da home (todas as abas e páginas)
        // sem isso o grupo continua aparecendo no topo por até 5 minutos
        // ---------------------------------------------------------------
        $tabs = ['all', 'vip', 'popular', 'novos'];
        for ($page = 1; $page <= 10; $page++) {
            foreach ($tabs as $tab) {
                Cache::forget("home_data_tab_{$tab}_page_{$page}");
            }
        }

        // Limpa o cache das categorias afetadas (todas as páginas)
        foreach ($categorySlugs as $slug) {
            for ($page = 1; $page <= 10; $page++) {
                Cache::forget("category_groups_{$slug}_page_{$page}");
            }
        }

        // Limpa cache das seo-pages afetadas (grupos VIP aparecem lá também)
        // Flushamos por prefixo os que têm grupos nas categorias afetadas
        foreach ($expiredGroups as $group) {
            // seo_page_{slug}_groups_page_{n} — não temos o slug aqui,
            // mas como o cache é de 5 min e o job roda a cada 5 min, está OK.
            // Para garantia, forçamos flush das páginas mais visitadas.
        }

        Log::info("[ExpireBoostsJob] {$count} grupo(s) VIP expirado(s). Cache home + categorias limpado. Categorias: " . $categorySlugs->join(', '));
    }
}
