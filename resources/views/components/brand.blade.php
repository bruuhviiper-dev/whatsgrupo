@props([
    'href' => '/',
    'size' => 'md',      // sm | md | lg
    'theme' => 'dark',   // dark = sobre fundo escuro (texto claro) | light
])

@php
    $markSize = ['sm' => 'w-7 h-7', 'md' => 'w-8 h-8', 'lg' => 'w-10 h-10'][$size] ?? 'w-8 h-8';
    $textSize = ['sm' => 'text-[17px]', 'md' => 'text-[19px]', 'lg' => 'text-[24px]'][$size] ?? 'text-[19px]';
    $whats    = $theme === 'dark' ? 'text-slate-400 group-hover:text-slate-300' : 'text-slate-500';
    $grupos   = $theme === 'dark' ? 'text-white' : 'text-slate-900';
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'flex items-center gap-2.5 shrink-0 select-none group']) }}
   aria-label="WhatsGrupos — Início">

    {{-- Mark: badge verde com balão de chat + grupo de pessoas --}}
    <span class="relative {{ $markSize }} shrink-0 transition-transform duration-200 group-hover:-translate-y-0.5">
        <svg viewBox="0 0 512 512" class="w-full h-full drop-shadow-sm" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <defs>
                <linearGradient id="wgBrand" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#2BE372"/>
                    <stop offset="1" stop-color="#0E9F66"/>
                </linearGradient>
            </defs>
            <rect width="512" height="512" rx="116" fill="url(#wgBrand)"/>
            <path fill="#fff" d="M118 96h276a44 44 0 0 1 44 44v168a44 44 0 0 1-44 44H214l-78 60a12 12 0 0 1-19-10v-50h-1a44 44 0 0 1-42-44V140a44 44 0 0 1 44-44z"/>
            <g fill="#0E9F66">
                <circle cx="190" cy="190" r="30"/>
                <path d="M142 286c0-30 21-52 48-52s48 22 48 52z"/>
                <circle cx="322" cy="190" r="30"/>
                <path d="M274 286c0-30 21-52 48-52s48 22 48 52z"/>
                <circle cx="256" cy="178" r="42" fill="#0E9F66" stroke="#fff" stroke-width="10"/>
                <path d="M192 292c0-38 28-66 64-66s64 28 64 66z" fill="#0E9F66" stroke="#fff" stroke-width="10"/>
            </g>
        </svg>
    </span>

    {{-- Wordmark --}}
    <span class="leading-none whitespace-nowrap" style="font-family:'Outfit',sans-serif;">
        <span class="{{ $textSize }} font-light tracking-tight transition-colors {{ $whats }}">Whats</span><span class="{{ $textSize }} font-black tracking-tight {{ $grupos }}">Grupos</span>
    </span>
</a>
