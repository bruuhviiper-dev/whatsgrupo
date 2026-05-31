@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegação da Paginação" class="flex flex-wrap items-center justify-center gap-2">
        {{-- Link para página anterior --}}
        @if ($paginator->onFirstPage())
            <span class="bg-slate-100 border border-slate-200 text-slate-400 rounded-lg px-3.5 py-2 text-sm font-semibold cursor-not-allowed">
                Anterior
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:border-[#25D366] rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors">
                Anterior
            </a>
        @endif

        {{-- Elementos de paginação --}}
        @foreach ($elements as $element)
            {{-- Separador de três pontos --}}
            @if (is_string($element))
                <span class="text-slate-400 px-3 py-2 text-sm font-semibold">{{ $element }}</span>
            @endif

            {{-- Array de links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="bg-[#25D366] border border-[#25D366] text-white rounded-lg px-3.5 py-2 text-sm font-bold shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="bg-white border border-slate-200 text-slate-600 hover:text-[#25D366] hover:border-[#25D366] hover:bg-green-50 rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Link para próxima página --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:border-[#25D366] rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors">
                Próxima
            </a>
        @else
            <span class="bg-slate-100 border border-slate-200 text-slate-400 rounded-lg px-3.5 py-2 text-sm font-semibold cursor-not-allowed">
                Próxima
            </span>
        @endif
    </nav>
@endif
