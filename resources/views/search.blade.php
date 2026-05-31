@extends('layouts.app')

@section('title', $query ? 'Busca: "' . $query . '" — WhatsGrupos' : 'Buscar Grupos — WhatsGrupos')
@section('description', 'Resultados da busca por "' . $query . '" no maior diretório de grupos de WhatsApp do Brasil.')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
        <x-heroicon-s-magnifying-glass class="w-6 h-6 text-slate-700" />
        <span>Buscar Grupos</span>
    </h1>
    @if ($query)
        <p class="text-slate-600 text-sm">
            {{ $groups->total() }} resultado{{ $groups->total() !== 1 ? 's' : '' }} para
            "<span class="text-[#25D366] font-semibold">{{ $query }}</span>"
        </p>
    @endif
</div>

<x-adsense class="mb-6" />

{{-- Barra de busca na página --}}
<form action="{{ route('group.search') }}" method="GET" class="mb-8">
    <div class="flex gap-3">
        <input type="text"
               name="q"
               id="search-page-input"
               value="{{ $query }}"
               placeholder="Buscar grupos por nome ou descrição..."
               autofocus
               class="flex-1 rounded-xl px-5 py-3.5 text-sm text-slate-900 border border-slate-200 bg-white focus:border-[#25D366] focus:outline-none transition-all hover:border-slate-300"
        />
        <button type="submit" class="bg-[#25D366] hover:bg-[#1DAC51] text-white font-bold px-6 rounded-xl transition-colors">Buscar</button>
    </div>
</form>

@if ($query)
    @if ($groups->isEmpty())
        <div class="text-center py-16">
            <div class="flex justify-center mb-4">
                <x-heroicon-o-face-frown class="w-16 h-16 text-slate-300" />
            </div>
            <h2 class="text-slate-900 font-bold text-xl mb-2">Nenhum resultado encontrado</h2>
            <p class="text-slate-600 text-sm mb-6">Tente outros termos ou explore pelas categorias.</p>
            <a href="{{ route('send-group.create') }}" class="bg-[#25D366] hover:bg-[#1DAC51] text-white font-bold px-6 py-3 rounded-xl transition-colors inline-flex items-center gap-2">➕ Adicionar Grupo</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($groups as $group)
                <x-group-card :group="$group" />
            @endforeach
        </div>

        @if ($groups->hasPages())
            <div class="mt-8 flex justify-center">
                <div class="flex gap-2 flex-wrap justify-center">
                    @if ($groups->onFirstPage())
                        <span class="px-4 py-2 border border-slate-200 rounded-lg text-slate-400 cursor-not-allowed">← Anterior</span>
                    @else
                        <a href="{{ $groups->previousPageUrl() }}" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-900 hover:border-[#25D366] hover:text-[#25D366] transition-colors">← Anterior</a>
                    @endif
                    @for ($i = max(1, $groups->currentPage() - 2); $i <= min($groups->lastPage(), $groups->currentPage() + 2); $i++)
                        @if ($i === $groups->currentPage())
                            <span class="px-4 py-2 bg-[#25D366] text-white rounded-lg font-bold">{{ $i }}</span>
                        @else
                            <a href="{{ $groups->url($i) }}" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-900 hover:border-[#25D366] hover:text-[#25D366] transition-colors">{{ $i }}</a>
                        @endif
                    @endfor
                    @if ($groups->hasMorePages())
                        <a href="{{ $groups->nextPageUrl() }}" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-900 hover:border-[#25D366] hover:text-[#25D366] transition-colors">Próximo →</a>
                    @else
                        <span class="px-4 py-2 border border-slate-200 rounded-lg text-slate-400 cursor-not-allowed">Próximo →</span>
                    @endif
                </div>
            </div>
        @endif
    @endif
@else
    {{-- Sugestão de categorias quando não há query --}}
    <div>
        <h2 class="text-slate-900 font-bold text-lg mb-4 flex items-center gap-2">
            <x-heroicon-s-folder-open class="w-5 h-5 text-slate-700" />
            <span>Explorar por Categoria</span>
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @foreach ($categories as $cat)
                <a href="{{ route('group.category', $cat->slug) }}"
                   class="group flex flex-col items-center gap-2 p-4 rounded-2xl border border-slate-200 bg-white hover:border-[#25D366] hover:bg-green-50 transition-all text-center">
                    @php
                        $heroiconName = \App\Models\Category::getHeroiconBySlug($cat->slug);
                    @endphp
                    <x-dynamic-component :component="$heroiconName" class="w-8 h-8 text-slate-700 group-hover:text-[#25D366] transition-colors" />
                    <span class="text-slate-600 text-xs font-medium leading-tight group-hover:text-slate-900 transition-colors">{{ $cat->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

@endsection
