<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CheckLinksJob;
use App\Jobs\CollectGroupsJob;
use App\Models\Group;
use App\Models\JobLog;
use Illuminate\Http\Request;

/**
 * Controller responsável pelo gerenciamento de coleta automatizada e limpeza de links.
 */
class CollectorController extends Controller
{
    /**
     * Exibe o painel do coletor automático.
     */
    public function index()
    {
        // Estatísticas básicas
        $botGroupsCount = Group::where('submitter_email', 'bot@whatsgrupos.com')->count();
        
        $deactivatedThisWeek = Group::where('status', 'rejected')
            ->where('description', 'like', '%Inativado pelo Bot de Limpeza%')
            ->where('updated_at', '>=', now()->startOfWeek())
            ->count();

        $activeApprovedCount = Group::approved()->count();
        $pendingModerationCount = Group::where('status', 'pending')->count();

        $stats = [
            'bot_groups'           => $botGroupsCount,
            'deactivated_week'     => $deactivatedThisWeek,
            'approved_total'       => $activeApprovedCount,
            'pending_moderation'   => $pendingModerationCount,
        ];

        // Últimos 10 logs de execução da tabela job_logs
        $logs = JobLog::orderBy('executed_at', 'desc')->limit(10)->get();

        return view('admin.collector.index', compact('stats', 'logs'));
    }

    /**
     * Dispara manualmente a coleta automática de grupos (DuckDuckGo).
     */
    public function run()
    {
        // Despacha o Job para a fila (segundo plano)
        CollectGroupsJob::dispatch();

        return redirect()->route('admin.collector.index')
            ->with('success', 'Coletor automático disparado em segundo plano! O progresso será registrado na tabela de logs a seguir.');
    }

    /**
     * Dispara manualmente o validador e faxina de links mortos.
     */
    public function checkLinks()
    {
        // Despacha o Job para a fila (segundo plano)
        CheckLinksJob::dispatch();

        return redirect()->route('admin.collector.index')
            ->with('success', 'Verificador de links mortos disparado em segundo plano! As desativações automáticas serão registradas na tabela de logs a seguir.');
    }
}
