<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Services\GroupCollectorService;
use Illuminate\Console\Command;

class WhatsGruposPopulate extends Command
{
    protected $signature = 'whatsgrupos:povoar {--limit=1000 : Limite de grupos a inserir por rodada}';
    protected $description = 'Minera grupos de WhatsApp usando Python collector (DuckDuckGo + sites diretos).';

    public function handle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2G');

        $this->info('🚀 Iniciando Engine Completa de Coleta de Grupos...');

        $categories = Category::all(['id', 'name', 'slug']);
        if ($categories->isEmpty()) {
            $this->error('Por favor, cadastre pelo menos uma categoria antes de rodar.');
            return 1;
        }

        $this->info("📊 Total de categorias para mineração: " . $categories->count());

        // Usa o serviço Python que varre sites + DuckDuckGo
        $service = new GroupCollectorService();
        $totalCadastrados = $service->collect();

        $this->newLine();
        $this->info("✨ Processo concluído!");
        $this->info("📈 Total de grupos inseridos nesta rodada: {$totalCadastrados}");

        return 0;
    }
}