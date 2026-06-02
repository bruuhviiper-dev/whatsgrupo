@extends('layouts.phrases')

@section('title', 'Frases para Status de WhatsApp | WhatsGrupos')
@section('description', 'Encontre as melhores frases para status de WhatsApp, legendas de fotos e stories. Mais de 200 frases de Amor, Amizade, Motivação, Engraçadas e Reflexão.')

@section('content')
{{-- Structured data: BreadcrumbList + ItemList das frases --}}
<x-seo.breadcrumbs :items="[
    ['name' => 'Início', 'url' => url('/')],
    ['name' => 'Frases para Status', 'url' => route('phrases.index')],
]" />
@php
    $phraseItems = collect($phrases)->map(fn ($p) => [
        'name' => Str::limit($p->phrase, 90),
        'url'  => route('phrases.show', $p),
    ])->all();
@endphp
@if(count($phraseItems))
<x-seo.itemlist name="Frases para Status de WhatsApp" :items="$phraseItems" />
@endif

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-slate-500 mb-6 flex-wrap" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
    <span class="text-slate-300">›</span>
    <span class="text-slate-900 font-medium">Frases para Status</span>
</nav>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
    @include('phrases.partials.sidebar-left')

    {{-- Coluna Principal (Frases + Paginação) --}}
    <div class="lg:col-span-6 space-y-8">
        {{-- Hero/Header da Seção --}}
        <div class="rounded-2xl p-6 border border-slate-200 relative overflow-hidden bg-white shadow-sm" style="background: linear-gradient(135deg, rgba(37,211,102,0.08), rgba(0,200,150,0.02));">
            <div class="relative z-10 max-w-3xl flex items-center gap-3">
                <div class="w-12 h-12 bg-green-50 text-primary rounded-2xl flex items-center justify-center shrink-0 shadow-sm border border-green-100">
                    <x-heroicon-s-chat-bubble-bottom-center-text class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Frases para Status de WhatsApp & Legendas</h1>
                    <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                        Precisa de inspiração? Explore nossa coleção de frases curtas e prontas para usar no seu status, legendas de fotos ou stories. Copie com um clique, curta e compartilhe!
                    </p>
                </div>
            </div>
        </div>

        {{-- Listagem de Frases Populares --}}
        <x-adsense class="mb-4" />
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="text-[#25D366] font-black text-xl leading-none">#</span>
                    <span>Frases Mais Curtidas</span>
                </h2>
                <span class="text-xs text-slate-500">As favoritas dos usuários</span>
            </div>

            @if ($phrases->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm flex flex-col items-center">
                    <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                        <x-heroicon-o-chat-bubble-left-right class="w-7 h-7" />
                    </div>
                    <h3 class="text-slate-800 font-bold text-lg mb-1">Nenhuma frase cadastrada ainda</h3>
                    <p class="text-slate-500 text-sm mb-4">Seja o primeiro a enviar uma frase inspiradora!</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6">
                    @foreach ($phrases as $phrase)
                        @if($loop->iteration > 1 && $loop->iteration % 2 == 1)
                            <x-adsense class="mb-6" />
                        @endif
                        {{-- Card de Frase Estilo gruposwhats.app --}}
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between"
                             x-data="{ 
                                likes: {{ $phrase->likes }}, 
                                liked: {{ session()->has('phrase_liked_' . $phrase->id) ? 'true' : 'false' }},
                                copied: false,
                                submitting: false,
                                likePhrase() {
                                    if (this.liked || this.submitting) return;
                                    this.submitting = true;
                                    fetch('{{ route('phrases.like', $phrase) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            this.likes = data.likes;
                                            this.liked = true;
                                        }
                                        this.submitting = false;
                                    })
                                    .catch(() => this.submitting = false);
                                }
                             }">
                            
                            {{-- Quote Body --}}
                            <div class="p-5 space-y-3.5 flex-1">
                                <svg class="w-7 h-7 text-slate-200 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-4.765 2.727-4.765 5.959h4.765v9.89h-9.978zm-14 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-4.765 2.727-4.765 5.959h4.765v9.89h-9.996z"/>
                                </svg>
                                <p class="text-slate-800 text-sm sm:text-base font-bold italic leading-relaxed font-serif select-all">
                                    {{ $phrase->phrase }}
                                </p>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-slate-400 text-xs font-semibold">
                                        — {{ $phrase->author ?: 'Anônimo' }}
                                    </span>
                                    <a href="{{ route('phrases.category', $phrase->category) }}"
                                       class="text-[10px] uppercase font-black px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 hover:text-primary transition-colors border border-slate-200/50 flex items-center gap-1">
                                        @php
                                            $icon = $categories[$phrase->category]['icon'] ?? 'heroicon-s-chat-bubble-bottom-center-text';
                                            $color = $categories[$phrase->category]['color'] ?? 'text-slate-500';
                                            $label = $categories[$phrase->category]['label'] ?? 'Frase';
                                        @endphp
                                        <x-dynamic-component :component="$icon" class="w-3 h-3 {{ $color }}" />
                                        <span>{{ $label }}</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Quote Footer --}}
                            <div class="border-t border-slate-100 px-4 py-3 bg-slate-50/50 rounded-b-2xl flex items-center justify-between flex-wrap gap-2">
                                {{-- Left: Social Share --}}
                                <div class="flex items-center gap-1.5">
                                    <a href="https://api.whatsapp.com/send?text={{ rawurlencode('“' . $phrase->phrase . '” — ' . ($phrase->author ?: 'Anônimo') . ' (Veja e baixe a imagem em: ' . route('phrases.show', $phrase) . ')') }}"
                                       class="w-7 h-7 flex items-center justify-center rounded-full bg-slate-200 text-slate-500 hover:bg-[#25D366] hover:text-white transition-colors"
                                       target="_blank" rel="noopener noreferrer" title="WhatsApp">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.003-2.637-1.03-5.114-2.914-6.999C16.638 1.857 14.162 1.824 12.01 1.824c-5.438 0-9.864 4.424-9.868 9.867-.001 1.637.452 3.23 1.309 4.63l-.993 3.62 3.712-.975zm12.183-7.062c-.29-.145-1.71-.844-1.974-.939-.263-.096-.455-.145-.646.145-.19.29-.739.939-.906 1.133-.167.193-.335.217-.626.072-.29-.145-1.222-.45-2.327-1.436-.86-.767-1.44-1.716-1.609-2.006-.168-.29-.018-.446.126-.59.13-.13.29-.338.436-.508.145-.17.193-.29.29-.483.096-.192.048-.36-.024-.505-.072-.145-.646-1.558-.885-2.133-.233-.56-.47-.483-.646-.492-.167-.008-.36-.01-.555-.01-.194 0-.51.072-.777.36-.266.29-1.02 1.002-1.02 2.44 0 1.437 1.045 2.824 1.19 3.017.146.193 2.057 3.14 4.985 4.41.696.302 1.24.482 1.66.615.7.22 1.336.19 1.84.115.56-.085 1.71-.698 1.95-1.37.24-.674.24-1.25.168-1.37-.072-.12-.266-.193-.555-.338z"/></svg>
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(route('phrases.show', $phrase)) }}"
                                       class="w-7 h-7 flex items-center justify-center rounded-full bg-slate-200 text-slate-500 hover:bg-[#1877F2] hover:text-white transition-colors"
                                       target="_blank" rel="noopener noreferrer" title="Facebook">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?text={{ rawurlencode('“' . $phrase->phrase . '”') }}&url={{ rawurlencode(route('phrases.show', $phrase)) }}"
                                       class="w-7 h-7 flex items-center justify-center rounded-full bg-slate-200 text-slate-500 hover:bg-[#1DA1F2] hover:text-white transition-colors"
                                       target="_blank" rel="noopener noreferrer" title="Twitter">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                    </a>
                                    <a href="https://t.me/share/url?url={{ rawurlencode(route('phrases.show', $phrase)) }}&text={{ rawurlencode('“' . $phrase->phrase . '”') }}"
                                       class="w-7 h-7 flex items-center justify-center rounded-full bg-slate-200 text-slate-500 hover:bg-[#0088cc] hover:text-white transition-colors"
                                       target="_blank" rel="noopener noreferrer" title="Telegram">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-1-.65-.35-1 .22-1.6 1.5-1.55 2.76-2.92 2.86-3.32.02-.08.01-.37-.21-.46-.22-.09-.55.03-.55.03s-1.87 1.26-5.28 3.56c-.5.34-.95.51-1.35.5-.44-.01-1.29-.25-1.92-.45-.77-.25-1.39-.39-1.34-.83.03-.23.35-.47.96-.73 3.76-1.64 6.27-2.72 7.53-3.25 3.58-1.51 4.32-1.77 4.81-1.78.11 0 .35.03.5.16.13.12.17.28.19.39.02.07.03.22.01.33z"/></svg>
                                    </a>
                                    <a href="https://pinterest.com/pin/create/button/?url={{ rawurlencode(route('phrases.show', $phrase)) }}&description={{ rawurlencode('“' . $phrase->phrase . '”') }}"
                                       class="w-7 h-7 flex items-center justify-center rounded-full bg-slate-200 text-slate-500 hover:bg-[#E60023] hover:text-white transition-colors"
                                       target="_blank" rel="noopener noreferrer" title="Pinterest">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.367 18.592 0 12.017 0z"/></svg>
                                    </a>
                                </div>

                                {{-- Right: View Image & Copy --}}
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('phrases.show', $phrase) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 transition-colors text-[11px] font-extrabold shadow-sm">
                                        <x-heroicon-o-eye class="w-3.5 h-3.5 text-slate-500" />
                                        <span>Ver Imagem</span>
                                    </a>

                                    <button @click="likePhrase()"
                                            class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 hover:text-red-500 text-[11px] font-extrabold shadow-sm outline-none transition-colors"
                                            :class="{'text-red-500 border-red-200 bg-red-50 hover:bg-red-50 hover:text-red-500': liked}">
                                        <svg x-show="!liked" class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        <svg x-show="liked" class="w-3.5 h-3.5 mr-1 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L12 8.343l3.172-3.171a4 4 0 115.656 5.656L12 21.657l-8.828-8.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                        <span x-text="likes"></span>
                                    </button>

                                    <button @click="navigator.clipboard.writeText('{{ $phrase->phrase }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 transition-colors text-[11px] font-extrabold shadow-sm outline-none">
                                        <span x-show="!copied" class="flex items-center gap-1">
                                            <x-heroicon-o-document-duplicate class="w-3.5 h-3.5 text-slate-500" />
                                            <span>Copiar</span>
                                        </span>
                                        <span x-show="copied" class="text-green-500 font-bold">Copiado!</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginação --}}
                <div class="mt-8 flex justify-center overflow-x-auto pb-4">
                    {{ $phrases->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>

    @include('phrases.partials.sidebar-right')
</div>
@endsection
