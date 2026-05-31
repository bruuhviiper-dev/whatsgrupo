<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostOrder;
use App\Models\Category;
use App\Models\Group;
use Illuminate\Http\Request;

/**
 * Controller do painel de administração — Módulo de Analytics avançado com gráficos.
 */
class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Gera base de dados temporal (últimos 30 dias)
        $groupsDaily = Group::selectRaw("date(created_at) as date, count(*) as count")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $salesDaily = BoostOrder::selectRaw("date(created_at) as date, sum(amount) as revenue")
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get()
            ->pluck('revenue', 'date')
            ->toArray();

        $chartLabels = [];
        $groupsChartData = [];
        $salesChartData = [];

        for ($i = 29; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $groupsChartData[] = $groupsDaily[$dateStr] ?? 0;
            $salesChartData[] = floatval($salesDaily[$dateStr] ?? 0.0);
        }

        // 2. Top 10 grupos mais clicados
        $topGroups = Group::approved()
            ->with('category')
            ->orderBy('clicks', 'desc')
            ->limit(10)
            ->get();

        // 3. Top categorias populares
        $topCategories = Category::withCount(['groups' => fn($q) => $q->where('status', 'approved')])
            ->orderBy('groups_count', 'desc')
            ->limit(6)
            ->get();

        // 4. Métricas consolidadas gerais
        $totalSalesCount   = BoostOrder::where('payment_status', 'paid')->count();
        $totalSalesRevenue = BoostOrder::where('payment_status', 'paid')->sum('amount');
        $totalClicksSum    = Group::sum('clicks');
        $totalViewsSum     = Group::sum('views');

        return view('admin.analytics.index', compact(
            'chartLabels',
            'groupsChartData',
            'salesChartData',
            'topGroups',
            'topCategories',
            'totalSalesCount',
            'totalSalesRevenue',
            'totalClicksSum',
            'totalViewsSum'
        ));
    }
}
