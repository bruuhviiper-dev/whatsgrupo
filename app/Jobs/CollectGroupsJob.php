<?php

namespace App\Jobs;

use App\Models\JobLog;
use App\Services\GroupCollectorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class CollectGroupsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout generoso: coleta completa (8+ diretórios + buscadores) pode demorar horas.
     */
    public $timeout = 10800; // 3h

    /**
     * Tentativas em caso de falha.
     */
    public $tries = 2;

    public function __construct()
    {
        $this->onQueue('coleta');
    }

    public function handle(GroupCollectorService $service): void
    {
        ini_set('memory_limit', '-1'); // sem limite — evita estouro em coletas grandes

        $executedAt = now();

        try {
            $newGroupsCount = $service->collect();

            JobLog::create([
                'job_type'       => 'collect_groups',
                'status'         => 'success',
                'result_summary' => "Coleta concluída! {$newGroupsCount} grupos importados como pendentes.",
                'executed_at'    => $executedAt,
            ]);
        } catch (Exception $e) {
            JobLog::create([
                'job_type'       => 'collect_groups',
                'status'         => 'failed',
                'result_summary' => 'Falha na coleta de grupos: ' . $e->getMessage(),
                'executed_at'    => $executedAt,
            ]);

            throw $e;
        }
    }
}
