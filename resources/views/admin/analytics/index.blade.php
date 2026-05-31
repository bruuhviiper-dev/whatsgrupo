@extends('admin.layout')

@section('title', 'Analytics & Relatórios')
@section('page-title', 'Analytics & Relatórios')

@section('content')

{{-- Métricas Consolidadas --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 mb-3 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Faturamento Geral</p>
        <p class="text-slate-900 text-3xl font-black">R$ {{ number_format($totalSalesRevenue, 2, ',', '.') }}</p>
        <p class="text-xs font-medium text-emerald-600 mt-2 bg-emerald-50 inline-block px-2 py-0.5 rounded border border-emerald-100">Total acumulado em vendas</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 mb-3 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
        </div>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Total de Pedidos Pagos</p>
        <p class="text-slate-900 text-3xl font-black">{{ number_format($totalSalesCount) }}</p>
        <p class="text-xs font-medium text-slate-500 mt-2">Transações processadas</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 mb-3 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
        </div>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Entradas em Grupos</p>
        <p class="text-slate-900 text-3xl font-black">{{ number_format($totalClicksSum) }}</p>
        <p class="text-xs font-medium text-slate-500 mt-2">Cliques no botão de entrar</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-500 mb-3 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
        </div>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Visualizações Gerais</p>
        <p class="text-slate-900 text-3xl font-black">{{ number_format($totalViewsSum) }}</p>
        <p class="text-xs font-medium text-slate-500 mt-2">Visualizações de detalhes</p>
    </div>
</div>

{{-- Área de Gráficos --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Gráfico 1: Vendas Diárias (PIX) --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-slate-900 font-black text-sm mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            Receita Diária (Últimos 30 dias)
        </h2>
        <div class="relative h-72">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Gráfico 2: Novos Cadastros Diários --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-slate-900 font-black text-sm mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            Novos Grupos Cadastrados
        </h2>
        <div class="relative h-72">
            <canvas id="groupsChart"></canvas>
        </div>
    </div>
</div>

{{-- Top Listagens --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Top 10 Grupos por Cliques (2/3 da largura) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-slate-900 font-bold text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" /></svg>
                Top 10 Grupos por Engajamento
            </h2>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-100 px-2 py-1 rounded">Ordenados por Cliques</span>
        </div>
        <table class="admin-table w-full">
            <thead class="bg-white">
                <tr>
                    <th class="text-left py-3 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">Grupo</th>
                    <th class="text-left py-3 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">Categoria</th>
                    <th class="text-center py-3 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">Visualizações</th>
                    <th class="text-center py-3 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">Entradas</th>
                    <th class="text-center py-3 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">Conversão</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($topGroups as $group)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-5">
                            <p class="font-bold text-slate-900 text-sm">{{ Str::limit($group->name, 35) }}</p>
                            @if ($group->is_currently_vip)
                                <span class="text-[9px] bg-amber-50 text-amber-600 border border-amber-200 px-1.5 py-0.5 rounded uppercase font-black tracking-wider mt-1 inline-block">VIP</span>
                            @endif
                        </td>
                        <td class="py-3 px-5 text-slate-600 text-xs font-semibold flex items-center gap-1.5 h-full pt-4">
                            @if($group->category->icon && str_starts_with($group->category->icon, 'heroicon'))
                                <x-dynamic-component :component="$group->category->icon" class="w-4 h-4 text-slate-400" />
                            @endif
                            {{ $group->category->name ?? '-' }}
                        </td>
                        <td class="py-3 px-5 text-center text-xs text-slate-500 font-medium">{{ number_format($group->views) }}</td>
                        <td class="py-3 px-5 text-center text-xs font-black text-blue-600">{{ number_format($group->clicks) }}</td>
                        <td class="py-3 px-5 text-center text-xs font-mono text-purple-600 font-bold bg-purple-50/50">
                            {{ $group->views > 0 ? round(($group->clicks / $group->views) * 100, 1) . '%' : '0%' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Categorias Mais Populares (1/3 da largura) --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-slate-900 font-bold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                    Top 6 Categorias Mais Populares
                </h2>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($topCategories as $index => $cat)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-black font-mono w-5 text-slate-400">#{{ $index + 1 }}</span>
                            @if($cat->icon && str_starts_with($cat->icon, 'heroicon'))
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                                    <x-dynamic-component :component="$cat->icon" class="w-5 h-5" />
                                </div>
                            @endif
                            <div>
                                <p class="text-slate-900 text-sm font-bold">{{ $cat->name }}</p>
                                <p class="text-slate-400 text-[10px] font-mono mt-0.5">{{ $cat->slug }}</p>
                            </div>
                        </div>
                        <span class="bg-[#25D366]/10 text-[#25D366] text-[11px] font-bold px-2.5 py-1.5 rounded-lg border border-[#25D366]/20">
                            {{ $cat->groups_count }} grupos
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            Diretório segmentado em tempo real
        </div>
    </div>
</div>

{{-- Scripts Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var primaryColor = '#3b82f6'; // blue-500
        var secondaryColor = '#10b981'; // emerald-500

        // Gráfico de Vendas
        new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: {!! json_encode($salesChartData) !!},
                    borderColor: secondaryColor,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: secondaryColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { color: '#64748b', font: { size: 10, weight: '600' } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { color: '#64748b', font: { size: 10, weight: '600' } }
                    }
                }
            }
        });

        // Gráfico de Cadastros de Grupos
        new Chart(document.getElementById('groupsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Novos Grupos',
                    data: {!! json_encode($groupsChartData) !!},
                    backgroundColor: primaryColor,
                    borderRadius: 6,
                    maxBarThickness: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 10, weight: '600' } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { color: '#64748b', font: { size: 10, weight: '600' } }
                    }
                }
            }
        });
    });
</script>

@endsection
