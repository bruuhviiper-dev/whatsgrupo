{{-- Coluna da Esquerda (Sidebar de Categorias + Enviar Frase Adesiva) --}}
<div class="lg:col-span-3 space-y-6 lg:sticky lg:top-24 self-start">
    {{-- Botão Enviar Frase --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm text-center">
        <div class="w-12 h-12 bg-green-50 text-primary rounded-full flex items-center justify-center mx-auto mb-3">
            <x-heroicon-s-pencil-square class="w-6 h-6" />
        </div>
        <h3 class="font-extrabold text-slate-800 text-sm mb-2">Tem uma frase legal?</h3>
        <p class="text-slate-500 text-[11px] leading-relaxed mb-4">
            Compartilhe com a comunidade e deixe sua marca no WhatsGrupos.
        </p>
        <a href="{{ route('phrases.create') }}" class="bg-[#25D366] hover:bg-[#1da851] text-white font-bold w-full py-2.5 rounded-xl text-xs transition-colors shadow-sm flex items-center justify-center gap-1.5">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Enviar Minha Frase</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-1.5">
            <x-heroicon-s-folder-open class="w-4 h-4 text-slate-500" />
            <span>Categorias de Frases</span>
        </h3>
        <nav class="flex flex-col gap-1.5 max-h-[calc(100vh-28rem)] overflow-y-scroll pr-1" style="scrollbar-width: thin; scrollbar-color: #25D366 #e6f9ed; /* For webkit */ --tw-scroll-track: #e6f9ed; --tw-scroll-thumb: #25D366;">
            <style>
                nav[style*="scrollbar-width"]::-webkit-scrollbar { width: 6px; }
                nav[style*="scrollbar-width"]::-webkit-scrollbar-track { background: #e6f9ed; border-radius: 6px; }
                nav[style*="scrollbar-width"]::-webkit-scrollbar-thumb { background: #25D366; border-radius: 6px; }
            </style>
            @foreach ($categories as $cat)
                @php
                    // Identifica se a categoria atual está ativa
                    // Suporta tanto a variável $category (usada na listagem) quanto $statusPhrase (usada na página interna)
                    $isActive = false;
                    if (isset($category) && $category === $cat['slug']) {
                        $isActive = true;
                    } elseif (isset($statusPhrase) && $statusPhrase->category === $cat['slug']) {
                        $isActive = true;
                    }
                @endphp
                <a href="{{ route('phrases.category', $cat['slug']) }}" 
                   class="px-3.5 py-2.5 rounded-xl text-xs font-extrabold text-slate-600 border border-slate-100/50 hover:bg-green-50/50 hover:text-primary hover:border-green-100 transition-all flex items-center justify-between {{ $isActive ? 'bg-green-50 text-primary border-green-100' : 'bg-slate-50' }}">
                    <div class="flex items-center">
                        <x-dynamic-component :component="$cat['icon']" class="w-4 h-4 {{ $cat['color'] }} mr-2 shrink-0" />
                        <span>{{ $cat['label'] }}</span>
                    </div>
                    <x-heroicon-m-chevron-right class="w-4 h-4 opacity-50" />
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Widget CTA Whatsapp --}}
    <div class="bg-[#25D366] text-white p-5 rounded-2xl shadow-sm relative overflow-hidden">
        <h4 class="font-black text-sm uppercase tracking-wider mb-1 relative z-10">Buscando Grupos?</h4>
        <p class="text-white/90 text-xs leading-relaxed mb-4 relative z-10">
            Acesse milhares de links de convites ativos de grupos do WhatsApp no Brasil organizados por categorias!
        </p>
        <a href="{{ route('home') }}" class="inline-block bg-white text-[#1da851] font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-lg shadow-md hover:bg-slate-50 transition-colors relative z-10">
            Procurar Grupos
        </a>
    </div>
</div>
