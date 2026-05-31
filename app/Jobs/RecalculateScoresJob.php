<?php

namespace App\Jobs;

use App\Services\GroupScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

/**
 * Job para recalcular periodicamente o score inteligente de relevância de todos os grupos.
 */
class RecalculateScoresJob implements ShouldQueue
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
    public function handle(GroupScoringService $service): void
    {
        try {
            $service->recalculateAll();
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("[RecalculateScoresJob] Falha ao recalcular scores: " . $e->getMessage());
            throw $e;
        }
    }
}
