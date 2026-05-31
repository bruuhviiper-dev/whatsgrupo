@extends('layouts.phrases')

@section('title', 'Minhas Frases | WhatsGrupos')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
    @include('phrases.partials.sidebar-left')

    {{-- Coluna Principal --}}
    <div class="lg:col-span-6 space-y-8">
        {{-- Breadcrumbs --}}
        <nav class="flex text-slate-500 text-xs font-bold uppercase tracking-wider mt-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('phrases.index') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                        <x-heroicon-s-home class="w-3.5 h-3.5" />
                        Início
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-m-chevron-right class="w-4 h-4 text-slate-400 mx-1" />
                        <span class="text-slate-400">Minhas Frases</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 text-center mb-8 pt-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <x-heroicon-s-bookmark class="w-8 h-8 text-[#25D366]" />
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 mb-2">Minhas Frases Enviadas</h1>
            <p class="text-slate-500 max-w-2xl mx-auto">
                Aqui você gerencia todas as frases que você enviou para o WhatsGrupos a partir deste navegador.
            </p>
        </div>

        @if($phrases->isEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center flex flex-col items-center justify-center shadow-sm">
                <x-heroicon-o-face-frown class="w-16 h-16 text-slate-300 mb-4" />
                <h3 class="text-lg font-bold text-slate-700 mb-2">Nenhuma frase encontrada!</h3>
                <p class="text-slate-500 text-sm mb-6 max-w-md mx-auto">
                    Você ainda não enviou nenhuma frase ou seus cookies foram limpos recentemente. Que tal contribuir enviando uma frase inspiradora agora?
                </p>
                <a href="{{ route('phrases.create') }}" class="bg-[#25D366] text-white px-6 py-3 rounded-lg font-bold shadow-md hover:bg-green-500 transition-colors">
                    Enviar Minha Primeira Frase
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($phrases as $phrase)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="bg-green-50 text-primary border border-green-100 text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-md">
                                    {{ $categories[$phrase->category]['label'] ?? $phrase->category }}
                                </span>
                                <span class="text-xs font-bold text-slate-400 flex items-center gap-1">
                                    <x-heroicon-s-heart class="w-3.5 h-3.5" /> {{ $phrase->likes }}
                                </span>
                            </div>
                            <p class="text-slate-700 font-medium leading-relaxed mb-4">
                                “{{ $phrase->phrase }}”
                            </p>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-wide mb-6">
                                — {{ $phrase->author ?: 'Anônimo' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 pt-4 border-t border-slate-100 mt-auto">
                            <a href="{{ route('phrases.show', $phrase) }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs py-2.5 rounded-lg transition-colors flex items-center justify-center gap-1.5">
                                <x-heroicon-s-eye class="w-4 h-4" /> Visualizar
                            </a>

                            <form action="{{ route('phrases.destroyMyPhrase', $phrase) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja excluir esta frase permanentemente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs py-2.5 rounded-lg transition-colors flex items-center justify-center gap-1.5 border border-red-100">
                                    <x-heroicon-s-trash class="w-4 h-4" /> Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @include('phrases.partials.sidebar-right')
</div>
@endsection
