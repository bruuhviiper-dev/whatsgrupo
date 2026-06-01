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
        <span class="text-slate-300">/</span>
        <span class="text-slate-900 font-medium">Gerador de Widget</span>
    </nav>

    {{-- Hero Banner --}}
    <div class="rounded-2xl p-6 md:p-8 border border-[#25D366]/20 relative overflow-hidden" style="background: linear-gradient(135deg, rgba(37,211,102,0.08), rgba(18,140,126,0.05));">
        <div class="relative z-10 max-w-3xl">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-o-code-bracket-square class="w-7 h-7 text-[#25D366]" />
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Widget de Grupos para seu Site</h1>
            </div>
            <p class="text-slate-500 text-sm leading-relaxed">
                Leve o conteúdo do <strong class="text-[#25D366]">WhatsGrupos</strong> para o seu blog, portal ou fórum! Configure a categoria, escolha as dimensões e incorpore o script no seu HTML de forma 100% gratuita.
            </p>
        </div>
    </div>

    <x-adsense class="mb-4" />

    {{-- Grid do Gerador --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Formulário de Configuração --}}
        <div class="lg:col-span-5 space-y-5">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <h2 class="text-slate-900 font-black text-base flex items-center gap-2 border-b border-slate-100 pb-4">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-slate-500" />
                    Configurações
                </h2>

                {{-- Categoria --}}
                <div class="space-y-2">
                    <label class="text-slate-900 font-bold text-xs uppercase tracking-wider block">
                        1. Escolha a Categoria
                    </label>

                    {{-- Picker customizado com ícones Heroicons --}}
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                        <div class="max-h-[220px] overflow-y-auto p-2 grid grid-cols-2 gap-1.5"
                             style="scrollbar-width: thin; scrollbar-color: #25D366 #f1f5f9;">
                            {{-- Opção "Todas" --}}
                            <button type="button" @click="category = 'all'"
                                    :class="category === 'all'
                                        ? 'bg-[#25D366]/10 border-[#25D366] text-[#25D366]'
                                        : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300 hover:text-slate-900'"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg border text-xs font-semibold transition-all text-left w-full">
                                <x-heroicon-o-squares-2x2 class="w-4 h-4 shrink-0" />
                                <span class="truncate">Todas</span>
                            </button>

                            @foreach ($categories as $cat)
                                <button type="button" @click="category = '{{ $cat->slug }}'"
                                        :class="category === '{{ $cat->slug }}'
                                            ? 'bg-[#25D366]/10 border-[#25D366] text-[#25D366]'
                                            : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300 hover:text-slate-900'"
                                        class="flex items-center gap-2 px-3 py-2 rounded-lg border text-xs font-semibold transition-all text-left w-full">
                                    @if ($cat->icon && str_starts_with($cat->icon, 'heroicon'))
                                        <x-dynamic-component :component="$cat->icon" class="w-4 h-4 shrink-0" />
                                    @else
                                        <x-heroicon-o-folder class="w-4 h-4 shrink-0" />
                                    @endif
                                    <span class="truncate">{{ $cat->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <p class="text-slate-500 text-[11px] leading-relaxed">
                        Selecione uma categoria para atrair membros com interesses específicos, ou mantenha <strong>Todas</strong> para maior diversidade.
                    </p>
                </div>

                {{-- Largura --}}
                <div class="space-y-2">
                    <label class="text-slate-900 font-bold text-xs uppercase tracking-wider block">
                        2. Largura do Widget
                    </label>
                    <input type="text" x-model="width"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:border-[#25D366] font-mono transition-colors"
                           placeholder="Ex: 100%, 350px…">
                    <p class="text-slate-500 text-[11px]">
                        Recomendamos <code class="text-[#25D366] font-bold">100%</code> para o widget se adaptar à coluna do seu site.
                    </p>
                </div>

                {{-- Altura --}}
                <div class="space-y-2">
                    <label class="text-slate-900 font-bold text-xs uppercase tracking-wider block">
                        3. Altura (px)
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="range" min="300" max="800" step="10" x-model="height"
                               class="flex-1 accent-[#25D366] h-2 rounded-lg cursor-pointer">
                        <span class="text-sm font-black text-[#25D366] font-mono w-16 text-right" x-text="height + 'px'"></span>
                    </div>
                    <p class="text-slate-500 text-[11px]">
                        Recomendamos <code class="text-[#25D366] font-bold">490px</code> para exibir 6 grupos sem barra de rolagem.
                    </p>
                </div>
            </div>

            {{-- Código de Incorporação --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-slate-900 font-black text-base flex items-center gap-2">
                    <x-heroicon-o-code-bracket class="w-5 h-5 text-slate-500" />
                    Código HTML Gerado
                </h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Cole o código abaixo no HTML do seu site (sidebar, rodapé de artigos, etc.):
                </p>
                <div class="relative bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <pre class="text-[#25D366] font-mono text-xs whitespace-pre-wrap select-all pr-14 leading-relaxed" x-text="embedCode"></pre>
                    <button @click="navigator.clipboard.writeText(embedCode); copied = true; setTimeout(() => copied = false, 2500)"
                            class="absolute top-2 right-2 bg-[#25D366] hover:bg-[#20bd5a] text-white px-3 py-1.5 rounded-lg transition-all text-xs font-black"
                            title="Copiar código">
                        <span x-show="!copied">Copiar</span>
                        <span x-show="copied">Copiado!</span>
                    </button>
                </div>
                <div class="bg-[#25D366]/5 border border-[#25D366]/20 rounded-xl p-3 text-[11px] text-slate-500 flex gap-2 items-start">
                    <x-heroicon-o-light-bulb class="w-4 h-4 text-[#25D366] flex-shrink-0 mt-0.5" />
                    <span>O widget é leve, carrega de forma assíncrona e não impacta a velocidade da sua página.</span>
                </div>
            </div>
        </div>

        {{-- Preview Ao Vivo --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center gap-2">
                <x-heroicon-o-eye class="w-5 h-5 text-slate-500" />
                <h2 class="text-slate-900 font-black text-base">Preview em Tempo Real</h2>
            </div>
            <div class="border border-slate-200 rounded-2xl p-4 bg-slate-100 flex items-center justify-center min-h-[520px] shadow-sm">
                <iframe :src="previewUrl" :style="{ width: width, height: height + 'px' }"
                        class="border-none rounded-2xl shadow-md bg-[#F8FAFC] transition-all duration-300"
                        scrolling="no"
                        frameborder="0"
                        allowtransparency="true">
                </iframe>
            </div>
            <p class="text-center text-slate-500 text-xs">
                Este é exatamente o aspecto do widget no seu site. Mude a categoria para ver os grupos atualizarem.
            </p>
        </div>
    </div>

    <x-adsense class="my-2" />

    {{-- Rodapé CTA --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-[#25D366]/10 rounded-xl">
                <x-heroicon-o-user-plus class="w-5 h-5 text-[#25D366]" />
            </div>
            <div>
                <p class="text-slate-900 font-bold text-sm">Tem um grupo para divulgar?</p>
                <p class="text-slate-500 text-xs">Adicione gratuitamente ao maior diretório do Brasil.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('home') }}"
               class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:border-slate-300 hover:text-slate-900 transition-all">
                Ver todos os grupos
            </a>
            <a href="{{ route('send-group.create') }}"
               class="px-4 py-2 rounded-xl bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold transition-all flex items-center gap-1.5">
                <x-heroicon-o-plus class="w-4 h-4" />
                Adicionar grupo
            </a>
        </div>
    </div>

</div>
@endsection
