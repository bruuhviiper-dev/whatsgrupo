<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostOrder;
use App\Models\Group;

/**
 * Controller do painel de administração — visão geral com estatísticas.
 */
class DashboardController extends Controller
{
    public function index()
    {
        // Totais gerais de grupos
        $totalGroups   = Group::count();
        $pendingGroups = Group::where('status', 'pending')->count();
        $activeVips    = Group::notExpiredVip()->count();

        // Pedidos de hoje
        $ordersToday = BoostOrder::whereDate('created_at', today())->count();

        // Receita do mês atual (apenas pedidos pagos)
        $revenueThisMonth = BoostOrder::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Últimos 5 pedidos para a tabela rápida (com paginação independente)
        $latestOrders = BoostOrder::with('boostPackage')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'orders_page');

        // Últimos 5 grupos pendentes para moderação rápida (com paginação independente)
        $pendingGroupsList = Group::with('category')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'pending_page');

        return view('admin.dashboard', compact(
            'totalGroups',
            'pendingGroups',
            'activeVips',
            'ordersToday',
            'revenueThisMonth',
            'latestOrders',
            'pendingGroupsList'
        ));
    }
}
