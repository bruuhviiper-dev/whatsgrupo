<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'WhatsGrupos') — Grupos de WhatsApp</title>
  <meta name="description" content="@yield('description', 'Encontre e entre nos melhores grupos de WhatsApp organizados por categorias.')">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <meta name="author" content="WhatsGrupos">
  <link rel="canonical" href="@yield('canonical', url()->current())">
  <link rel="alternate" hreflang="pt-BR" href="@yield('canonical', url()->current())">
  <link rel="alternate" hreflang="x-default" href="@yield('canonical', url()->current())">
  
  <!-- Favicons -->
  @php $dynamicFavicon = \App\Models\Setting::get('favicon'); @endphp
  @if($dynamicFavicon)
    <link rel="icon" href="{{ Storage::disk('public')->url($dynamicFavicon) }}">
    <link rel="apple-touch-icon" href="{{ Storage::disk('public')->url($dynamicFavicon) }}">
  @else
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
  @endif

  <!-- Open Graph -->
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:site_name" content="WhatsGrupos">
  <meta property="og:locale" content="pt_BR">
  <meta property="og:title" content="@yield('title', 'WhatsGrupos') — Grupos de WhatsApp">
  <meta property="og:description" content="@yield('description', 'Encontre e entre nos melhores grupos de WhatsApp...')">
  <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
  <meta property="og:url" content="{{ url()->current() }}">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('title', 'WhatsGrupos')">
  <meta name="twitter:description" content="@yield('description', 'Encontre e entre nos melhores grupos de WhatsApp...')">
  <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

  <!-- PWA Manifest & Theme -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <meta name="theme-color" content="#25D366">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="WhatsGrupos">

  <!-- Estilos Globais -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  {{-- Tailwind CSS CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            heading: ['Outfit', 'sans-serif'],
          },
          colors: {
            primary: '#25D366',
            secondary: '#1da851',
            bg: '#f8fafc',
            card: '#ffffff',
            'text-main': '#0f172a',
            'text-muted': '#64748b',
            gold: '#eab308',
          }
        }
      }
    }
  </script>

  {{-- Alpine.js CDN --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  @stack('head')

  {{-- Google Analytics (GA4) em todas as páginas --}}
  <x-google-analytics />

  {{-- JSON-LD global (Organization + WebSite + SearchAction) em todas as páginas --}}
  <x-seo.global />
  @stack('schema')

  {{-- Google AdSense: Meta Tag e Script (gerenciados pelo painel) --}}
  @php
    use App\Models\Setting;
    $adsenseEnabled = Setting::adsenseEnabled();
  @endphp
  @if($adsenseEnabled)
    {!! Setting::adsenseMetaTag() !!}
    {!! Setting::adsenseScript() !!}
  @endif
</head>
<body class="bg-[#f8fafc]" x-data="{ mobileMenuOpen: false }">

  {{-- ─────────────────────────────────────────────────────────────────────────
       HEADER — Mobile-first: Row 1 = brand + nav, Row 2 = search (mobile only)
       Desktop: single row — brand | search (center) | actions
  ────────────────────────────────────────────────────────────────────────── --}}
  <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">

    {{-- ── Linha principal ──────────────────────────────────────────────── --}}
    <div class="max-w-[1400px] mx-auto px-4 h-16 flex items-center gap-3 lg:gap-5">

      {{-- ── NAVBRAND (componente reutilizável) ──────────────────────────── --}}
      <x-brand href="/" size="md" theme="dark" />

      {{-- ── BUSCA (centro — flex, visível só em md+) ────────────────────── --}}
      <div class="hidden md:flex flex-1 max-w-2xl mx-auto" x-data="{ q: '{{ request('q', '') }}' }">
        <form action="{{ request()->is('blog') || request()->is('blog/*') ? '/blog' : '/buscar' }}" method="GET" class="relative w-full">
          <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
          <input type="text" name="q" x-model="q"
            placeholder="Buscar grupos de WhatsApp..."
            class="w-full bg-slate-800/70 border border-slate-700/60 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none focus:border-[#25D366]/70 focus:bg-slate-800 focus:ring-1 focus:ring-[#25D366]/40 transition-all" />
        </form>
      </div>

      {{-- ── AÇÕES (direita) ─────────────────────────────────────────────── --}}
      <div class="flex items-center gap-1.5 sm:gap-2 ml-auto md:ml-0">

        {{-- Meus Grupos: botão cinza visível (lg+) --}}
        <a href="/meus-grupos"
           class="hidden lg:inline-flex items-center justify-center gap-1.5 min-w-[140px] text-[13px] font-semibold px-4 py-2 rounded-lg border transition-all
                  {{ request()->is('meus-grupos*') ? 'bg-slate-700 border-slate-600 text-white' : 'bg-slate-800 border-slate-700/70 text-slate-200 hover:bg-slate-700 hover:text-white hover:border-slate-600' }}">
          <x-heroicon-o-users class="w-4 h-4" />
          Meus Grupos
        </a>

        {{-- Impulsionar: DESTAQUE com fundo amarelo (consistente com o offcanvas) --}}
        <a href="/pacotes-vip"
           class="hidden md:inline-flex items-center justify-center gap-1.5 min-w-[140px] bg-amber-50 hover:bg-amber-100 border border-amber-200 hover:border-amber-300 text-amber-700 hover:text-amber-800 text-[13px] font-semibold px-4 py-2 rounded-lg shadow-sm shadow-amber-900/10 transition-all"
           aria-label="Impulsionar grupo — pacotes VIP">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" class="w-3.5 h-3.5 text-amber-500 shrink-0">
            <path d="M239.54,98.11l-36.88,86.07a16,16,0,0,1-14.66,9.82H68a16,16,0,0,1-14.66-9.82L16.46,98.11A8,8,0,0,1,24.63,86.3l57,21.36,39.11-65.18a8,8,0,0,1,13.72,0l39.11,65.18,57-21.36a8,8,0,0,1,8.17,11.81Z"></path>
          </svg>
          Impulsionar
        </a>

        {{-- Enviar Grupo: CTA primário verde --}}
        <a href="/enviar-grupo"
           class="hidden sm:inline-flex items-center justify-center gap-1.5 min-w-[140px] bg-[#25D366] hover:bg-[#1da851] border border-[#25D366] hover:border-[#1da851] text-white text-[13px] font-semibold px-4 py-2 rounded-lg shadow-sm shadow-green-900/20 transition-colors">
          <x-heroicon-s-plus class="w-4 h-4" />
          Enviar Grupo
        </a>

        {{-- Divider (md+) --}}
        <div class="hidden md:block w-px h-6 bg-slate-700/60 mx-1"></div>

        {{-- Hamburger / Menu completo --}}
        <button @click="mobileMenuOpen = true"
                class="flex items-center justify-center w-10 h-10 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all"
                aria-label="Abrir Menu">
          <x-heroicon-o-bars-3 class="w-5 h-5" />
        </button>

      </div>
    </div>

    {{-- ── Busca mobile (abaixo da linha principal, só < md) ─────────────── --}}
    <div class="md:hidden border-t border-slate-800/60 px-4 py-2.5" x-data="{ q: '{{ request('q', '') }}' }">
      <form action="{{ request()->is('blog') || request()->is('blog/*') ? '/blog' : '/buscar' }}" method="GET" class="relative">
        <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
        <input type="text" name="q" x-model="q"
          placeholder="Buscar grupos de WhatsApp..."
          class="w-full bg-slate-800/70 border border-slate-700/50 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none focus:border-[#25D366]/70 focus:ring-1 focus:ring-[#25D366]/30 transition-all" />
      </form>
    </div>

  </header>

  <!-- NAVEGAÇÃO DE CATEGORIAS EXPANSÍVEL GLOBAL (apenas na home e lista de categorias) -->
  @if(request()->is('/') || request()->is('categoria/*') || request()->is('buscar') || request()->is('grupos-*') || request()->is('grupo/*'))
  <div class="bg-white border-b border-slate-200/60 shadow-sm relative z-40" x-data="{ expanded: false }">
    <div class="max-w-[1400px] mx-auto px-4 py-6">
      
      <!-- Grid de Categorias -->
      <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-x-2 gap-y-8 transition-all duration-300"
           :class="expanded ? 'max-h-[2000px]' : 'max-h-[65px] overflow-hidden'">
        @foreach($categories ?? [] as $index => $cat)
          @php
            $catSlug = is_array($cat) ? ($cat['slug'] ?? '') : ($cat->slug ?? '');
            $catName = is_array($cat) ? ($cat['name'] ?? ($cat['label'] ?? '')) : ($cat->name ?? '');
            $heroiconName = \App\Models\Category::getHeroiconBySlug($catSlug);
          @endphp
          <a href="/categoria/{{ $catSlug }}" 
             class="flex flex-col items-center text-center group transition-transform hover:-translate-y-1">
             <div class="mb-2 transition-colors {{ request()->is('categoria/'.$catSlug) ? 'text-[#25D366]' : 'text-slate-700 group-hover:text-[#25D366]' }}">
               <x-dynamic-component :component="$heroiconName" class="w-7 h-7 mx-auto" />
             </div>
             <span class="text-[11px] font-bold transition-colors leading-tight line-clamp-2 px-1
                          {{ request()->is('categoria/'.$catSlug) ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900' }}">
               {{ $catName }}
             </span>
          </a>
        @endforeach
      </div>

      <!-- Botão Mais/Menos -->
      <div class="flex justify-center mt-6 -mb-9">
        <button @click="expanded = !expanded" 
                class="bg-slate-600 hover:bg-slate-700 text-white text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-md shadow-md transition-colors flex items-center gap-1 z-10 relative">
          <span x-text="expanded ? 'Menos Categorias' : 'Mais Categorias'"></span>
          <x-heroicon-m-chevron-down class="w-3 h-3 transition-transform duration-300" x-bind:class="expanded ? 'rotate-180' : ''" />
        </button>
      </div>

    </div>
  </div>
  @endif

  <!-- BODY: sem sidebar lateral (full-width) -->
  <div style="max-width: 1400px; margin: 0 auto; min-height: calc(100vh - 120px); padding: 24px 16px; padding-bottom: 100px;" class="md:pb-6">
    <!-- CONTEÚDO PRINCIPAL -->
    <main style="flex: 1; min-width: 0;">
      @if(session('success'))
        <div style="background: rgba(37, 211, 102, 0.1); border: 1px solid var(--whatsapp); border-radius: var(--radius-md);
                    padding: 12px 16px; margin-bottom: 20px; font-size: 14px; color: var(--whatsapp); font-weight: 600; display:flex; align-items:center; gap:8px;">
          <x-heroicon-s-check-circle class="w-5 h-5" /> {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div style="background: rgba(226, 75, 74, 0.1); border: 1px solid #E24B4A; border-radius: var(--radius-md);
                    padding: 12px 16px; margin-bottom: 20px; font-size: 14px; color: #E24B4A; font-weight: 600; display:flex; align-items:center; gap:8px;">
          <x-heroicon-s-x-circle class="w-5 h-5" /> {{ session('error') }}
        </div>
      @endif
      @yield('content')

    </main>
  </div>

  <!-- FOOTER -->
  <x-footer />



  <x-offcanvas />

  <!-- MOBILE BOTTOM NAVIGATION — mesmo estilo escuro da navbar/footer -->
  <nav class="fixed bottom-0 left-0 right-0 bg-slate-900 border-t border-slate-800 shadow-2xl md:hidden z-40">
    <div class="flex items-center justify-around h-20 max-w-full px-2">

      <!-- Home -->
      <a href="/" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg transition-all
         {{ request()->is('/') ? 'text-[#25D366]' : 'text-slate-400 hover:text-[#25D366]' }}">
        <x-heroicon-o-home class="w-6 h-6" />
        <span class="text-[10px] font-bold mt-0.5">Home</span>
      </a>

      <!-- Meus Grupos -->
      <a href="/meus-grupos" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg transition-all
         {{ request()->is('meus-grupos') ? 'text-[#25D366]' : 'text-slate-400 hover:text-[#25D366]' }}">
        <x-heroicon-o-users class="w-6 h-6" />
        <span class="text-[10px] font-bold mt-0.5">Grupos</span>
      </a>

      <!-- FAB Enviar Grupo -->
      <a href="/enviar-grupo" class="flex flex-col items-center justify-center -translate-y-4 relative">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
          <x-heroicon-s-plus class="w-8 h-8 text-white" />
        </div>
        <span class="text-[10px] font-bold mt-1 text-slate-400">Enviar</span>
      </a>

      <!-- Impulsionar (Pacotes VIP) -->
      <a href="/pacotes-vip" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg transition-all
         {{ request()->is('pacotes-vip') ? 'text-amber-400' : 'text-slate-400 hover:text-amber-400' }}">
        <x-heroicon-s-rocket-launch class="w-6 h-6" />
        <span class="text-[10px] font-bold mt-0.5">Impulsionar</span>
      </a>

      <!-- Menu -->
      <button @click="mobileMenuOpen = true" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-400 hover:text-slate-200 transition-all">
        <x-heroicon-o-ellipsis-horizontal class="w-6 h-6" />
        <span class="text-[10px] font-bold mt-0.5">Menu</span>
      </button>

    </div>
  </nav>

  @stack('scripts')

  {{-- Honeypot anti-scraper: invisível para humanos, visível no HTML para bots.
       Bots que rastreiam o DOM e seguem este link são banidos por 24h. --}}
  <a href="/hp" aria-hidden="true" tabindex="-1"
     style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;opacity:0;pointer-events:none;"
     rel="nofollow">&#x200B;</a>
</body>
</html>




