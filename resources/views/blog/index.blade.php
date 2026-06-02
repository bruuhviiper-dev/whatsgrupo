@extends('layouts.blog')

@section('title', 'Blog WhatsGrupos — Dicas, Dúvidas e Tutoriais de WhatsApp')
@section('description', 'Aprenda como entrar, criar, divulgar e gerenciar grupos e canais do WhatsApp. Dicas de segurança, engajamento e as últimas novidades de 2026.')

@section('content')

{{-- Structured data: BreadcrumbList + ItemList dos artigos --}}
<x-seo.breadcrumbs :items="[
    ['name' => 'Início', 'url' => url('/')],
    ['name' => 'Blog', 'url' => url('/blog')],
]" />
@php
    $postItems = collect($posts)->map(fn ($p) => [
        'name' => $p->title,
        'url'  => url('/blog/' . $p->slug),
    ])->all();
@endphp
@if(count($postItems))
<x-seo.itemlist name="Blog WhatsGrupos" :items="$postItems" />
@endif

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-slate-500 pt-5 mb-6" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
    <span class="text-slate-300">›</span>
    <span class="text-slate-900 font-medium">Blog</span>
</nav>

{{-- Hero Section --}}
<div class="mb-10 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm relative overflow-hidden">
    <div class="absolute top-0 right-0 w-80 h-80 bg-green-50 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 left-1/3 w-64 h-64 bg-slate-50 rounded-full blur-2xl -z-10"></div>
    
    <div class="max-w-3xl">
        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-3 inline-block">
            {{ empty($query) ? 'Central de Ajuda & Conhecimento' : 'Resultados de Busca' }}
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">
            {{ empty($query) ? 'Blog WhatsGrupos' : 'Resultados para: "' . $query . '"' }}
        </h1>
        <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
            {{ empty($query) 
                ? 'Dicas essenciais, guias passo a passo, tutoriais de segurança e tudo o que você precisa saber para criar, gerenciar e impulsionar grupos e canais do WhatsApp com sucesso em 2026.' 
                : 'Encontramos ' . $posts->total() . ' artigo(s) correspondente(s) à sua pesquisa de ajuda.' }}
        </p>
    </div>
</div>

<x-adsense class="mb-10" />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Coluna de Posts --}}
    <div class="lg:col-span-2">
        <div class="grid grid-cols-1 gap-6">
            @forelse($posts as $post)
                <x-blog-card :post="$post" />
                @if($loop->iteration % 2 == 0)
                    <div class="my-6 space-y-6">
                        <x-adsense />
                        <x-publish-invite />
                    </div>
                @endif
            @empty
                <div class="col-span-full text-center py-20 border border-slate-100 rounded-2xl bg-white shadow-sm flex flex-col items-center">
                    <x-heroicon-o-magnifying-glass class="w-12 h-12 text-slate-300 mb-4" />
                    <h3 class="text-slate-900 font-bold text-lg mb-1">Nenhum artigo encontrado</h3>
                    <p class="text-slate-500 text-sm">Tente pesquisar por outros termos no buscador do blog.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if ($posts->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $posts->links('components.pagination') }}
            </div>
        @endif
    </div>

    {{-- Sidebar Lateral --}}
    <aside class="space-y-6 lg:sticky lg:top-8 h-max">
        <x-adsense class="!mt-0 !mb-0" />
        {{-- Widget de Cadastro --}}
        <div class="bg-[#25D366] text-white p-6 rounded-2xl shadow-sm relative overflow-hidden">
            <div class="absolute right-0 bottom-0 opacity-10 translate-x-4 translate-y-4">
                <x-heroicon-s-rocket-launch class="w-36 h-36" />
            </div>
            <h3 class="font-extrabold text-lg mb-2 relative z-10">Tem um Grupo de WhatsApp?</h3>
            <p class="text-white/90 text-xs sm:text-sm leading-relaxed mb-4 relative z-10">
                Divulgue gratuitamente na maior comunidade do Brasil! Atraia novos membros qualificados de forma 100% automatizada.
            </p>
            <a href="{{ route('send-group.create') }}" class="inline-block bg-white text-emerald-800 font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-lg shadow-md hover:bg-slate-50 transition-colors relative z-10">
                ➕ Divulgar Meu Grupo
            </a>
        </div>

        {{-- Widget VIP --}}
        <div class="bg-gradient-to-br from-amber-400 to-yellow-600 text-white p-6 rounded-2xl shadow-sm relative overflow-hidden">
            <div class="absolute right-0 bottom-0 opacity-10 translate-x-4 translate-y-4">
                <x-heroicon-s-star class="w-36 h-36" />
            </div>
            <h3 class="font-extrabold text-lg mb-2 relative z-10">Destaque seu Grupo com VIP</h3>
            <p class="text-white/90 text-xs sm:text-sm leading-relaxed mb-4 relative z-10">
                Coloque seu grupo no topo das pesquisas e da página principal para decolar a quantidade de novos participantes.
            </p>
            <a href="{{ route('boost.packages') }}" class="inline-block bg-slate-900 text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-lg shadow-md hover:bg-slate-800 transition-colors relative z-10">
                ⭐ Conhecer Planos
            </a>
        </div>

        {{-- Widget Categorias Principais --}}
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-wider mb-4">
                📂 Categorias Recomendadas
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($blogCategories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" class="px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-600 hover:bg-green-50 hover:text-[#25D366] hover:border-green-200 transition-colors text-xs font-semibold flex items-center gap-1.5">
                        <x-dynamic-component :component="$cat->icon ?? 'heroicon-o-folder'" class="w-3.5 h-3.5" /> {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </aside>
</div>

@endsection
