{{--
    Componente de Menu de Categorias
    Versão desktop: grid 2 colunas na sidebar
    Versão mobile: scroll horizontal com chips

    Propriedades: $categories (Collection de Category), $currentSlug (string, opcional)
--}}
@props(['categories', 'currentSlug' => null])

{{-- ====================================================
     VERSÃO DESKTOP — Grid vertical embutida na sidebar
     Usada dentro do layout app.blade.php
     ==================================================== --}}
<nav class="hidden lg:block" aria-label="Menu de categorias desktop">
    <a href="{{ route('home') }}"
       class="flex items-center px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ !$currentSlug ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1">
        <span>Todos os Grupos</span>
    </a>
    @foreach ($categories as $cat)
        <a href="{{ route('group.category', $cat->slug) }}"
           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ $currentSlug === $cat->slug ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1">
            <span class="truncate">{{ $cat->name }}</span>
            @if(isset($cat->groups_count))
                <span class="ml-auto text-[10px] text-slate-500 bg-slate-100/50 border border-slate-200 px-1.5 py-0.5 rounded-md flex-shrink-0">
                    {{ $cat->groups_count }}
                </span>
            @endif
        </a>
    @endforeach
</nav>

{{-- ====================================================
     VERSÃO MOBILE — Scroll horizontal com chips de categoria
     Exibida abaixo do header nas páginas de listagem
     ==================================================== --}}
<div class="lg:hidden overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide" aria-label="Menu de categorias mobile">
    <div class="flex gap-2 w-max">
        <a href="{{ route('home') }}"
           class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all border
                  {{ !$currentSlug ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Todos
        </a>
        @foreach ($categories as $cat)
            <a href="{{ route('group.category', $cat->slug) }}"
               class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all border
                      {{ $currentSlug === $cat->slug ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>
