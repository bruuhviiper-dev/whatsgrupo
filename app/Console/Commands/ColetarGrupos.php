<?php

namespace App\Console\Commands;

use App\Jobs\CollectGroupsJob;
use App\Models\Category;
use App\Services\GroupCollectorService;
use Illuminate\Console\Command;

class ColetarGrupos extends Command
{
    protected $signature = 'grupos:coletar
                            {--queue : Disparar via fila (modo job) ao invés de executar direto}';

    protected $description = 'Coleta grupos reais de WhatsApp em diretórios públicos e buscadores, cadastrando-os conforme as regras de negócio.';

    public function handle(): int
    {
        set_time_limit(0);
        ini_set('memory_limit', '2G');

        $this->line('');
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║       WhatsGrupos – Coletor de Grupos v2.0           ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->line('');

        // Validação de categorias
        $categories = Category::all(['id', 'name', 'slug']);
        if ($categories->isEmpty()) {
            $this->error('❌ Nenhuma categoria cadastrada. Execute: php artisan db:seed --class=CategorySeeder');
            return 1;
        }

        $this->info("📂 Categorias para coleta: {$categories->count()}");
        $categories->each(fn ($c) => $this->line("   • {$c->name} [{$c->slug}]"));
        $this->line('');

        // Modo fila
        if ($this->option('queue')) {
            CollectGroupsJob::dispatch();
            $this->info('✅ Job CollectGroupsJob despachado para a fila!');
            $this->line('   → Execute: php artisan queue:work --queue=default --timeout=7200');
            return 0;
        }

        $this->info('🚀 Iniciando coleta direta...');
        $this->line('   Log em tempo real: php artisan mineracao:log --follow');
        $this->line('');

        $service = new GroupCollectorService();
        $total = $service->collect();

        $this->line('');
        $this->info("✨ Coleta concluída!");
        $this->info("📈 Grupos cadastrados como pendentes: {$total}");
        $this->line('');
        $this->comment('Para moderar os grupos: php artisan tinker → Group::where("status","pending")->count()');

        return 0;
    }
}
