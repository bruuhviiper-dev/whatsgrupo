@extends('layouts.app')

@section('title', $category->name . ' — Grupos de WhatsApp')
@section('description', 'Encontre os melhores grupos de WhatsApp de ' . $category->name . '. Participe de discussões, faça amizades e interaja gratuitamente!')

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

@endsection
