@extends('layouts.app')

@section('title', 'Gerador de Widget de Grupos de WhatsApp | WhatsGrupos')
@section('description', 'Crie um widget elegante de grupos de WhatsApp para embutir no seu site gratuitamente. Aumente seu engajamento com nossa lista responsiva.')

@section('content')
<div class="space-y-6" x-data="{ 
    category: 'all', 
    height: '490', 
    width: '100%', 
    copied: false,
    get embedCode() {
        return `<script src=&quot;{{ url('/widget.js') }}?category=${this.category}&height=${this.height}&width=${this.width}&quot;></script>`;
    },
    get previewUrl() {
        return `{{ url('/widget') }}/${this.category}`;
    }
}">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 flex-wrap" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-[#25D366] transition-colors">Início</a>
        <span class="text-slate-900/20">›</span>
        <span class="text-slate-900 font-medium">Gerador de Widget</span>
    </nav>

    {{-- Intro Banner --}}
    <div class="rounded-2xl p-6 border border-slate-200 relative overflow-hidden" style="background: linear-gradient(135deg, rgba(108,63,197,0.15), rgba(0,200,150,0.05));">
        <div class="relative z-10 max-w-3xl">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">Widget de Grupos para seu Site</h1>
            <p class="text-slate-500 text-sm leading-relaxed">
                Leve o conteúdo premium do <strong class="text-[#25D366]">WhatsGrupos</strong> para o seu blog, portal ou fórum! Configure a categoria dos grupos, escolha as dimensões ideais e incorpore o script no seu código HTML de forma 100% gratuita.
            </p>
        </div>
    </div>

    <x-adsense class="mb-4" />

    {{-- Grid do Gerador --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Formulário de Configuração --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
                <h2 class="text-slate-900 font-black text-lg border-b border-slate-200 pb-3"><x-heroicon-o-cog-6-tooth class="w-5 h-5 inline-block mr-1 text-slate-600 align-text-bottom" /> Configurações</h2>

                {{-- Categoria --}}
                <div class="space-y-2">
                    <label class="text-slate-900 font-bold text-xs uppercase tracking-wider block">1. Escolha a Categoria</label>
                    <select x-model="category"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:border-secondary transition-colors">
                        <option value="all">📁 Todas as Categorias (Geral)</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-slate-500 text-[11px] leading-relaxed">
                        Selecione uma categoria específica para atrair membros com interesses específicos ou mantenha geral para maior diversidade.
                    </p>
                </div>

                {{-- Largura --}}
                <div class="space-y-2">
                    <label class="text-slate-900 font-bold text-xs uppercase tracking-wider block">2. Largura do Widget</label>
                    <input type="text" x-model="width"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:border-secondary font-mono transition-colors"
                           placeholder="Ex: 100%, 350px, etc.">
                    <p class="text-slate-500 text-[11px]">Recomendamos manter <code class="text-[#25D366]">100%</code> para o widget se auto-adaptar na coluna do seu site.</p>
                </div>

                {{-- Altura --}}
                <div class="space-y-2">
                    <label class="text-slate-900 font-bold text-xs uppercase tracking-wider block">3. Altura (px)</label>
                    <div class="flex items-center gap-4">
                        <input type="range" min="300" max="800" step="10" x-model="height"
                               class="flex-1 accent-secondary bg-slate-50 h-2 rounded-lg cursor-pointer">
                        <span class="text-sm font-bold text-[#25D366] font-mono w-14 text-right" x-text="height + 'px'"></span>
                    </div>
                    <p class="text-slate-500 text-[11px]">Recomendamos <code class="text-[#25D366]">490px</code> para exibir a lista de 6 grupos sem barras de rolagem.</p>
                </div>
            </div>

            {{-- Código de Incorporação --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                <h3 class="text-slate-900 font-black text-base flex items-center gap-2">
                    <x-heroicon-o-code-bracket class="w-5 h-5 text-slate-600" /> Código HTML Gerado
                </h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Copie o código abaixo e cole no local desejado do código HTML do seu site (sidebar, fim dos artigos, etc.):
                </p>
                <div class="relative bg-slate-100 border border-slate-200 rounded-xl p-4">
                    <pre class="text-[#25D366] font-mono text-xs whitespace-pre-wrap select-all pr-12 leading-relaxed" x-text="embedCode"></pre>
                    <button @click="navigator.clipboard.writeText(embedCode); copied = true; setTimeout(() => copied = false, 2500)"
                            class="absolute top-2 right-2 bg-secondary hover:bg-secondary-hover text-black p-2 rounded-lg transition-all"
                            title="Copiar código">
                        <span x-show="!copied" class="text-xs font-black px-1">Copiar</span>
                        <span x-show="copied" class="text-xs font-black px-1">Copiado!</span>
                    </button>
                </div>
                <div class="bg-primary/5 border border-primary/20 rounded-xl p-3 text-[11px] text-slate-500 flex gap-2">
                    <x-heroicon-o-light-bulb class="w-4 h-4 text-primary flex-shrink-0" />
                    <span>Nosso widget é leve, carrega de forma assíncrona e não interfere na velocidade de carregamento de sua página.</span>
                </div>
            </div>
        </div>

        {{-- Preview Ao Vivo --}}
        <div class="lg:col-span-7 space-y-4">
            <h2 class="text-slate-900 font-black text-lg flex items-center gap-2">
                <x-heroicon-o-eye class="w-5 h-5 text-slate-600" /> Preview em Tempo Real
            </h2>
            <div class="border border-slate-200 rounded-2xl p-4 bg-slate-100 flex items-center justify-center min-h-[520px]">
                {{-- Elemento Iframe de visualização controlada por Alpine --}}
                <iframe :src="previewUrl" :style="{ width: width, height: height + 'px' }"
                        class="border-none rounded-2xl shadow-2xl bg-bg transition-all duration-300 overflow-hidden"
                        scrolling="no"
                        frameborder="0"
                        allowtransparency="true">
                </iframe>
            </div>
            <p class="text-center text-slate-500 text-xs">
                O widget acima é exatamente como ele aparecerá no seu site! Teste alterar a categoria para ver os grupos mudando.
            </p>
        </div>
    </div>
</div>
@endsection
