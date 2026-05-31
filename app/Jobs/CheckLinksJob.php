<?php

namespace App\Jobs;

use App\Models\JobLog;
use App\Services\LinkCheckerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

/**
 * Job para rodar a limpeza e verificação periódica de links de grupos de forma assíncrona.
 */
class CheckLinksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Cria uma nova instância de job.
     */
    public function __construct()
    {
        //
    }

    /**
     * Executa o Job.
     */
    public function handle(LinkCheckerService $service): void
    {
        $executedAt = now();

        try {
            $report = $service->check();

            JobLog::create([
                'job_type'       => 'check_links',
                'status'         => 'success',
                'result_summary' => "Verificação concluída! Total de grupos avaliados: {$report['checked']}. Links desativados por estarem mortos ou inválidos: {$report['deactivated']}.",
                'executed_at'    => $executedAt,
            ]);
        } catch (Exception $e) {
            JobLog::create([
                'job_type'       => 'check_links',
                'status'         => 'failed',
                'result_summary' => "Falha na execução de verificação de links: " . $e->getMessage(),
                'executed_at'    => $executedAt,
            ]);

            throw $e;
        }
    }
}
