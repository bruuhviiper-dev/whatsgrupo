@extends('admin.layout')

@section('title', 'Figurinhas — Admin')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-black text-slate-900">Figurinhas</h1>
        <p class="text-sm text-slate-500 mt-0.5">Modere as figurinhas enviadas pela comunidade.</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold px-3 py-1.5 rounded-full">
            {{ $pendentes }} pendentes
        </span>
    </div>
</div>

{{-- Filtros --}}
<div class="bg-white border border-slate-200 rounded-2xl p-4 mb-6 flex flex-wrap gap-3 items-center">
    <form method="GET" action="{{ route('admin.figurinhas.index') }}" class="flex gap-3 flex-wrap flex-1">
        <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl px-3 py-2 font-medium bg-white text-slate-700 outline-none focus:border-green-400">
            <option value="">Todos os status</option>
            @foreach(\App\Enums\FigurinhaStatus::cases() as $s)
                <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
            @endforeach
        </select>
        <select name="categoria" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl px-3 py-2 font-medium bg-white text-slate-700 outline-none focus:border-green-400">
            <option value="">Todas as categorias</option>
            @foreach(\App\Enums\FigurinhaCategoria::cases() as $c)
                <option value="{{ $c->value }}" {{ request('categoria') === $c->value ? 'selected' : '' }}>{{ $c->emoji() }} {{ $c->label() }}</option>
            @endforeach
        </select>
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por título..." class="text-sm border border-slate-200 rounded-xl px-3 py-2 font-medium text-slate-700 outline-none focus:border-green-400 flex-1 min-w-[160px]">
        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold px-4 py-2 rounded-xl transition-colors">Filtrar</button>
        @if(request()->hasAny(['status','categoria','busca']))
            <a href="{{ route('admin.figurinhas.index') }}" class="text-sm text-slate-500 hover:text-red-500 font-bold px-3 py-2 rounded-xl border border-slate-200 hover:border-red-200 transition-colors">Limpar</a>
        @endif
    </form>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 text-sm font-medium px-4 py-3 rounded-2xl mb-6 flex items-center gap-2">
        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 text-sm font-medium px-4 py-3 rounded-2xl mb-6">{{ session('error') }}</div>
@endif

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <table class="w-full admin-table">
        <thead>
            <tr>
                <th class="text-left w-16">Preview</th>
                <th class="text-left">Título / Categoria</th>
                <th class="text-left">Status</th>
                <th class="text-left">Envios</th>
                <th class="text-left">Data</th>
                <th class="text-left">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($figurinhas as $figurinha)
                <tr>
                    {{-- Preview --}}
                    <td>
                        <a href="{{ $figurinha->url_arquivo }}" target="_blank" class="block w-12 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 hover:border-green-300 transition-colors">
                            <img src="{{ $figurinha->url_arquivo }}" alt="{{ $figurinha->titulo }}" class="w-full h-full object-contain p-1">
                        </a>
                    </td>

                    {{-- Título / Categoria --}}
                    <td>
                        <p class="font-bold text-slate-900 text-[13px]">{{ $figurinha->titulo }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $figurinha->categoria->emoji() }} {{ $figurinha->categoria->label() }}</p>
                        @if($figurinha->tags)
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach(array_slice($figurinha->tags, 0, 4) as $tag)
                                    <span class="bg-slate-100 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-md font-medium">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="{{ $figurinha->status->color() }} text-xs font-bold px-2.5 py-1 rounded-full">
                            {{ $figurinha->status->label() }}
                        </span>
                        @if($figurinha->status->value === 'rejeitado' && $figurinha->motivo_rejeicao)
                            <p class="text-[10px] text-red-500 mt-1 max-w-[140px] truncate" title="{{ $figurinha->motivo_rejeicao }}">{{ $figurinha->motivo_rejeicao }}</p>
                        @endif
                    </td>

                    {{-- Downloads/Visualizações --}}
                    <td>
                        <p class="text-xs text-slate-600"><span class="font-bold">{{ number_format($figurinha->downloads, 0, '', '.') }}</span> downloads</p>
                        <p class="text-xs text-slate-400">{{ number_format($figurinha->visualizacoes, 0, '', '.') }} views</p>
                    </td>

                    {{-- Data --}}
                    <td class="text-xs text-slate-400">{{ $figurinha->created_at->format('d/m/Y H:i') }}</td>

                    {{-- Ações --}}
                    <td>
                        <div class="flex items-center gap-2">
                            @if($figurinha->status->value === 'pendente')
                                <form method="POST" action="{{ route('admin.figurinhas.aprovar', $figurinha) }}">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">Aprovar</button>
                                </form>
                                <button type="button"
                                    onclick="document.getElementById('modal-rejeitar-{{ $figurinha->id }}').classList.remove('hidden')"
                                    class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold px-3 py-1.5 rounded-lg border border-red-200 transition-colors">
                                    Rejeitar
                                </button>
                            @endif
                            <form method="POST" action="{{ route('admin.figurinhas.destroy', $figurinha) }}" onsubmit="return confirm('Excluir esta figurinha? A imagem também será apagada.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>

                        {{-- Modal Rejeitar --}}
                        @if($figurinha->status->value === 'pendente')
                        <div id="modal-rejeitar-{{ $figurinha->id }}" class="hidden fixed inset-0 bg-slate-900/50 flex items-center justify-center z-50">
                            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4">
                                <h3 class="font-black text-slate-900 text-lg mb-1">Rejeitar figurinha</h3>
                                <p class="text-sm text-slate-500 mb-4">Informe o motivo para que o usuário entenda.</p>
                                <form method="POST" action="{{ route('admin.figurinhas.rejeitar', $figurinha) }}">
                                    @csrf
                                    <textarea name="motivo" rows="3" placeholder="Ex: Imagem com conteúdo inadequado..." required
                                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 outline-none focus:border-red-400 focus:ring-4 focus:ring-red-50 transition-all mb-4 resize-none"></textarea>
                                    <div class="flex gap-2">
                                        <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">Confirmar Rejeição</button>
                                        <button type="button" onclick="document.getElementById('modal-rejeitar-{{ $figurinha->id }}').classList.add('hidden')"
                                            class="flex-1 border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-slate-400 text-sm font-medium">
                        Nenhuma figurinha encontrada.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($figurinhas->hasPages())
    <div class="mt-6">{{ $figurinhas->links() }}</div>
@endif

@endsection
