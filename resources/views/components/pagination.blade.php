@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegação de páginas" class="w-full">

        {{-- ─────────────── MOBILE: compacto (Anterior · X de Y · Próxima) ─────────────── --}}
        <div class="flex sm:hidden items-center justify-between gap-3">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-slate-50 border border-slate-200 text-slate-300 rounded-xl px-4 py-2.5 text-sm font-bold cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-white border border-slate-200 text-slate-700 active:bg-green-50 active:border-[#25D366] rounded-xl px-4 py-2.5 text-sm font-bold transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Anterior
                </a>
            @endif

            <span class="shrink-0 text-xs font-bold text-slate-500 whitespace-nowrap px-1">
                {{ $paginator->currentPage() }} <span class="text-slate-300">/</span> {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-white border border-slate-200 text-slate-700 active:bg-green-50 active:border-[#25D366] rounded-xl px-4 py-2.5 text-sm font-bold transition-colors shadow-sm">
                    Próxima
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span aria-disabled="true" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-slate-50 border border-slate-200 text-slate-300 rounded-xl px-4 py-2.5 text-sm font-bold cursor-not-allowed">
                    Próxima
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>

        {{-- ─────────────── DESKTOP: números completos ─────────────── --}}
        <div class="hidden sm:flex items-center justify-center gap-1.5">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex items-center gap-1 bg-slate-50 border border-slate-200 text-slate-300 rounded-lg pl-2.5 pr-3.5 py-2 text-sm font-bold cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 bg-white border border-slate-200 text-slate-600 hover:text-[#25D366] hover:border-[#25D366] hover:bg-green-50 rounded-lg pl-2.5 pr-3.5 py-2 text-sm font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Anterior
                </a>
            @endif

            {{-- Números --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-2 text-sm font-bold text-slate-400 select-none">…</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="min-w-[40px] text-center bg-[#25D366] border border-[#25D366] text-white rounded-lg px-3 py-2 text-sm font-extrabold shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="min-w-[40px] text-center bg-white border border-slate-200 text-slate-600 hover:text-[#25D366] hover:border-[#25D366] hover:bg-green-50 rounded-lg px-3 py-2 text-sm font-bold transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Próxima --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 bg-white border border-slate-200 text-slate-600 hover:text-[#25D366] hover:border-[#25D366] hover:bg-green-50 rounded-lg pl-3.5 pr-2.5 py-2 text-sm font-bold transition-colors">
                    Próxima
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span aria-disabled="true" class="inline-flex items-center gap-1 bg-slate-50 border border-slate-200 text-slate-300 rounded-lg pl-3.5 pr-2.5 py-2 text-sm font-bold cursor-not-allowed">
                    Próxima
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
