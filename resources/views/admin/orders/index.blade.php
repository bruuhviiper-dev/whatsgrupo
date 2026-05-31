@extends('admin.layout')

@section('title', 'Pedidos VIP')
@section('page-title', 'Pedidos de Impulso VIP')

@section('content')

{{-- Filtros --}}
<form action="{{ route('admin.orders.index') }}" method="GET"
      class="rounded-2xl border border-slate-200 bg-white p-5 mb-6 flex flex-wrap gap-4 items-end shadow-sm">
    <div>
        <label class="block text-slate-500 text-[10px] font-bold mb-1.5 uppercase tracking-wider">Status</label>
        <select name="status"
                class="rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-slate-50 border border-slate-200 focus:outline-none focus:border-[#25D366] focus:ring-1 focus:ring-[#25D366] transition-all font-semibold">
            <option value="">Todos</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Pendente</option>
            <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>✅ Pago</option>
            <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>❌ Falhou</option>
        </select>
    </div>
    <div>
        <label class="block text-slate-500 text-[10px] font-bold mb-1.5 uppercase tracking-wider">Data</label>
        <input type="date" name="date" value="{{ request('date') }}"
               class="rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-slate-50 border border-slate-200 focus:outline-none focus:border-[#25D366] focus:ring-1 focus:ring-[#25D366] transition-all font-medium">
    </div>
    <div class="flex-1 min-w-[200px]">
        <label class="block text-slate-500 text-[10px] font-bold mb-1.5 uppercase tracking-wider">Buscar por e-mail</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="email@exemplo.com"
               class="w-full rounded-xl px-4 py-2.5 text-sm text-slate-900 bg-slate-50 border border-slate-200 focus:outline-none focus:border-[#25D366] focus:ring-1 focus:ring-[#25D366] transition-all font-medium">
    </div>
    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white transition-all bg-slate-900 hover:bg-slate-800 shadow-sm">
        Filtrar
    </button>
    <a href="{{ route('admin.orders.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-all">
        Limpar
    </a>
</form>

{{-- Tabela de pedidos --}}
<div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
    <div class="p-5 border-b border-slate-100 bg-slate-50/50">
        <p class="text-slate-500 text-sm font-semibold">{{ $orders->total() }} pedido(s) encontrado(s)</p>
    </div>

    @if ($orders->isEmpty())
        <div class="p-16 text-center">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
            </div>
            <p class="text-slate-600 font-medium">Nenhum pedido encontrado.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead class="bg-white border-b border-slate-100">
                    <tr>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">#ID</th>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Comprador</th>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pacote</th>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Método</th>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Código</th>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Valor</th>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Data</th>
                        <th class="text-left py-4 px-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($orders as $order)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="text-slate-400 text-xs font-mono font-bold py-3 px-5">#{{ $order->id }}</td>

                        <td class="py-3 px-5">
                            <p class="font-bold text-slate-900 text-sm">{{ Str::limit($order->buyer_name, 22) }}</p>
                            <p class="text-slate-500 text-[11px] font-medium">{{ $order->buyer_email }}</p>
                        </td>

                        <td class="text-xs text-slate-600 font-semibold py-3 px-5">
                            {{ $order->boostPackage->name ?? '-' }}
                            <br>
                            <span class="text-[11px] text-slate-400">{{ $order->boosts_total }} impulsos</span>
                        </td>

                        <td class="text-xs font-bold py-3 px-5">
                            @if ($order->payment_method === 'pix')
                                <span class="text-[#25D366] bg-[#25D366]/10 px-2 py-0.5 rounded border border-[#25D366]/20">PIX</span>
                            @else
                                <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">Cartão</span>
                            @endif
                        </td>

                        <td class="py-3 px-5">
                            @if($order->payment_status === 'paid')
                                <span class="bg-[#25D366] text-white px-2.5 py-1 rounded-md text-xs font-bold shadow-sm">Pago</span>
                            @elseif($order->payment_status === 'pending')
                                <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-md text-xs font-bold">Pendente</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-md text-xs font-bold">Falhou</span>
                            @endif
                        </td>

                        <td class="py-3 px-5">
                            @if ($order->boost_code)
                                <span class="font-mono text-amber-600 bg-amber-50 px-2 py-1 border border-amber-200 rounded text-xs tracking-wider font-bold">{{ $order->boost_code }}</span>
                                <br>
                                <span class="text-slate-500 text-[11px] font-medium mt-1 inline-block">{{ $order->boosts_used }}/{{ $order->boosts_total }} usados</span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>

                        <td class="text-sm font-black text-slate-900 py-3 px-5">
                            R$ {{ number_format($order->amount, 2, ',', '.') }}
                        </td>

                        <td class="text-xs text-slate-500 font-medium py-3 px-5">
                            {{ $order->created_at->format('d/m/Y') }}
                            <br>
                            {{ $order->created_at->format('H:i') }}
                        </td>

                        <td class="py-3 px-5">
                            {{-- Reenviar código (só para pedidos pagos) --}}
                            @if ($order->payment_status === 'paid' && $order->boost_code)
                                <form action="{{ route('admin.orders.resend', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all shadow-sm bg-indigo-50 text-indigo-600 border border-indigo-200 hover:bg-indigo-100 inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        Reenviar
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if ($orders->hasPages())
            <div class="flex justify-center items-center gap-2 p-5 border-t border-slate-100 bg-slate-50/50 flex-wrap">
                @if (!$orders->onFirstPage())
                    <a href="{{ $orders->previousPageUrl() }}"
                       class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        Anterior
                    </a>
                @endif
                @for ($i = max(1, $orders->currentPage() - 2); $i <= min($orders->lastPage(), $orders->currentPage() + 2); $i++)
                    <a href="{{ $orders->url($i) }}"
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-sm
                              {{ $i === $orders->currentPage() ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                        {{ $i }}
                    </a>
                @endfor
                @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}"
                       class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        Próxima
                    </a>
                @endif
            </div>
        @endif
    @endif
</div>

@endsection
