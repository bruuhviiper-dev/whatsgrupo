@extends('layouts.blog')

@section('title', $post->title . ' — Blog WhatsGrupos')
@section('description', $post->meta_description)
@section('canonical', url('/blog/' . $post->slug))

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
    <span class="text-slate-300">›</span>
    <a href="/blog" class="hover:text-primary transition-colors">Blog</a>
    <span class="text-slate-300">›</span>
    <span class="text-slate-900 font-medium line-clamp-1">{{ $post->title }}</span>
</nav>

<x-adsense class="mb-8" />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Conteúdo do Artigo --}}
    <div class="lg:col-span-2 space-y-6">
        <article class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-10">
            {{-- Badge & Views --}}
            <div class="flex items-center justify-between gap-4 mb-4">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest">
                    Dica & Segurança
                </span>
                <span class="text-slate-400 text-xs font-semibold flex items-center gap-1">
                    <x-heroicon-o-eye class="w-4 h-4" /> {{ number_format($post->views) }} visualizações
                </span>
            </div>

            {{-- Título Principal --}}
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                {{ $post->title }}
            </h1>

            {{-- Autor / Data --}}
            <div class="flex items-center gap-3 pb-6 border-b border-slate-100 mb-6 text-xs text-slate-400">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 text-white flex items-center justify-center font-bold text-sm">
                    W
                </div>
                <div>
                    <p class="font-bold text-slate-700">Equipe WhatsGrupos</p>
                    <p>{{ $post->created_at->translatedFormat('d \d\e F \d\e Y') }} • 4 min de leitura</p>
                </div>
            </div>

            {{-- Conteúdo do Artigo (HTML do TinyMCE) --}}
            <div class="prose prose-slate prose-a:text-primary max-w-none text-slate-600 text-sm sm:text-base leading-relaxed">
                {!! $post->content !!}
            </div>

            {{-- Compartilhamento & SEO Call --}}
            <div class="mt-10 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <p class="text-xs text-slate-400 font-medium">Gostou desse conteúdo? Ajude a espalhar conhecimento!</p>
                <div class="flex flex-wrap items-center gap-3">
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text={{ rawurlencode($post->title . ': ' . url()->current()) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#25D366] text-white hover:bg-[#1da851] transition-colors shadow-sm" title="WhatsApp">
                      <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97-1.863-1.868-4.343-2.898-6.978-2.9-5.437 0-9.864 4.37-9.869 9.8-.001 1.76.476 3.476 1.385 4.982L1.8 22.282l5.05-1.326l-.203-.122zm10.743-6.611c-.301-.15-1.78-.879-2.056-.979-.275-.1-.475-.15-.675.15-.2.3-.775 1.01-1.038 1.3-.263.29-.525.32-.825.17-1.4-.7-2.312-1.28-3.138-2.7-.22-.38.22-.35.63-.78.18-.19.3-.3.45-.45.15-.15.2-.25.3-.45.1-.2.05-.375-.025-.525-.075-.15-.675-1.625-.925-2.225-.244-.589-.48-.51-.662-.519c-.171-.009-.367-.01-.563-.01c-.196 0-.516.07-.786.37-.27.3-1.03 1.01-1.03 2.46c0 1.45 1.05 2.85 1.2 3.05.15.2 2.07 3.15 5.007 4.42c.699.302 1.243.483 1.668.619.702.223 1.342.192 1.847.116.563-.085 1.78-.729 2.03-1.43c.25-.7.25-1.3.175-1.43-.075-.13-.275-.205-.575-.355z"/></svg>
                    </a>

                    <!-- Telegram -->
                    <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ rawurlencode($post->title) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#0088cc] text-white hover:bg-[#0077b3] transition-colors shadow-sm" title="Telegram">
                      <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.892-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    </a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#1877F2] text-white hover:bg-[#166fe5] transition-colors shadow-sm" title="Facebook">
                       <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>

                    <!-- Instagram -->
                    <div x-data="{ copiedIg: false }">
                        <button @click="navigator.clipboard.writeText('{{ url()->current() }}'); copiedIg = true; setTimeout(() => copiedIg = false, 2000)"
                           class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gradient-to-tr from-[#f09433] via-[#e6683c] to-[#bc1888] text-white hover:opacity-90 transition-opacity shadow-sm" title="Copiar link para Instagram">
                           <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.07M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                        </button>
                    </div>

                    <!-- Copiar Link -->
                    <div x-data="{ copied: false }">
                        <button @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 2000)"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors shadow-sm text-xs font-bold" title="Copiar link">
                           <x-heroicon-o-link class="w-4 h-4" />
                           <span x-show="!copied">Copiar Link</span>
                           <span x-show="copied" x-cloak style="display:none;" class="text-green-600">Copiado!</span>
                        </button>
                    </div>
                </div>
            </div>
        </article>

        {{-- Seção de Perguntas Frequentes / FAQ estruturado por baixo da postagem para rankear melhor --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-10">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <span>💬 Dúvidas Frequentes sobre WhatsApp</span>
            </h3>
            
            <div class="space-y-4" x-data="{ active: null }">
                <div class="border-b border-slate-50 pb-4">
                    <button @click="active === 1 ? active = null : active = 1" class="w-full flex justify-between items-center text-left font-bold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
                        <span>O cadastro de grupos no WhatsGrupos é gratuito?</span>
                        <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform" x-bind:class="active === 1 ? 'rotate-180 text-primary' : ''" />
                    </button>
                    <div x-show="active === 1" x-collapse class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed" style="display: none;">
                        Sim! Qualquer administrador pode cadastrar o link oficial de convite do seu grupo ou canal sem custo algum. O cadastro é rápido e traz visitas orgânicas e constantes.
                    </div>
                </div>

                <div class="border-b border-slate-50 pb-4">
                    <button @click="active === 2 ? active = null : active = 2" class="w-full flex justify-between items-center text-left font-bold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
                        <span>Quanto tempo demora para meu grupo ser aprovado?</span>
                        <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform" x-bind:class="active === 2 ? 'rotate-180 text-primary' : ''" />
                    </button>
                    <div x-show="active === 2" x-collapse class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed" style="display: none;">
                        Nossa equipe realiza moderações constantes ao longo do dia. A aprovação ocorre tipicamente em menos de 4 horas úteis, desde que o grupo atenda aos Termos de Serviço.
                    </div>
                </div>

                <div class="pb-2">
                    <button @click="active === 3 ? active = null : active = 3" class="w-full flex justify-between items-center text-left font-bold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
                        <span>O que são e como funcionam os pacotes VIP?</span>
                        <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform" x-bind:class="active === 3 ? 'rotate-180 text-primary' : ''" />
                    </button>
                    <div x-show="active === 3" x-collapse class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed" style="display: none;">
                        Os pacotes VIP são serviços pagos opcionais que colocam o link do seu grupo em posições fixas de destaque no topo do portal. Isso garante tráfego até 10 vezes maior e conversões aceleradas de novos membros.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Lateral --}}
    <aside class="space-y-6 lg:sticky lg:top-8 h-max">
        <x-adsense class="!mt-0 !mb-0" />
        {{-- Artigos Relacionados --}}
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-wider mb-4">
                📖 Artigos Relacionados
            </h3>
            <div class="space-y-4">
                @foreach($relatedPosts as $related)
                    <a href="/blog/{{ $related->slug }}" class="group block pb-3 border-b border-slate-50 last:border-0 last:pb-0">
                        <span class="text-[10px] text-slate-400 font-bold block mb-0.5">
                            {{ $related->created_at->translatedFormat('d/m/Y') }}
                        </span>
                        <h4 class="font-bold text-slate-700 group-hover:text-primary transition-colors text-xs sm:text-sm leading-snug line-clamp-2">
                            {{ $related->title }}
                        </h4>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Widget CTA Divulgar --}}
        <div class="bg-gradient-to-br from-green-500 to-emerald-700 text-white p-6 rounded-3xl shadow-sm relative overflow-hidden">
            <div class="absolute right-0 bottom-0 opacity-10 translate-x-4 translate-y-4">
                <x-heroicon-s-rocket-launch class="w-36 h-36" />
            </div>
            <h3 class="font-extrabold text-lg mb-2 relative z-10">Coloque seu Grupo no Catálogo</h3>
            <p class="text-white/90 text-xs sm:text-sm leading-relaxed mb-4 relative z-10">
                Divulgue gratuitamente e ganhe participantes todos os dias no maior site de grupos do Brasil.
            </p>
            <a href="{{ route('send-group.create') }}" class="inline-block bg-white text-emerald-800 font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-lg shadow-md hover:bg-slate-50 transition-colors relative z-10">
                ➕ Enviar Grupo Grátis
            </a>
        </div>

        {{-- Widget Categorias --}}
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-wider mb-4">
                📂 Outras Categorias
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
