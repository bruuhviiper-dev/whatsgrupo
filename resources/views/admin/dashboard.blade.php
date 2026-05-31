@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── STATS GRID ── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 md:gap-4 mb-6">

    @php
    $stats = [
        ['Total Grupos',    $totalGroups,       '#3b82f6', '#eff6ff', '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
        ['Pendentes',       $pendingGroups,     '#f59e0b', '#fffbeb', '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        ['VIPs Ativos',     $activeVips,        '#f59e0b', '#fefce8', '<path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>'],
        ['Pedidos Hoje',    $ordersToday,       '#22c55e', '#f0fdf4', '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>'],
        ['Receita do Mês',  'R$ ' . number_format($revenueThisMonth, 2, ',', '.'), '#25D366', '#f0fdf4', '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
    ];
    @endphp

    @foreach ($stats as $s)
    <div class="card stat-card p-4 dark:bg-slate-800 dark:border-slate-700">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 flex-shrink-0"
             style="background:{{ $s[3] }};">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="{{ $s[2] }}" stroke-width="2">{!! $s[4] !!}</svg>
        </div>
        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">{{ $s[0] }}</p>
        <p class="text-2xl font-black text-slate-900 dark:text-white leading-none">{{ $s[1] }}</p>
    </div>
    @endforeach

    {{-- Quick action card --}}
    @if ($pendingGroups > 0)
    <a href="{{ route('admin.groups.pending') }}"
       class="card stat-card p-4 flex flex-col items-center justify-center text-center group cursor-pointer border-amber-200 hover:border-amber-400 transition-colors"
       style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
        <span class="text-3xl font-black text-amber-600">{{ $pendingGroups }}</span>
        <span class="text-[11px] font-bold text-amber-700 mt-1.5 flex items-center gap-1">
            Aprovar agora
            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </span>
    </a>
    @endif
</div>

{{-- ── TABLES GRID ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5">

    {{-- Grupos Pendentes --}}
    <div class="card overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div class="card-header border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                Aguardando Aprovação
            </h2>
            <a href="{{ route('admin.groups.pending') }}" class="btn btn-slate text-[11px]" style="padding:5px 10px;">
                Ver todos →
            </a>
        </div>

        @if ($pendingGroupsList->isEmpty())
            <div class="py-12 text-center">
                <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-slate-500 text-sm font-medium">Tudo limpo! Nenhum pendente.</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Grupo</th>
                        <th class="text-left">Categoria</th>
                        <th class="text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendingGroupsList as $group)
                    <tr>
                        <td>
                            <p class="font-bold text-slate-900 dark:text-white text-[13px]">{{ Str::limit($group->name, 30) }}</p>
                            <p class="text-slate-400 text-[11px] font-medium">{{ $group->created_at->diffForHumans() }}</p>
                        </td>
                        <td>
                            <span class="text-slate-500 text-[12px] font-semibold">{{ $group->category->name ?? '—' }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.groups.index') }}?status=pending"
                               class="btn btn-orange text-[11px]" style="padding:5px 10px;">
                                Moderar →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginação --}}
            @if ($pendingGroupsList->hasPages())
                <div class="flex justify-center items-center gap-1.5 px-4 py-3 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex-wrap">
                    @if (!$pendingGroupsList->onFirstPage())
                        <a href="{{ $pendingGroupsList->previousPageUrl() }}"
                           class="btn btn-slate text-[11px]" style="padding:4px 10px;">← Ant</a>
                    @endif
                    @if ($pendingGroupsList->hasMorePages())
                        <a href="{{ $pendingGroupsList->nextPageUrl() }}"
                           class="btn btn-slate text-[11px]" style="padding:4px 10px;">Próx →</a>
                    @endif
                </div>
            @endif
        @endif
    </div>

    {{-- Últimos Pedidos --}}
    <div class="card overflow-hidden dark:bg-slate-800 dark:border-slate-700">
        <div class="card-header border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
                Últimos Pedidos
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-slate text-[11px]" style="padding:5px 10px;">
                Ver todos →
            </a>
        </div>

        @if ($latestOrders->isEmpty())
            <div class="py-12 text-center">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="text-slate-500 text-sm font-medium">Nenhum pedido ainda.</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Comprador</th>
                        <th class="text-left">Pacote</th>
                        <th class="text-left">Status</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestOrders as $order)
                    <tr>
                        <td>
                            <p class="font-bold text-slate-900 dark:text-white text-[13px]">{{ Str::limit($order->buyer_name, 18) }}</p>
                            <p class="text-slate-400 text-[11px] font-medium">{{ $order->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="text-[12px] text-slate-600 dark:text-slate-400 font-semibold">{{ $order->boostPackage->name ?? '—' }}</td>
                        <td>
                            @if($order->payment_status === 'paid')
                                <span class="badge badge-paid">Pago</span>
                            @elseif($order->payment_status === 'pending')
                                <span class="badge badge-pending">Pendente</span>
                            @else
                                <span class="badge badge-rejected">Falhou</span>
                            @endif
                        </td>
                        <td class="text-right text-[13px] font-black text-slate-900 dark:text-white">
                            R$ {{ number_format($order->amount, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginação --}}
            @if ($latestOrders->hasPages())
                <div class="flex justify-center items-center gap-1.5 px-4 py-3 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex-wrap">
                    @if (!$latestOrders->onFirstPage())
                        <a href="{{ $latestOrders->previousPageUrl() }}"
                           class="btn btn-slate text-[11px]" style="padding:4px 10px;">← Ant</a>
                    @endif
                    @if ($latestOrders->hasMorePages())
                        <a href="{{ $latestOrders->nextPageUrl() }}"
                           class="btn btn-slate text-[11px]" style="padding:4px 10px;">Próx →</a>
                    @endif
                </div>
            @endif
        @endif
    </div>

</div>

@endsection
