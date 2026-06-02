@extends('layouts.phrases')

@section('title', 'Frase de ' . ($statusPhrase->author ?: 'Anônimo') . ' — ' . $categoryName . ' | WhatsGrupos')
@section('description', '“' . Str::limit($statusPhrase->phrase, 140) . '” — Encontre essa e outras lindas frases de ' . $categoryName . ' para status de WhatsApp e legendas.')
@section('canonical', route('phrases.show', $statusPhrase))

@section('content')
{{-- Structured data: Quotation (frase) + BreadcrumbList --}}
<x-seo.quotation :phrase="$statusPhrase" :categoryName="$categoryName" />
<x-seo.breadcrumbs :items="[
    ['name' => 'Início', 'url' => url('/')],
    ['name' => 'Frases', 'url' => route('phrases.index')],
    ['name' => $categoryName, 'url' => route('phrases.category', $statusPhrase->category)],
    ['name' => 'Frase', 'url' => route('phrases.show', $statusPhrase)],
]" />

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-slate-500 mb-6 flex-wrap" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
    <span class="text-slate-300">›</span>
    <a href="{{ route('phrases.index') }}" class="hover:text-primary transition-colors">Frases</a>
    <span class="text-slate-300">›</span>
    <a href="{{ route('phrases.category', $statusPhrase->category) }}" class="hover:text-primary transition-colors">{{ $categoryName }}</a>
    <span class="text-slate-300">›</span>
    <span class="text-slate-900 font-medium">Visualizar Frase</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
    @include('phrases.partials.sidebar-left')

    {{-- Coluna Principal (Frase + Ações + Relacionados) --}}
    <div class="lg:col-span-6 space-y-8"
         x-data="{
            likes: {{ $statusPhrase->likes }},
            liked: {{ session()->has('phrase_liked_' . $statusPhrase->id) ? 'true' : 'false' }},
            copied: false,
            submitting: false,
            phraseText: '{{ addslashes($statusPhrase->phrase) }}',
            phraseAuthor: '{{ addslashes($statusPhrase->author ?: 'Anônimo') }}',
            phraseId: '{{ $statusPhrase->id }}',
            
            downloadPhraseImage() {
                const canvas = document.createElement('canvas');
                canvas.width = 800;
                canvas.height = 800;
                const ctx = canvas.getContext('2d');

                // 1. Background com degradê escuro super premium
                ctx.fillStyle = '#0B0F19';
                ctx.fillRect(0, 0, 800, 800);

                const grad = ctx.createRadialGradient(400, 400, 50, 400, 400, 550);
                grad.addColorStop(0, '#1E293B');
                grad.addColorStop(1, '#020617');
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, 800, 800);

                // 2. Bordas decorativas sutis
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
                ctx.lineWidth = 2;
                ctx.strokeRect(35, 35, 730, 730);

                // Linha de detalhe verde do WhatsGrupos
                ctx.strokeStyle = '#25D366';
                ctx.lineWidth = 4;
                ctx.beginPath();
                ctx.moveTo(360, 55);
                ctx.lineTo(440, 55);
                ctx.stroke();

                // 3. Aspas grandes decorativas
                ctx.fillStyle = 'rgba(37, 211, 102, 0.08)';
                ctx.font = 'italic bold 220px Georgia, serif';
                ctx.textAlign = 'center';
                ctx.fillText('“', 400, 200);

                // 4. Desenha o texto da frase centralizado e quebrado em linhas
                ctx.fillStyle = '#FFFFFF';
                ctx.font = 'italic bold 32px Georgia, serif';
                ctx.textBaseline = 'middle';
                ctx.textAlign = 'center';

                const words = this.phraseText.split(' ');
                const lines = [];
                let currentLine = '';
                const maxWidth = 640;

                for (let n = 0; n < words.length; n++) {
                    let testLine = currentLine + words[n] + ' ';
                    let metrics = ctx.measureText(testLine);
                    if (metrics.width > maxWidth && n > 0) {
                        lines.push(currentLine.trim());
                        currentLine = words[n] + ' ';
                    } else {
                        currentLine = testLine;
                    }
                }
                lines.push(currentLine.trim());

                const lineHeight = 55;
                const totalHeight = lines.length * lineHeight;
                let startY = 380 - (totalHeight / 2);

                for (let i = 0; i < lines.length; i++) {
                    ctx.fillText(lines[i], 400, startY + (i * lineHeight));
                }

                // 5. Autor
                ctx.fillStyle = '#25D366';
                ctx.font = 'bold 22px \"Inter\", sans-serif';
                ctx.fillText('— ' + this.phraseAuthor, 400, startY + totalHeight + 45);

                // 6. Rodapé da Imagem com Logo do WhatsGrupos
                ctx.fillStyle = '#25D366';
                ctx.beginPath();
                ctx.arc(400, 680, 20, 0, 2 * Math.PI);
                ctx.fill();

                ctx.fillStyle = '#FFFFFF';
                ctx.font = 'bold 15px \"Inter\", sans-serif';
                ctx.fillText('💬', 400, 683);

                ctx.fillStyle = '#E2E8F0';
                ctx.font = 'bold 15px \"Inter\", sans-serif';
                ctx.fillText('WHATSGRUPOS', 400, 720);

                ctx.fillStyle = 'rgba(255, 255, 255, 0.35)';
                ctx.font = '12px \"Inter\", sans-serif';
                ctx.fillText('whatsgrupos.com/frases', 400, 740);

                // 7. Dispara o download real
                const link = document.createElement('a');
                link.download = 'frase-' + this.phraseId + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }
         }">
         
        {{-- Card da Frase --}}
        <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden flex flex-col max-w-[650px] mx-auto w-full"
             x-data="{ 
                likes: {{ $statusPhrase->likes }}, 
                liked: {{ session()->has('phrase_liked_' . $statusPhrase->id) ? 'true' : 'false' }},
                copied: false,
                submitting: false,
                likePhrase() {
                    if (this.liked || this.submitting) return;
                    this.submitting = true;
                    fetch('{{ route('phrases.like', $statusPhrase) }}', {
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
            
            <!-- Imagem Card Mockup Interativo -->
            <div class="w-full aspect-square bg-[#0B0F19] flex flex-col justify-between p-6 relative select-none overflow-hidden" 
                 style="background: radial-gradient(circle, #1E293B 0%, #020617 100%);">
                <!-- Quote Mark Deco -->
                <span class="text-[120px] font-serif text-[#25D366] absolute top-2 left-1/2 -translate-x-1/2 leading-none pointer-events-none opacity-80">“</span>

                <!-- Content -->
                <div class="flex-1 flex flex-col justify-center text-center space-y-4 px-2 mt-12">
                    <p class="text-[#E2E8F0] text-xl sm:text-2xl font-bold italic leading-relaxed font-serif tracking-wide select-all">
                        {{ $statusPhrase->phrase }}
                    </p>
                    <p class="text-[#25D366] text-sm sm:text-base font-bold tracking-widest">
                        {{ $statusPhrase->author ?: 'Eu e meu coquinho cabeçudo' }}
                    </p>
                </div>

                <!-- Footer Logo -->
                <div class="flex flex-col items-center gap-1.5 pt-4 mt-8">
                    <div class="w-8 h-8 rounded-full bg-white text-[#25D366] flex items-center justify-center font-bold text-lg shadow-sm">
                        <svg class="w-5 h-5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.003-2.637-1.03-5.114-2.914-6.999C16.638 1.857 14.162 1.824 12.01 1.824c-5.438 0-9.864 4.424-9.868 9.867-.001 1.637.452 3.23 1.309 4.63l-.993 3.62 3.712-.975zm12.183-7.062c-.29-.145-1.71-.844-1.974-.939-.263-.096-.455-.145-.646.145-.19.29-.739.939-.906 1.133-.167.193-.335.217-.626.072-.29-.145-1.222-.45-2.327-1.436-.86-.767-1.44-1.716-1.609-2.006-.168-.29-.018-.446.126-.59.13-.13.29-.338.436-.508.145-.17.193-.29.29-.483.096-.192.048-.36-.024-.505-.072-.145-.646-1.558-.885-2.133-.233-.56-.47-.483-.646-.492-.167-.008-.36-.01-.555-.01-.194 0-.51.072-.777.36-.266.29-1.02 1.002-1.02 2.44 0 1.437 1.045 2.824 1.19 3.017.146.193 2.057 3.14 4.985 4.41.696.302 1.24.482 1.66.615.7.22 1.336.19 1.84.115.56-.085 1.71-.698 1.95-1.37.24-.674.24-1.25.168-1.37-.072-.12-.266-.193-.555-.338z"/>
                        </svg>
                    </div>
                    <p class="text-[13px] font-black tracking-widest text-[#E2E8F0]">FRASES</p>
                    <p class="text-[10px] text-slate-400 font-semibold">{{ url('/frases') }}</p>
                </div>
            </div>

            {{-- Text Below Image --}}
            <div class="p-5 flex-1">
                <p class="text-slate-600 text-[15px] leading-relaxed mb-3">
                    {{ $statusPhrase->phrase }}
                </p>
                <p class="text-slate-500 text-sm">
                    — {{ $statusPhrase->author ?: 'Eu e meu coquinho cabeçudo' }}
                </p>
            </div>

            {{-- Botão de Compartilhamento Social e Copiar --}}
            <div class="border-t border-slate-100 px-4 py-3 bg-white flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-1.5">
                    <a href="https://api.whatsapp.com/send?text={{ rawurlencode('“' . $statusPhrase->phrase . '” — ' . ($statusPhrase->author ?: 'Anônimo') . ' (Baixe o card da frase em: ' . route('phrases.show', $statusPhrase)) }}"
                       class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-200 text-slate-400 hover:bg-[#25D366] hover:text-white transition-colors"
                       target="_blank" rel="noopener noreferrer" title="WhatsApp">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.003-2.637-1.03-5.114-2.914-6.999C16.638 1.857 14.162 1.824 12.01 1.824c-5.438 0-9.864 4.424-9.868 9.867-.001 1.637.452 3.23 1.309 4.63l-.993 3.62 3.712-.975zm12.183-7.062c-.29-.145-1.71-.844-1.974-.939-.263-.096-.455-.145-.646.145-.19.29-.739.939-.906 1.133-.167.193-.335.217-.626.072-.29-.145-1.222-.45-2.327-1.436-.86-.767-1.44-1.716-1.609-2.006-.168-.29-.018-.446.126-.59.13-.13.29-.338.436-.508.145-.17.193-.29.29-.483.096-.192.048-.36-.024-.505-.072-.145-.646-1.558-.885-2.133-.233-.56-.47-.483-.646-.492-.167-.008-.36-.01-.555-.01-.194 0-.51.072-.777.36-.266.29-1.02 1.002-1.02 2.44 0 1.437 1.045 2.824 1.19 3.017.146.193 2.057 3.14 4.985 4.41.696.302 1.24.482 1.66.615.7.22 1.336.19 1.84.115.56-.085 1.71-.698 1.95-1.37.24-.674.24-1.25.168-1.37-.072-.12-.266-.193-.555-.338z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(route('phrases.show', $statusPhrase)) }}"
                       class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-200 text-slate-400 hover:bg-[#1877F2] hover:text-white transition-colors"
                       target="_blank" rel="noopener noreferrer" title="Facebook">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ rawurlencode('“' . $statusPhrase->phrase . '”') }}&url={{ rawurlencode(route('phrases.show', $statusPhrase)) }}"
                       class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-200 text-slate-400 hover:bg-[#000000] hover:text-white transition-colors"
                       target="_blank" rel="noopener noreferrer" title="X (Twitter)">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.008 3.827H5.045z"/></svg>
                    </a>
                    <a href="https://t.me/share/url?url={{ rawurlencode(route('phrases.show', $statusPhrase)) }}&text={{ rawurlencode('“' . $statusPhrase->phrase . '”') }}"
                       class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-200 text-slate-400 hover:bg-[#0088cc] hover:text-white transition-colors"
                       target="_blank" rel="noopener noreferrer" title="Telegram">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-1-.65-.35-1 .22-1.6 1.5-1.55 2.76-2.92 2.86-3.32.02-.08.01-.37-.21-.46-.22-.09-.55.03-.55.03s-1.87 1.26-5.28 3.56c-.5.34-.95.51-1.35.5-.44-.01-1.29-.25-1.92-.45-.77-.25-1.39-.39-1.34-.83.03-.23.35-.47.96-.73 3.76-1.64 6.27-2.72 7.53-3.25 3.58-1.51 4.32-1.77 4.81-1.78.11 0 .35.03.5.16.13.12.17.28.19.39.02.07.03.22.01.33z"/></svg>
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url={{ rawurlencode(route('phrases.show', $statusPhrase)) }}&description={{ rawurlencode('“' . $statusPhrase->phrase . '”') }}"
                       class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-200 text-slate-400 hover:bg-[#E60023] hover:text-white transition-colors"
                       target="_blank" rel="noopener noreferrer" title="Pinterest">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.367 18.592 0 12.017 0z"/></svg>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="likePhrase()"
                            class="inline-flex items-center justify-center px-3 py-1 bg-white hover:bg-slate-50 border border-slate-300 text-slate-500 hover:text-red-500 text-[11px] font-medium rounded outline-none transition-colors"
                            :class="{'text-red-500 border-red-200 bg-red-50': liked}">
                        <svg x-show="!liked" class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <svg x-show="liked" class="w-3.5 h-3.5 mr-1 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L12 8.343l3.172-3.171a4 4 0 115.656 5.656L12 21.657l-8.828-8.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                        <span x-text="likes"></span>
                    </button>
                    <button @click="navigator.clipboard.writeText('{{ $statusPhrase->phrase }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="inline-flex items-center justify-center px-3 py-1 bg-white hover:bg-slate-50 border border-slate-300 text-slate-500 hover:text-slate-700 text-[11px] font-medium rounded outline-none transition-colors">
                        <span x-show="!copied">Copiar</span>
                        <span x-show="copied" class="text-green-500 font-bold">Copiado!</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Outras Frases Relacionadas --}}
        @if($relatedPhrases->isNotEmpty())
            <div class="space-y-4">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <x-heroicon-s-light-bulb class="w-5 h-5 text-amber-500" />
                    <span>Outras Frases Relacionadas</span>
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($relatedPhrases as $related)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
                            <p class="text-slate-700 font-medium italic text-sm sm:text-base leading-relaxed line-clamp-3 mb-4 select-all">
                                “{{ $related->phrase }}”
                            </p>
                            <div class="flex justify-between items-center border-t border-slate-50 pt-3">
                                <span class="text-slate-400 text-xs font-semibold">
                                    — {{ $related->author ?: 'Anônimo' }}
                                </span>
                                <a href="{{ route('phrases.show', $related) }}" class="text-xs font-extrabold text-primary hover:text-secondary transition-colors inline-flex items-center gap-0.5">
                                    <span>Ver e Baixar Card</span> <x-heroicon-m-chevron-right class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @include('phrases.partials.sidebar-right')
</div>
<x-adsense class="mt-6" />
@endsection
