<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Artisan command: php artisan vip:expire
 * Expira manualmente todos os grupos VIP cujo prazo já venceu.
 * Também limpa o cache da home e das categorias afetadas.
 */
class ExpireVipBoosts extends Command
{
    protected $signature = 'vip:expire {--dry-run : Apenas lista os grupos que seriam expirados, sem alterar nada}';
    protected $description = 'Expira VIPs vencidos e limpa o cache da home e das categorias afetadas';

    public function handle(): int
    {
        $expiredGroups = Group::with('category')
            ->where('is_vip', true)
            ->whereNotNull('vip_expires_at')
            ->where('vip_expires_at', '<=', now())
            ->get();

        if ($expiredGroups->isEmpty()) {
            $this->info('Nenhum grupo VIP expirado encontrado.');
            return 0;
        }

        $this->table(
            ['ID', 'Nome', 'Categoria', 'Expirou em'],
            $expiredGroups->map(fn ($g) => [
                $g->id,
                $g->name,
                $g->category?->name ?? '-',
                $g->vip_expires_at?->format('d/m/Y H:i') ?? '-',
            ])
        );

        if ($this->option('dry-run')) {
            $this->warn("[dry-run] Nenhuma alteração feita. {$expiredGroups->count()} grupo(s) seriam expirados.");
            return 0;
        }

        $categorySlugs = $expiredGroups
            ->pluck('category.slug')
            ->filter()
            ->unique()
            ->values();

        // Expira todos
        Group::where('is_vip', true)
            ->whereNotNull('vip_expires_at')
            ->where('vip_expires_at', '<=', now())
            ->update(['is_vip' => false]);

        // Limpa cache da home
        $tabs = ['all', 'vip', 'popular', 'novos'];
        for ($page = 1; $page <= 10; $page++) {
            foreach ($tabs as $tab) {
                Cache::forget("home_data_tab_{$tab}_page_{$page}");
            }
        }

        // Limpa cache das categorias afetadas
        foreach ($categorySlugs as $slug) {
            for ($page = 1; $page <= 10; $page++) {
                Cache::forget("category_groups_{$slug}_page_{$page}");
            }
        }

        $count = $expiredGroups->count();
        $this->info("✅ {$count} grupo(s) VIP expirado(s) com sucesso.");
        $this->info("🧹 Cache da home e categorias [{$categorySlugs->join(', ')}] limpo.");

        return 0;
    }
}
