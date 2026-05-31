@extends('layouts.app')

@section('title', 'Mais Categorias Especiais de WhatsApp — WhatsGrupos')
@section('description', 'Filtre e descubra links de grupos de WhatsApp focados em assuntos específicos e termos muito buscados no Brasil, como Palmeiras, Figurinhas, K-pop e muito mais.')

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
    <span class="text-slate-300">›</span>
    <span class="text-slate-900 font-medium">Assuntos Especiais</span>
</nav>

{{-- Hero Section --}}
<div class="mb-10 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm relative overflow-hidden">
    <div class="absolute top-0 right-0 w-80 h-80 bg-green-50 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-slate-50 rounded-full blur-2xl -z-10"></div>
    
    <div class="max-w-3xl">
        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-3 inline-block">
            Explorar Nichos de Discussão
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">
            Assuntos e Categorias Especiais
        </h1>
        <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
            Precisa de um grupo focado em um tema super específico que não está nas categorias normais? Pesquise e entre nas comunidades mais ativas do Brasil mapeadas dinamicamente abaixo.
        </p>
    </div>
</div>

{{-- Grid of Special SEO Categories --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
            <span>🔥 Termos e Páginas de Alta Relevância</span>
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($seoPages as $page)
                @php
                    $cleanTerm = str_ireplace(['no WhatsApp', 'do WhatsApp', 'no whatsapp', 'do whatsapp', 'WhatsApp de', 'WhatsApp'], '', $page->keyword);
                    $cleanTerm = trim($cleanTerm);
                @endphp
                <a href="{{ url('/grupos-whatsapp/' . $page->slug) }}" 
                   class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-50 text-emerald-600 flex items-center justify-center font-bold text-lg group-hover:bg-[#25D366] group-hover:text-white transition-colors">
                            ⚡
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-sm group-hover:text-primary transition-colors">
                                Grupos de {{ $cleanTerm }}
                            </h3>
                            <p class="text-slate-400 text-[11px]">
                                {{ number_format($page->views) }} buscas mensais estimadas
                            </p>
                        </div>
                    </div>
                    <x-heroicon-m-chevron-right class="w-5 h-5 text-slate-300 group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
                </a>
            @empty
                <div class="col-span-full text-center py-20 border border-slate-100 rounded-2xl bg-white shadow-sm">
                    <p class="text-5xl mb-4">🔍</p>
                    <h3 class="text-slate-900 font-bold text-lg mb-1">Nenhum assunto cadastrado</h3>
                    <p class="text-slate-500 text-sm">Volte mais tarde para novas listagens especiais.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Widget Divulgar --}}
        <div class="bg-[#25D366] text-white p-6 rounded-3xl shadow-sm relative overflow-hidden">
            <div class="absolute right-0 bottom-0 opacity-10 translate-x-4 translate-y-4">
                <x-heroicon-s-rocket-launch class="w-36 h-36" />
            </div>
            <h3 class="font-extrabold text-lg mb-2 relative z-10">Seu Grupo no WhatsGrupos</h3>
            <p class="text-white/90 text-xs sm:text-sm leading-relaxed mb-4 relative z-10">
                Divulgue hoje mesmo seu link de convite e apareça em nossas buscas inteligentes de categorias e termos especiais do Brasil!
            </p>
            <a href="{{ route('send-group.create') }}" class="inline-block bg-white text-emerald-800 font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-lg shadow-md hover:bg-slate-50 transition-colors relative z-10">
                ➕ Enviar Meu Grupo
            </a>
        </div>

        {{-- Widget Explicação Termos --}}
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-wider mb-3">
                🎯 Como funcionam?
            </h3>
            <p class="text-xs text-slate-500 leading-relaxed space-y-2">
                As nossas páginas de assuntos especiais realizam uma correspondência textual inteligente baseada no nome e descrição de grupos cadastrados. 
                <br><br>
                Isso assegura que, mesmo que o grupo não tenha sido cadastrado com tags explícitas, ele seja sugerido para o público interessado de forma dinâmica e precisa.
            </p>
        </div>
    </div>
</div>

@endsection
