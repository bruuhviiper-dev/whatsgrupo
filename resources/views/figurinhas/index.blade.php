@extends('layouts.figurinhas')

@section('navbar_color', 'bg-[#15803d]')

@section('title', 'Figurinhas para WhatsApp | WhatsGrupos')
@section('description', 'Baixe as melhores figurinhas e stickers para WhatsApp. Categorias: Engraçado, Amor, Zoeira e mais!')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-900 mb-1">Figurinhas para WhatsApp</h1>
        <p class="text-sm text-slate-500 font-medium">
            <span class="text-slate-900 font-bold">{{ $figurinhas->total() }}</span> figurinhas disponíveis para baixar grátis.
        </p>
    </div>
    
    <a href="{{ route('figurinhas.create') }}" class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-2.5 rounded-full transition-all shadow-md hover:shadow-lg whitespace-nowrap">
        <x-heroicon-o-plus-circle class="w-5 h-5" /> Enviar Figurinha
    </a>
</div>

<!-- Barra de Busca -->
<form method="GET" action="{{ route('figurinhas.index') }}" class="mb-8 relative">
    @if(request('categoria'))
        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
    @endif
    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por título ou palavra-chave..." 
           class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-slate-700 font-medium outline-none focus:border-green-500 focus:ring-4 focus:ring-green-50 transition-all shadow-sm">
    @if(request('busca'))
        <a href="{{ route('figurinhas.index', ['categoria' => request('categoria')]) }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 bg-slate-100 hover:bg-red-50 p-1.5 rounded-full transition-colors">
            <x-heroicon-s-x-mark class="w-4 h-4" />
        </a>
    @endif
</form>

<x-adsense class="mb-8" />

<!-- Filtros de Categorias -->
<div class="flex overflow-x-auto gap-2 mb-8 pb-2 scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap sm:overflow-visible">
    <a href="{{ route('figurinhas.index', ['busca' => request('busca')]) }}" 
       class="whitespace-nowrap flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all border {{ !request('categoria') ? 'bg-slate-900 border-slate-900 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50' }}">
       Todos
    </a>
    @foreach($categorias as $cat)
        <a href="{{ route('figurinhas.index', ['categoria' => $cat->value, 'busca' => request('busca')]) }}" 
           class="whitespace-nowrap flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all border {{ request('categoria') === $cat->value ? 'bg-green-100 border-green-200 text-green-800 shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50' }}">
           {{ $cat->emoji() }} {{ $cat->label() }}
        </a>
    @endforeach
</div>

@if($figurinhas->isEmpty())
    <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center shadow-sm">
        <x-heroicon-o-face-frown class="w-16 h-16 text-slate-300 mx-auto mb-4" />
        <h3 class="text-xl font-bold text-slate-700 mb-2">Nenhuma figurinha encontrada</h3>
        <p class="text-slate-500 text-sm">Tente mudar sua busca ou categoria.</p>
    </div>
@else
    <!-- Grid de Figurinhas -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        @foreach($figurinhas as $index => $figurinha)
            @if($index === 4)
                <div class="col-span-2 md:col-span-3 lg:col-span-4 flex justify-center w-full">
                    <x-adsense class="my-4" />
                </div>
            @endif
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col">
            <!-- Imagem Container -->
            <a href="{{ route('figurinhas.show', $figurinha->slug) }}" class="block relative w-full aspect-square bg-gradient-to-br from-slate-50 to-slate-100 overflow-hidden group-hover:from-slate-100 group-hover:to-slate-200 transition-colors">
                <img src="{{ $figurinha->url_arquivo }}" alt="{{ $figurinha->titulo }}" class="absolute inset-0 w-full h-full object-contain p-4 drop-shadow-md group-hover:scale-110 transition-transform duration-300">
                <span class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full text-[10px] font-black text-slate-700 border border-slate-200/50 shadow-sm flex items-center gap-1">
                    {{ $figurinha->categoria->emoji() }} {{ $figurinha->categoria->label() }}
                </span>
            </a>
            
            <!-- Info Container -->
            <div class="p-4 flex flex-col flex-1 border-t border-slate-100">
                <a href="{{ route('figurinhas.show', $figurinha->slug) }}" class="text-sm font-bold text-slate-900 mb-1 truncate hover:text-green-600 transition-colors" title="{{ $figurinha->titulo }}">
                    {{ $figurinha->titulo }}
                </a>
                
                <div class="flex items-center gap-1 text-xs text-slate-500 font-medium mb-4">
                    <x-heroicon-s-arrow-down-tray class="w-3.5 h-3.5 text-slate-400" />
                    {{ number_format($figurinha->downloads, 0, '', '.') }} downloads
                </div>
                
                <div class="mt-auto grid grid-cols-2 gap-2">
                    <a href="{{ route('figurinhas.download', $figurinha->slug) }}" class="flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-slate-800 text-white py-2 rounded-xl text-xs font-bold transition-colors">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Baixar
                    </a>
                    <a href="https://wa.me/?text={{ urlencode('Olha essa figurinha legal! ' . route('figurinhas.show', $figurinha->slug)) }}" target="_blank" class="flex items-center justify-center gap-1.5 bg-green-50 hover:bg-green-100 text-green-700 py-2 rounded-xl text-xs font-bold transition-colors border border-green-200/50">
                        <x-heroicon-o-share class="w-4 h-4" /> Enviar
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $figurinhas->links() }}
    </div>
@endif

<x-adsense class="mt-8" />

@endsection

