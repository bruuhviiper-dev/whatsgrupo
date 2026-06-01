@extends('admin.layout')
@section('title', 'Pedidos VIP')
@section('page-title', 'Pedidos de Impulso VIP')
@section('content')

{{-- Filtros --}}
<form action="{{ route('admin.orders.index') }}" method="GET"
      class="card p-5 mb-5 flex flex-wrap gap-3 items-end dark:bg-slate-800 dark:border-slate-700">
    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
        <select name="status" class="rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40 focus:border-emerald-400">
            <option value="">Todos</option>
            <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pendente</option>
            <option value="paid"    {{ request('status')==='paid'   ?'selected':'' }}>Pago</option>
            <option value="failed"  {{ request('status')==='failed' ?'selected':'' }}>Falhou</option>
        </select>
    </div>
    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data</label>
        <input type="date" name="date" value="{{ request('date') }}"
               class="rounded-xl px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40">
    </div>
    <div class="flex-1 min-w-[200px]">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">E-mail</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="email@exemplo.com"
               class="w-full rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-200 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40 placeholder:text-slate-400">
    </div>
    <button type="submit" class="btn btn-green shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zm3 4a1 1 0 011-1h10a1 1 0 010 2H7a1 1 0 01-1-1zm4 4a1 1 0 011-1h2a1 1 0 010 2h-2a1 1 0 01-1-1z"/></svg>
        Filtrar
    </button>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-slate">Limpar</a>
</form>

{{-- Tabela --}}
<div class="card overflow-hidden dark:bg-slate-800 dark:border-slate-700">
    <div class="card-header dark:border-slate-700">
        <p class="text-slate-500 dark:text-slate-400 text-sm font-semibold">{{ $orders->total() }} pedido(s)</p>
    </div>

    @if ($orders->isEmpty())
        <div class="py-16 text-center">
            <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Nenhum pedido encontrado.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Comprador</th>
                    <th class="text-left">Pacote</th>
                    <th class="text-left">Método</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Código</th>
                    <th class="text-left">Valor</th>
                    <th class="text-left">Data</th>
                    <th class="text-left">Ação</th>
                </tr></thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr>
                        <td class="text-slate-400 dark:text-slate-500 font-mono text-xs font-bold">#{{ $order->id }}</td>
                        <td>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ Str::limit($order->buyer_name, 20) }}</p>
                            <p class="text-slate-400 text-[11px] font-medium">{{ $order->buyer_email }}</p>
                        </td>
                        <td class="text-xs text-slate-600 dark:text-slate-400 font-semibold">
                            {{ $order->boostPackage->name ?? '-' }}
                            <br><span class="text-slate-400 text-[10px]">{{ $order->boosts_total }} impulsos</span>
                        </td>
                        <td>
                            @if($order->payment_method === 'pix')
                                <span class="inline-flex items-center gap-1 text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2 py-0.5 rounded text-[11px] font-bold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/></svg>
                                    PIX
                                </span>
                            @else
                                <span class="text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 px-2 py-0.5 rounded text-[11px] font-bold inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    Cartão
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($order->payment_status === 'paid')
                                <span class="badge badge-paid">Pago</span>
                            @elseif($order->payment_status === 'pending')
                                <span class="badge badge-pending">Pendente</span>
                            @else
                                <span class="badge badge-rejected">Falhou</span>
                            @endif
                        </td>
                        <td>
                            @if($order->boost_code)
                                <span class="font-mono text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 px-2 py-0.5 rounded text-xs font-bold tracking-wider">{{ $order->boost_code }}</span>
                                <br><span class="text-slate-400 text-[10px] mt-0.5 inline-block">{{ $order->boosts_used }}/{{ $order->boosts_total }} usados</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="text-sm font-black text-slate-900 dark:text-white">R$ {{ number_format($order->amount,2,',','.') }}</td>
                        <td>
                            <p class="text-xs text-slate-600 dark:text-slate-400 font-semibold">{{ $order->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-slate-400 font-medium">{{ $order->created_at->format('H:i') }}</p>
                        </td>
                        <td>
                            @if($order->payment_status === 'paid' && $order->boost_code)
                                <form action="{{ route('admin.orders.resend', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-blue" style="padding:5px 10px;font-size:11px;">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        Reenviar
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="flex justify-center items-center gap-1.5 px-5 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex-wrap">
                @if (!$orders->onFirstPage())
                    <a href="{{ $orders->previousPageUrl() }}" class="btn btn-slate" style="padding:6px 14px;font-size:12px;">← Anterior</a>
                @endif
                @for ($i = max(1, $orders->currentPage()-2); $i <= min($orders->lastPage(), $orders->currentPage()+2); $i++)
                    <a href="{{ $orders->url($i) }}" class="btn {{ $i===$orders->currentPage() ? 'btn-green' : 'btn-slate' }}" style="padding:6px 12px;font-size:12px;min-width:36px;justify-content:center;">{{ $i }}</a>
                @endfor
                @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="btn btn-slate" style="padding:6px 14px;font-size:12px;">Próxima →</a>
                @endif
            </div>
        @endif
    @endif
</div>
@endsection
