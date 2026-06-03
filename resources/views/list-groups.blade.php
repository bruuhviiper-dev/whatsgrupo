@extends('layouts.app')

@section('title', $title . ' — WhatsGrupos')
@section('description', $description)

@section('content')

<x-schema-list :title="$title" :groups="$groups" />

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-text-muted mb-6" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-secondary transition-colors">Início</a>
    <span class="text-white/20">›</span>
    <span class="text-text-main font-medium">{{ $title }}</span>
</nav>

{{-- Cabeçalho --}}
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-black text-text-main leading-tight mb-2">
        {{ $title }}
    </h1>
    <p class="text-text-muted text-sm sm:text-base leading-relaxed">
        {{ $description }}
    </p>
</div>

{{-- Filtros de Categorias --}}
@if(isset($categories) && $categories->isNotEmpty())
<div class="flex overflow-x-auto gap-2 mb-8 pb-2 scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap sm:overflow-visible">
    <a href="{{ route('home') }}" 
       class="whitespace-nowrap flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all border bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50">
       Todos
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('group.category', $cat->slug) }}" 
           class="whitespace-nowrap flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all border bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50 flex items-center gap-1.5">
           @if($cat->icon && str_starts_with($cat->icon, 'heroicon'))
               <x-dynamic-component :component="$cat->icon" class="w-3.5 h-3.5" />
           @elseif($cat->icon)
               <span>{{ $cat->icon }}</span>
           @endif
           {{ $cat->name }}
        </a>
    @endforeach
</div>
@endif

{{-- Grid de Grupos --}}
@if ($groups->isEmpty())
    <div class="text-center py-20 bg-card border border-white/5 rounded-2xl flex flex-col items-center">
        <x-heroicon-o-inbox class="w-12 h-12 text-slate-300 mb-4" />
        <h2 class="text-text-main font-bold text-xl mb-2">Nenhum grupo encontrado</h2>
        <p class="text-text-muted text-sm mb-6">Parece que não temos grupos cadastrados para este critério no momento.</p>
        <a href="{{ route('send-group.create') }}" class="btn-primary inline-flex items-center justify-center gap-2">
            <x-heroicon-m-plus class="w-4 h-4" /> Cadastrar Novo Grupo
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($groups as $group)
            <x-group-card :group="$group" />
        @endforeach
    </div>

    {{-- Paginação responsiva (mesmo componente da home e categorias) --}}
    @if ($groups->hasPages())
        <div class="mt-10 max-w-xl mx-auto">
            {{ $groups->onEachSide(1)->links('components.pagination') }}
        </div>
    @endif
@endif

@endsection
