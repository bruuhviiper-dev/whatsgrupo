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
    <link rel="icon" type="image/png" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
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

  <!-- HEADER -->
  <header style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 50;">
    <div style="max-width: 1400px; margin: 0 auto; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">

      <!-- Logo -->
      <div style="display: flex; align-items: center; gap: 12px;">
        <a href="/" class="flex items-center gap-2.5 text-decoration-none group" style="text-decoration: none;">
          <div class="w-10 h-10 bg-[#25D366]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#25D366]/20 transition-colors duration-300">
            <svg class="w-6 h-6 text-[#25D366]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 21a9 9 0 1 0-9-9c0 1.488.36 2.89 1 4.127L3 21l4.873-1c1.236.64 2.64 1 4.127 1Z" />
              <path d="M15.5 10c-.5-1-1.5-1.5-2.5-1.5s-2 .5-2.5 1.5" />
              <path d="M9 14.5c.5 1 1.5 1.5 2.5 1.5s2-.5 2.5-1.5" />
            </svg>
          </div>
          <span class="font-black text-[24px] text-slate-800 tracking-tight" style="font-family: 'Outfit', sans-serif;">Whats<span class="text-[#25D366]">Grupos</span></span>
        </a>
      </div>

      <!-- Busca (Alpine.js) centralizada e larga -->
      <div class="w-full md:w-[40%] lg:w-[48%] order-3 md:order-2" x-data="{ q: '{{ request('q', '') }}' }">
        <form action="{{ request()->is('blog') || request()->is('blog/*') ? '/blog' : '/buscar' }}" method="GET" style="position: relative;">
          <input
            type="text"
            name="q"
            x-model="q"
            placeholder="{{ request()->is('blog') || request()->is('blog/*') ? 'Buscar no blog...' : 'Buscar grupos ativos...' }}"
            style="width: 100%; background: #f1f5f9; border: 1px solid rgba(0,0,0,0.08); border-radius: var(--radius-sm);
                   padding: 10px 42px 10px 14px; color: #0f172a; font-size: 14px; outline: none; transition: all 0.15s;"
            x-on:focus="$el.style.borderColor='var(--whatsapp)'; $el.style.background='#fff'"
            x-on:blur="$el.style.borderColor='rgba(0,0,0,0.08)'; $el.style.background='#f1f5f9'"
          />
          <button type="submit" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                  background: none; border: none; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center;">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400 hover:text-slate-600 transition-colors" />
          </button>
        </form>
      </div>

      <!-- Nav links & Hamburger Menu Sandwich (Far Right) -->
      <div style="display: flex; align-items: center; gap: 12px;" class="order-2 md:order-3">
        <!-- Desktop Navigation Buttons (hidden on mobile) -->
        <nav class="hidden md:flex items-center gap-3">
          <a href="/enviar-grupo" style="background: var(--whatsapp); color: #fff;
             border-radius: var(--radius-sm); padding: 8px 16px; box-shadow: var(--shadow-whatsapp);
             font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: none; transition: background 0.15s;"
             onmouseover="this.style.background='var(--whatsapp-dark)'"
             onmouseout="this.style.background='var(--whatsapp)'">
            <x-heroicon-s-plus class="w-4 h-4" /> Enviar Grupo
          </a>
          <a href="/meus-grupos" style="background: #f1f5f9; border: 1px solid rgba(0,0,0,0.08);
             color: #0f172a; border-radius: var(--radius-sm); padding: 8px 16px;
             font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s;"
             onmouseover="this.style.background='#e2e8f0'"
             onmouseout="this.style.background='#f1f5f9'">
            <x-heroicon-o-users class="w-4 h-4" /> Meus Grupos
          </a>
          <a href="/pacotes-vip" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-amber-200 bg-amber-50 text-amber-700 font-semibold text-[13px] hover:bg-amber-100 hover:border-amber-300 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" class="w-4 h-4 text-amber-500 flex-shrink-0">
              <path d="M239.54,98.11l-36.88,86.07a16,16,0,0,1-14.66,9.82H68a16,16,0,0,1-14.66-9.82L16.46,98.11A8,8,0,0,1,24.63,86.3l57,21.36,39.11-65.18a8,8,0,0,1,13.72,0l39.11,65.18,57-21.36a8,8,0,0,1,8.17,11.81Z"></path>
            </svg>
            Impulsionar
          </a>
        </nav>

        <!-- Menu Sandwich Button (Visible on both Mobile and Desktop) -->
        <button @click="mobileMenuOpen = true" class="text-slate-900 hover:text-[#25D366] transition-colors p-1" aria-label="Abrir Menu">
          <x-heroicon-o-bars-3 class="w-7 h-7" />
        </button>
      </div>
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

  <!-- MOBILE APP-LIKE BOTTOM NAVIGATION (Apenas Mobile) -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 shadow-2xl md:hidden z-40">
    <div class="flex items-center justify-around h-20 max-w-full px-2">
      
      <!-- Home -->
      <a href="/" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('/') ? 'text-[#25D366] bg-green-50' : '' }}">
        <x-heroicon-o-home class="w-6 h-6" />
        <span class="text-xs font-bold mt-0.5">Home</span>
      </a>

      <!-- Meus Grupos -->
      <a href="/meus-grupos" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('meus-grupos') ? 'text-[#25D366] bg-green-50' : '' }}">
        <x-heroicon-o-users class="w-6 h-6" />
        <span class="text-xs font-bold mt-0.5">Grupos</span>
      </a>

      <!-- FAB Enviar Grupo - Centro destacado -->
      <a href="/enviar-grupo" class="flex flex-col items-center justify-center -translate-y-4 relative">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
          <x-heroicon-s-plus class="w-8 h-8 text-white" />
        </div>
        <span class="text-xs font-bold mt-1 text-slate-600">Enviar</span>
      </a>

      <!-- Pacotes VIP -->
      <a href="/pacotes-vip" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-amber-500 hover:bg-amber-50 transition-all {{ request()->is('pacotes-vip') ? 'text-amber-500 bg-amber-50' : '' }}">
        <x-heroicon-o-star class="w-6 h-6" />
        <span class="text-xs font-bold mt-0.5">VIP</span>
      </a>

      <!-- Menu -->
      <button @click="mobileMenuOpen = true" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all">
        <x-heroicon-o-ellipsis-horizontal class="w-6 h-6" />
        <span class="text-xs font-bold mt-0.5">Menu</span>
      </button>

    </div>
  </nav>

  @stack('scripts')
</body>
</html>




