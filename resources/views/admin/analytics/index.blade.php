@extends('admin.layout')

@section('title', 'Analytics & Relatórios')
@section('page-title', 'Analytics & Relatórios')

@section('content')

{{-- Métricas --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Faturamento Geral', 'R$ '.number_format($totalSalesRevenue,2,',','.'), 'text-emerald-500', 'bg-emerald-50 dark:bg-emerald-500/10', '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        ['Pedidos Pagos', number_format($totalSalesCount), 'text-blue-500', 'bg-blue-50 dark:bg-blue-500/10', '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
        ['Entradas em Grupos', number_format($totalClicksSum), 'text-amber-500', 'bg-amber-50 dark:bg-amber-500/10', '<path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>'],
        ['Visualizações', number_format($totalViewsSum), 'text-purple-500', 'bg-purple-50 dark:bg-purple-500/10', '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'],
    ] as [$label,$value,$color,$bg,$path])
    <div class="card stat-card p-5 dark:bg-slate-800 dark:border-slate-700">
        <div class="w-10 h-10 rounded-xl {{ $bg }} {{ $color }} mb-3 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">{!! $path !!}</svg>
        </div>
        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">{{ $label }}</p>
        <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $value }}</p>
    </div>
    @endforeach
</div>

{{-- Gráficos --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="card p-5 dark:bg-slate-800 dark:border-slate-700">
        <h2 class="text-slate-900 dark:text-white font-black text-sm mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            Receita Diária (Últimos 30 dias)
        </h2>
        <div class="relative h-64"><canvas id="salesChart"></canvas></div>
    </div>
    <div class="card p-5 dark:bg-slate-800 dark:border-slate-700">
        <h2 class="text-slate-900 dark:text-white font-black text-sm mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Novos Grupos Cadastrados
        </h2>
        <div class="relative h-64"><canvas id="groupsChart"></canvas></div>
    </div>
</div>

{{-- Top Listagens --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 card overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div class="card-header dark:border-slate-700">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
                Top 10 por Engajamento
            </h2>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-700 dark:text-slate-400 px-2 py-1 rounded">por cliques</span>
        </div>
        <table class="data-table">
            <thead><tr>
                <th class="text-left">Grupo</th>
                <th class="text-left">Categoria</th>
                <th class="text-center">Views</th>
                <th class="text-center">Cliques</th>
                <th class="text-center">Conversão</th>
            </tr></thead>
            <tbody>
                @foreach ($topGroups as $group)
                <tr>
                    <td>
                        <p class="font-bold text-slate-900 dark:text-white text-sm">{{ Str::limit($group->name, 32) }}</p>
                        @if ($group->is_currently_vip)
                            <span class="text-[9px] bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 px-1.5 py-0.5 rounded uppercase font-black tracking-wider mt-0.5 inline-block">VIP</span>
                        @endif
                    </td>
                    <td class="text-slate-500 dark:text-slate-400 text-xs font-semibold">
                        <div class="flex items-center gap-1.5">
                            @if($group->category->icon && str_starts_with($group->category->icon, 'heroicon'))
                                <x-dynamic-component :component="$group->category->icon" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                            @endif
                            {{ $group->category->name ?? '-' }}
                        </div>
                    </td>
                    <td class="text-center text-xs text-slate-500 dark:text-slate-400 font-medium">{{ number_format($group->views) }}</td>
                    <td class="text-center text-xs font-black text-blue-600 dark:text-blue-400">{{ number_format($group->clicks) }}</td>
                    <td class="text-center text-xs font-mono text-purple-600 dark:text-purple-400 font-bold">
                        {{ $group->views > 0 ? round(($group->clicks / $group->views) * 100, 1).'%' : '0%' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card overflow-hidden dark:bg-slate-800 dark:border-slate-700 flex flex-col">
        <div class="card-header dark:border-slate-700">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                Top 6 Categorias
            </h2>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-700 flex-1">
            @foreach ($topCategories as $index => $cat)
            <div class="px-4 py-3 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-black font-mono w-5 text-slate-400">#{{ $index+1 }}</span>
                    @if($cat->icon && str_starts_with($cat->icon, 'heroicon'))
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            <x-dynamic-component :component="$cat->icon" class="w-4 h-4 text-slate-500 dark:text-slate-400" />
                        </div>
                    @endif
                    <p class="text-slate-900 dark:text-white text-sm font-bold">{{ $cat->name }}</p>
                </div>
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 px-2 py-1 rounded-lg">
                    {{ $cat->groups_count }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.04)';
    const tickColor = isDark ? '#64748b' : '#94a3b8';

    new Chart(document.getElementById('salesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{ label: 'R$', data: {!! json_encode($salesChartData) !!}, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.08)', borderWidth: 2.5, fill: true, tension: 0.35, pointRadius: 3, pointBackgroundColor: '#10b981' }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ x:{grid:{color:gridColor},ticks:{color:tickColor,font:{size:10,weight:'600'}}}, y:{grid:{color:gridColor},ticks:{color:tickColor,font:{size:10,weight:'600'}}} } }
    });

    new Chart(document.getElementById('groupsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{ label: 'Grupos', data: {!! json_encode($groupsChartData) !!}, backgroundColor: '#3b82f6', borderRadius: 5, maxBarThickness: 14 }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ x:{grid:{display:false},ticks:{color:tickColor,font:{size:10,weight:'600'}}}, y:{grid:{color:gridColor},ticks:{color:tickColor,font:{size:10,weight:'600'}}} } }
    });
});
</script>
@endsection
