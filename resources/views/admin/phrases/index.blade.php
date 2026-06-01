@extends('admin.layout')

@section('title', 'Frases — Admin')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Frases de Status</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Modere as frases enviadas pela comunidade.</p>
    </div>
    <div class="flex items-center gap-3">
        @if($pendentes > 0)
            <span class="bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold px-3 py-1.5 rounded-full">
                {{ $pendentes }} pendentes
            </span>
        @else
            <span class="bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold px-3 py-1.5 rounded-full">
                Tudo em dia ✓
            </span>
        @endif
    </div>
</div>

{{-- Filtros --}}
<div class="card p-4 mb-6 dark:bg-slate-800 dark:border-slate-700">
    <form method="GET" action="{{ route('admin.phrases.index') }}" class="flex gap-3 flex-wrap items-center no-confirm">
        <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 font-semibold bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 outline-none focus:border-emerald-400 transition">
            <option value="">Todos os status</option>
            <option value="pendente"  {{ request('status') === 'pendente'  ? 'selected' : '' }}>Pendente</option>
            <option value="aprovado"  {{ request('status') === 'aprovado'  ? 'selected' : '' }}>Aprovado</option>
            <option value="rejeitado" {{ request('status') === 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
        </select>
        <select name="categoria" onchange="this.form.submit()" class="text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 font-semibold bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 outline-none focus:border-emerald-400 transition">
            <option value="">Todas as categorias</option>
            @foreach(\App\Models\StatusPhrase::allCategories() as $slug => $label)
                <option value="{{ $slug }}" {{ request('categoria') === $slug ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <div class="relative flex-1 min-w-[160px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por frase ou autor..."
                   class="w-full pl-9 pr-4 text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 font-medium text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-900 outline-none focus:border-emerald-400 transition">
        </div>
        <button type="submit" class="btn btn-green shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zm3 4a1 1 0 011-1h10a1 1 0 010 2H7a1 1 0 01-1-1zm4 4a1 1 0 011-1h2a1 1 0 010 2h-2a1 1 0 01-1-1z"/></svg>
            Filtrar
        </button>
        @if(request()->hasAny(['status','categoria','busca']))
            <a href="{{ route('admin.phrases.index') }}" class="btn btn-slate">Limpar</a>
        @endif
    </form>
</div>

@if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-xl px-4 py-3 text-sm font-semibold shadow-sm mb-6">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 rounded-xl px-4 py-3 text-sm font-semibold shadow-sm mb-6">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
@endif

<div class="card overflow-hidden dark:bg-slate-800 dark:border-slate-700">
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-left">Frase / Autor</th>
                <th class="text-left w-28">Categoria</th>
                <th class="text-left w-32">Status</th>
                <th class="text-left w-20">Likes</th>
                <th class="text-left w-32">Data</th>
                <th class="text-left w-44">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($phrases as $phrase)
                <tr>
                    {{-- Frase / Autor --}}
                    <td>
                        <p class="text-[13px] text-slate-800 dark:text-slate-200 leading-relaxed line-clamp-2 max-w-md">
                            "{{ $phrase->phrase }}"
                        </p>
                        @if($phrase->author)
                            <p class="text-xs text-slate-400 mt-0.5">— {{ $phrase->author }}</p>
                        @endif
                    </td>

                    {{-- Categoria --}}
                    <td>
                        <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold px-2 py-0.5 rounded-lg">
                            {{ \App\Models\StatusPhrase::allCategories()[$phrase->category] ?? ucfirst($phrase->category) }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td>
                        @php
                            $statusColor = match($phrase->status) {
                                'aprovado'  => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
                                'pendente'  => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400',
                                'rejeitado' => 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400',
                                default     => 'bg-slate-100 text-slate-600',
                            };
                            $statusLabel = match($phrase->status) {
                                'aprovado'  => 'Aprovado',
                                'pendente'  => 'Pendente',
                                'rejeitado' => 'Rejeitado',
                                default     => ucfirst($phrase->status),
                            };
                        @endphp
                        <span class="{{ $statusColor }} text-xs font-bold px-2.5 py-1 rounded-full">
                            {{ $statusLabel }}
                        </span>
                        @if($phrase->status === 'rejeitado' && $phrase->motivo_rejeicao)
                            <p class="text-[10px] text-red-500 mt-1 max-w-[160px] truncate" title="{{ $phrase->motivo_rejeicao }}">
                                {{ $phrase->motivo_rejeicao }}
                            </p>
                        @endif
                    </td>

                    {{-- Likes --}}
                    <td class="text-sm font-bold text-slate-600 dark:text-slate-400">
                        {{ number_format($phrase->likes, 0, '', '.') }}
                    </td>

                    {{-- Data --}}
                    <td class="text-xs text-slate-400">{{ $phrase->created_at->format('d/m/Y H:i') }}</td>

                    {{-- Ações --}}
                    <td>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($phrase->status !== 'aprovado')
                                <form method="POST" action="{{ route('admin.phrases.aprovar', $phrase) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-green" style="padding:5px 10px;font-size:11px;">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Aprovar
                                    </button>
                                </form>
                            @endif
                            @if($phrase->status !== 'rejeitado')
                                <button type="button"
                                    onclick="document.getElementById('modal-rejeitar-{{ $phrase->id }}').classList.remove('hidden')"
                                    class="btn btn-red" style="padding:5px 10px;font-size:11px;">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Rejeitar
                                </button>
                            @endif
                            <form method="POST" action="{{ route('admin.phrases.destroy', $phrase) }}" onsubmit="return confirm('Excluir esta frase permanentemente?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-slate" style="padding:5px 8px;font-size:11px;" title="Excluir">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>

                        {{-- Modal Rejeitar --}}
                        @if($phrase->status !== 'rejeitado')
                        <div id="modal-rejeitar-{{ $phrase->id }}" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4">
                                <h3 class="font-black text-slate-900 dark:text-white text-lg mb-1">Rejeitar frase</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Informe o motivo da rejeição.</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900 rounded-xl p-3 mb-4 italic line-clamp-3">"{{ $phrase->phrase }}"</p>
                                <form method="POST" action="{{ route('admin.phrases.rejeitar', $phrase) }}" class="no-confirm">
                                    @csrf
                                    <textarea name="motivo" rows="3" placeholder="Ex: Conteúdo inadequado, discurso de ódio..." required
                                        class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 outline-none focus:border-red-400 transition-all mb-4 resize-none"></textarea>
                                    <div class="flex gap-2">
                                        <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors flex items-center justify-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Confirmar Rejeição
                                        </button>
                                        <button type="button" onclick="document.getElementById('modal-rejeitar-{{ $phrase->id }}').classList.add('hidden')"
                                            class="flex-1 btn btn-slate py-2.5 rounded-xl text-sm justify-center">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-slate-400 dark:text-slate-500 text-sm font-medium">
                        Nenhuma frase encontrada.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($phrases->hasPages())
    <div class="mt-6 px-1">{{ $phrases->links() }}</div>
@endif

@endsection
