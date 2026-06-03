@extends('layouts.app')

@section('title', '+' . $groups->total() . ' Links de Grupos de ' . $category->name . ' para WhatsApp (Atualizados em ' . date('Y') . ')')
@section('description', 'Encontre os melhores grupos de WhatsApp de ' . $category->name . ' ativos em ' . date('Y') . '. ' . $groups->total() . ' grupos verificados com link de convite direto. Participe grátis!')

@push('head')
@php
    $catUrl   = url('/categoria/' . $category->slug);
    $curPage  = $groups->currentPage();
    $lastPage = $groups->lastPage();
@endphp
{{-- Canonical auto-referencial por página (Google prefere self-referential pós-2019) --}}
@if($curPage > 1)
<link rel="canonical" href="{{ $catUrl }}?page={{ $curPage }}">
@endif
{{-- rel prev/next: ajuda o Google a entender a série paginada --}}
@if($curPage > 1)
<link rel="prev" href="{{ $catUrl }}{{ $curPage === 2 ? '' : '?page=' . ($curPage - 1) }}">
@endif
@if($curPage < $lastPage)
<link rel="next" href="{{ $catUrl }}?page={{ $curPage + 1 }}">
@endif
@if($curPage > 1)
<meta name="robots" content="noindex, follow">
@endif
@endpush

@section('content')

{{-- Breadcrumb estilo moderno --}}
<nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
  <a href="/" class="hover:text-slate-900 transition-colors">Início</a>
  <span class="text-slate-300">/</span>
  <span class="text-slate-900 font-semibold">{{ $category->name }}</span>
</nav>

{{-- Cabeçalho da Categoria --}}
<div class="mb-8 flex items-center gap-3">
  <div class="p-3 bg-slate-100 rounded-xl flex items-center justify-center w-14 h-14">
    @if($category->icon && str_starts_with($category->icon, 'heroicon'))
      <x-dynamic-component :component="$category->icon" class="w-8 h-8 text-slate-700" />
    @elseif($category->icon)
      <span class="text-3xl select-none leading-none">{{ $category->icon }}</span>
    @else
      <x-heroicon-o-folder class="w-8 h-8 text-slate-700" />
    @endif
  </div>
  <div>
    <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight mb-1">
      Grupos de {{ $category->name }}
    </h1>
    <p class="text-sm text-slate-500">
      Temos <span class="text-slate-900 font-bold">{{ $groups->total() }}</span> grupo{{ $groups->total() !== 1 ? 's' : '' }} cadastrado{{ $groups->total() !== 1 ? 's' : '' }} ativamente nesta categoria
    </p>
  </div>
</div>

{{-- Banner de Patrocinador --}}
@if ($category->sponsoredCategory && $category->sponsoredCategory->is_active && $category->sponsoredCategory->starts_at->isPast() && $category->sponsoredCategory->ends_at->isFuture())
    <div class="relative flex flex-col md:flex-row items-center justify-between gap-6 p-6 rounded-2xl border border-green-200 mb-8 overflow-hidden bg-green-50">
        <div class="absolute top-3 right-3 bg-green-200 text-green-900 text-[9px] font-black uppercase px-2.5 py-0.5 rounded-md tracking-wider">
            Anúncio
        </div>
        <div class="flex items-center gap-4 flex-1">
            @if ($category->sponsoredCategory->banner_path)
                <img src="{{ Storage::url($category->sponsoredCategory->banner_path) }}" alt="{{ $category->sponsoredCategory->sponsor_name }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200 flex-shrink-0">
            @else
                <div class="w-16 h-16 rounded-xl flex items-center justify-center bg-green-100 flex-shrink-0">
                    <x-heroicon-o-megaphone class="w-8 h-8 text-green-600" />
                </div>
            @endif
            <div>
                <h3 class="text-slate-900 font-black text-base">{{ $category->sponsoredCategory->sponsor_name }}</h3>
                <p class="text-xs text-slate-600 mt-1">Oferta premium selecionada recomendada para você!</p>
            </div>
        </div>
        <a href="{{ $category->sponsoredCategory->link_url }}" target="_blank" rel="noopener noreferrer nofollow"
           class="bg-[#25D366] hover:bg-[#20bd5a] text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-wider text-center transition-colors whitespace-nowrap shadow-sm inline-flex items-center gap-2">
            Acessar Oferta <x-heroicon-m-arrow-top-right-on-square class="w-4 h-4" />
        </a>
    </div>
@endif

<!-- GRID DE CARDS RESPONSIVO -->
<x-adsense class="mb-6" />
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
  @forelse($groups as $group)
    @include('components.group-card', ['group' => $group])
  @empty
    <div class="col-span-full text-center py-20 border border-slate-100 rounded-2xl bg-white shadow-sm">
      <div class="flex justify-center mb-4">
        <x-heroicon-o-inbox class="w-12 h-12 text-slate-300" />
      </div>
      <h3 class="text-slate-900 font-bold text-lg mb-1">Nenhum grupo encontrado</h3>
      <p class="text-slate-500 text-sm">Seja o primeiro a enviar um grupo para esta categoria clicando no botão acima!</p>
    </div>
  @endforelse
</div>

<!-- PAGINAÇÃO -->
<div class="flex justify-center mt-12">
  {{ $groups->onEachSide(2)->links('components.pagination') }}
</div>

<!-- BLOCO DE TEXTO SEO DA CATEGORIA -->
<section class="mt-12 p-6 md:p-8 rounded-2xl bg-white border border-slate-200 shadow-sm">
  <h2 class="text-lg font-bold text-slate-900 mb-3">Grupos de WhatsApp de {{ $category->name }}</h2>
  <p class="text-slate-600 text-sm leading-relaxed">
    Encontre aqui os melhores <strong>grupos de WhatsApp de {{ $category->name }}</strong> ativos do Brasil, com links
    de convite verificados pelo nosso validador automático. Entre nos grupos com um clique, participe das conversas e
    faça parte de comunidades reais sobre {{ Str::lower($category->name) }}. Novos grupos são adicionados todos os dias —
    volte sempre para conferir as novidades. Tem um grupo de {{ Str::lower($category->name) }}?
    <a href="/enviar-grupo" class="text-primary font-semibold hover:underline">Cadastre-o gratuitamente</a> e alcance
    milhares de pessoas. Veja também os
    <a href="/grupos-novos" class="text-primary font-semibold hover:underline">grupos mais novos</a> e os
    <a href="/grupos-mais-populares" class="text-primary font-semibold hover:underline">mais populares</a> do portal.
  </p>
</section>

{{-- FAQ da categoria: visual accordion + JSON-LD FAQPage (captura queries informacionais) --}}
<x-category-faq :category="$category" />

{{-- Tópicos relacionados (cauda longa) da categoria: internal linking para as
     páginas /grupos-whatsapp/* — funila autoridade da categoria para os tópicos SEO. --}}
@if(isset($relatedSeoPages) && $relatedSeoPages->isNotEmpty())
<section class="mt-8 p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
        <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-500" />
        Buscas populares de {{ $category->name }}
    </h2>
    <div class="flex flex-wrap gap-2">
        @foreach($relatedSeoPages as $page)
            @php
                $cleanName = str_ireplace(
                    ['grupos de whatsapp de', 'grupos de whatsapp', 'grupo de whatsapp', 'grupos whatsapp', 'grupo whatsapp', 'no whatsapp', 'do whatsapp', 'de whatsapp', 'whatsapp', 'grupos de ', 'grupo de ', 'grupos ', 'grupo '],
                    '',
                    $page->keyword
                );
                $cleanName = trim($cleanName);
            @endphp
            <a href="/grupos-whatsapp/{{ $page->slug }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-slate-50 hover:bg-green-50 hover:border-green-300 hover:text-green-800 text-slate-700 text-xs font-semibold transition-all">
                Grupos de <span class="capitalize">{{ $cleanName ?: $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Internal linking: outras categorias com grupos ativos (SEO cross-linking) --}}
@if(isset($categories) && $categories->count() > 1)
@php
    $related = $categories->where('id', '!=', $category->id)
        ->where('groups_count', '>', 0)
        ->sortByDesc('groups_count')
        ->take(10);
@endphp
@if($related->count())
<section class="mt-8 p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
        <x-heroicon-o-squares-2x2 class="w-4 h-4 text-slate-500" />
        Explore outras categorias
    </h2>
    <div class="flex flex-wrap gap-2">
        @foreach($related as $rel)
            <a href="{{ url('/categoria/' . $rel->slug) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-slate-50 hover:bg-green-50 hover:border-green-300 hover:text-green-800 text-slate-700 text-xs font-semibold transition-all">
                @if($rel->icon && str_starts_with($rel->icon, 'heroicon'))
                    <x-dynamic-component :component="$rel->icon" class="w-4 h-4 text-slate-500" />
                @elseif($rel->icon)
                    <span class="text-sm leading-none">{{ $rel->icon }}</span>
                @endif
                {{ $rel->name }}
                <span class="text-slate-400 text-[10px]">({{ $rel->groups_count }})</span>
            </a>
        @endforeach
    </div>
</section>
@endif
@endif

{{-- Structured data: ItemList dos grupos + BreadcrumbList --}}
@if($groups->count())
  <x-schema-list :title="'Grupos de WhatsApp de ' . $category->name" :groups="$groups->getCollection()" />
@endif
<x-seo.breadcrumbs :items="[
    ['name' => 'Início', 'url' => url('/')],
    ['name' => 'Grupos de ' . $category->name, 'url' => url('/categoria/' . $category->slug)],
]" />

@endsection
