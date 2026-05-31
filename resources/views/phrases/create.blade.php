@extends('layouts.phrases')

@section('title', 'Enviar Frase para Status de WhatsApp | WhatsGrupos')
@section('description', 'Compartilhe sua frase inspiradora, de amor, reflexão ou motivação com a nossa comunidade e deixe sua voz ecoar.')

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
                        <span class="text-slate-400">Enviar Frase</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="text-center mb-8 pt-2">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 mb-2">Enviar Frase para Status de WhatsApp</h1>
            <p class="text-slate-500 text-sm">Compartilhe a sua frase para status e deixe sua voz ecoar em todas as redes sociais.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-sm">
            <form action="{{ route('phrases.submit') }}" method="POST" class="space-y-5">
                @csrf
                
                {{-- Frase --}}
                <div class="space-y-1.5">
                    <label class="text-slate-600 font-bold text-xs uppercase block">Frase</label>
                    <textarea name="phrase" rows="4" required placeholder="Insira a sua frase"
                              class="w-full bg-white border border-slate-300 rounded-lg px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">{{ old('phrase') }}</textarea>
                </div>

                {{-- Autor --}}
                <div class="space-y-1.5">
                    <label class="text-slate-600 font-bold text-xs uppercase block">Autor</label>
                    <input type="text" name="author" value="{{ old('author') }}" placeholder="Insira o autor"
                           class="w-full bg-white border border-slate-300 rounded-lg px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                </div>

                {{-- Categoria --}}
                <div class="space-y-1.5">
                    <label class="text-slate-600 font-bold text-xs uppercase block">Categoria</label>
                    <select name="category" required
                            class="w-full bg-white border border-slate-300 rounded-lg px-4 py-3 text-sm text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                        <option value="">Selecione uma categoria...</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['slug'] }}" {{ old('category') == $cat['slug'] ? 'selected' : '' }}>
                                {{ $cat['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="bg-[#25D366] hover:bg-[#1da851] text-white font-bold w-full py-3.5 rounded-lg text-sm transition-colors mt-2">
                    Enviar frase
                </button>
            </form>
        </div>
    </div>

    @include('phrases.partials.sidebar-right')
</div>
@endsection
