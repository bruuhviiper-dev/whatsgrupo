<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MineracaoLog extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     */
    protected $signature = 'mineracao:log {--follow : Acompanhar o log em tempo real} {--lines=50 : Número de linhas a mostrar}';

    /**
     * A descrição do comando.
     */
    protected $description = 'Exibe o log detalhado de mineração de grupos';

    /**
     * Executa o comando.
     */
    public function handle()
    {
        $logPath = storage_path('logs/mineracao.log');

        if (!File::exists($logPath)) {
            $this->error('❌ Arquivo de log não encontrado: ' . $logPath);
            return 1;
        }

        $lines = (int) $this->option('lines');
        $follow = $this->option('follow');

        if ($follow) {
            $this->followLog($logPath);
        } else {
            $this->displayLog($logPath, $lines);
        }

        return 0;
    }

    /**
     * Exibe o log com as últimas linhas
     */
    private function displayLog($logPath, $lines)
    {
        $content = File::get($logPath);
        $allLines = explode(PHP_EOL, trim($content));

        // Pega as últimas N linhas
        $lastLines = array_slice($allLines, max(0, count($allLines) - $lines));

        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->info('📋 LOG DE MINERAÇÃO DE GRUPOS');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->line('');

        foreach ($lastLines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            // Coloriza as linhas baseado no tipo
            if (strpos($line, '[START]') !== false) {
                $this->info($line);
            } elseif (strpos($line, '[END]') !== false) {
                $this->comment($line);
            } elseif (strpos($line, '[SUCCESS]') !== false) {
                $this->line('<fg=green>' . $line . '</>');
            } elseif (strpos($line, '[ERROR]') !== false) {
                $this->line('<fg=red>' . $line . '</>');
            } elseif (strpos($line, '[WARNING]') !== false) {
                $this->line('<fg=yellow>' . $line . '</>');
            } elseif (strpos($line, '[SUMMARY]') !== false) {
                $this->line('<fg=cyan>' . $line . '</>');
            } else {
                $this->line($line);
            }
        }

        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->line('Total de linhas no arquivo: ' . count($allLines));
        $this->line('Mostrando as últimas: ' . count($lastLines));
        $this->line('');
    }

    /**
     * Acompanha o log em tempo real (tail -f)
     */
    private function followLog($logPath)
    {
        $this->info('📡 Acompanhando log em tempo real (Ctrl+C para sair)...');
        $this->line('');

        $handle = fopen($logPath, 'r');
        fseek($handle, 0, SEEK_END);

        while (true) {
            $line = fgets($handle);
            if ($line !== false) {
                echo $line;
            } else {
                usleep(100000); // 100ms
            }
        }

        fclose($handle);
    }
}
