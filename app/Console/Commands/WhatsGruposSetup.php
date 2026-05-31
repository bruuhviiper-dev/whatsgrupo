<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class WhatsGruposSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsgrupos:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa o assistente de instalação completa automatizada do WhatsGrupos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->output->writeln(' ');
        $this->output->writeln('<fg=magenta;options=bold>====================================================</>');
        $this->output->writeln('<fg=cyan;options=bold>          __      __  .__            __              </>');
        $this->output->writeln('<fg=cyan;options=bold>         /  \    /  \ |  |__  _____ |  | __  ______  </>');
        $this->output->writeln('<fg=cyan;options=bold>         \   \/\/   / |  |  \ \__  \|  |/ / /  ___/  </>');
        $this->output->writeln('<fg=cyan;options=bold>          \        /  |   Y  \ / __ \|    <  \___ \   </>');
        $this->output->writeln('<fg=cyan;options=bold>           \__/\  /   |___|  /(____  /__|_ \/____  >  </>');
        $this->output->writeln('<fg=cyan;options=bold>                \/         \/      \/     \/     \/   </>');
        $this->output->writeln('<fg=green;options=bold>              WhatsGrupos — ASSISTENTE DE SETUP       </>');
        $this->output->writeln('<fg=magenta;options=bold>====================================================</>');
        $this->output->writeln(' ');

        if (!$this->confirm('Deseja iniciar o processo de setup completo automatizado?', true)) {
            $this->warn('Setup cancelado pelo usuário.');
            return Command::FAILURE;
        }

        // 1. Criando o banco de dados SQLite se não existir
        $this->info('1. Verificando o banco de dados SQLite...');
        $dbPath = database_path('database.sqlite');
        if (!File::exists($dbPath)) {
            File::put($dbPath, '');
            $this->info('✅ Arquivo database/database.sqlite criado com sucesso!');
        } else {
            $this->comment('ℹ️ O banco de dados SQLite já existe.');
        }

        // 2. Executando as Migrações
        $this->info('2. Executando as migrações de tabelas...');
        try {
            Artisan::call('migrate', ['--force' => true], $this->output);
            $this->info('✅ Migrações concluídas com sucesso!');
        } catch (\Exception $e) {
            $this->error('❌ Falha ao rodar as migrações: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 3. Executando os Seeders
        $this->info('3. Alimentando o banco de dados com dados iniciais (Seeders)...');
        try {
            Artisan::call('db:seed', ['--force' => true], $this->output);
            $this->info('✅ Banco de dados populado com sucesso (Categorias, Banners, SEO, Frases)!');
        } catch (\Exception $e) {
            $this->error('❌ Falha ao rodar os seeders: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 4. Criando link simbólico da storage
        $this->info('4. Criando o link simbólico de arquivos (storage:link)...');
        if (!File::exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link', [], $this->output);
                $this->info('✅ Link simbólico de armazenamento público criado!');
            } catch (\Exception $e) {
                $this->error('❌ Falha ao criar link simbólico: ' . $e->getMessage());
            }
        } else {
            $this->comment('ℹ️ O link simbólico de storage já existe.');
        }

        // 5. Testando ambiente Python
        $this->info('5. Verificando ambiente Python...');
        $pythonBin = env('PYTHON_BIN', 'python');
        exec($pythonBin . ' --version', $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->info('✅ Python encontrado no sistema: ' . implode(' ', $output));
        } else {
            $this->warn('⚠️ Python não foi encontrado no PATH do sistema. Certifique-se de configurar o PYTHON_BIN no arquivo .env se for utilizar o bot coletor.');
        }

        // Fim
        $this->output->writeln(' ');
        $this->output->writeln('<fg=green;options=bold>====================================================</>');
        $this->output->writeln('<fg=green;options=bold> 🎉 WhatsGrupos configurado com sucesso!            </>');
        $this->output->writeln('<fg=green;options=bold> Use "php artisan serve" para iniciar o servidor.   </>');
        $this->output->writeln('<fg=green;options=bold>====================================================</>');
        $this->output->writeln(' ');

        return Command::SUCCESS;
    }
}
