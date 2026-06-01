@extends('layouts.app')

@section('title', $seoPage->title)
@section('description', $seoPage->meta_description)
@section('og_title', $seoPage->title)
@section('og_description', $seoPage->meta_description)

@section('content')

<x-schema-list :title="$seoPage->h1" :groups="$groups" />

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-slate-500 mb-6 font-semibold" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-green-600 transition-colors">Início</a>
    <span class="text-slate-300">›</span>
    @if ($category)
        <a href="{{ route('group.category', $category->slug) }}" class="hover:text-green-600 transition-colors flex items-center gap-1">
            <x-dynamic-component :component="$category->icon" class="w-4 h-4" /> {{ $category->name }}
        </a>
        <span class="text-slate-300">›</span>
    @endif
    <span class="text-slate-900 font-bold">{{ $seoPage->h1 }}</span>
</nav>

{{-- Cabeçalho --}}
<div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight mb-2 leading-tight">
        {{ $seoPage->h1 }}
    </h1>
    <p class="text-slate-500 text-sm sm:text-base leading-relaxed max-w-4xl">
        {{ $seoPage->content }}
    </p>
    <div class="mt-3 flex items-center gap-4 text-xs font-semibold text-slate-500">
        <span>Foco: <strong class="text-slate-800 capitalize">{{ $seoPage->keyword }}</strong></span>
        <span>{{ $groups->total() }} grupo{{ $groups->total() !== 1 ? 's' : '' }} listado{{ $groups->total() !== 1 ? 's' : '' }}</span>
    </div>
</div>

{{-- Grid de Grupos --}}
<div class="mb-6">
    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wider mb-4">
        Grupos Recomendados
    </h2>

    @if ($groups->isEmpty())
        <div class="text-center py-16 bg-white border border-slate-200 rounded-2xl shadow-sm">
            <h2 class="text-slate-900 font-bold text-xl mb-2">Nenhum grupo ativo cadastrado</h2>
            <p class="text-slate-500 text-sm mb-6">Seja o primeiro a enviar o seu grupo gratuitamente para esta listagem!</p>
            <a href="{{ route('send-group.create') }}" class="btn-primary inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-sm">
                <x-heroicon-o-plus class="w-5 h-5" /> Enviar Meu Grupo
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($groups as $group)
                <x-group-card :group="$group" />
            @endforeach
        </div>

        {{-- Paginação --}}
        @if ($groups->hasPages())
            <div class="mt-8 flex justify-center">
                <div class="pagination flex gap-2">
                    @if ($groups->onFirstPage())
                        <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed text-sm font-semibold">← Anterior</span>
                    @else
                        <a href="{{ $groups->previousPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-green-600 rounded-lg transition-colors text-sm font-semibold shadow-sm">← Anterior</a>
                    @endif

                    @for ($i = max(1, $groups->currentPage() - 2); $i <= min($groups->lastPage(), $groups->currentPage() + 2); $i++)
                        @if ($i === $groups->currentPage())
                            <span class="px-4 py-2 bg-green-500 text-white rounded-lg font-bold text-sm shadow-sm">{{ $i }}</span>
                        @else
                            <a href="{{ $groups->url($i) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-green-600 rounded-lg transition-colors text-sm font-semibold shadow-sm">{{ $i }}</a>
                        @endif
                    @endfor

                    @if ($groups->hasMorePages())
                        <a href="{{ $groups->nextPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-green-600 rounded-lg transition-colors text-sm font-semibold shadow-sm">Próximo →</a>
                    @else
                        <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed text-sm font-semibold">Próximo →</span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

{{-- Páginas Relacionadas — mesmo estilo da home "Tópicos e Assuntos mais Buscados" --}}
@if ($relatedPages->isNotEmpty())
    <section class="mt-12 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-2.5 inline-block">
                    Buscas Relacionadas
                </span>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Outras pessoas também buscaram por:
                </h3>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                    Explore outros grupos e comunidades relacionados a este tema.
                </p>
            </div>

            <div class="flex flex-wrap gap-2.5">
                @foreach ($relatedPages as $related)
                    @php
                        $cleanRelatedName = str_ireplace(
                            ['grupos de whatsapp de', 'grupos de whatsapp', 'grupo de whatsapp', 'grupos whatsapp', 'grupo whatsapp', 'no whatsapp', 'do whatsapp', 'de whatsapp', 'whatsapp', 'grupos de ', 'grupo de ', 'grupos ', 'grupo '],
                            '',
                            $related->keyword
                        );
                        $cleanRelatedName = trim($cleanRelatedName);
                    @endphp
                    <a href="{{ url('/grupos-whatsapp/' . $related->slug) }}"
                       class="px-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-bold text-slate-600 hover:bg-green-50 hover:text-primary hover:border-green-200 transition-all flex items-center gap-1.5 shadow-sm">
                        <x-heroicon-o-hashtag class="w-3.5 h-3.5 text-slate-400" /> <span class="capitalize">{{ $cleanRelatedName }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

<x-adsense class="mt-6" />
@endsection
