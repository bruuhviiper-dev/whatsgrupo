<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Frases para Status — WhatsGrupos')</title>
  <meta name="description" content="@yield('description', 'Encontre as melhores frases para status, reflexão, amor, motivação e mais.')">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="@yield('canonical', url()->current())">
  
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
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="WhatsGrupos Frases">
  <meta property="og:title" content="@yield('title', 'Frases para Status')">
  <meta property="og:description" content="@yield('description', 'Encontre as melhores frases para status...')">
  <meta property="og:image" content="@yield('og_image', asset('images/og-phrases.png'))">
  <meta property="og:url" content="{{ url()->current() }}">

  <!-- PWA Manifest & Theme -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <meta name="theme-color" content="#25D366">

  <!-- Estilos Globais -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
  
  {{-- Tailwind CSS CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#25D366',
            secondary: '#1da851',
          }
        }
      }
    }
  </script>

  {{-- Alpine.js CDN --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  @stack('head')

  {{-- Google Analytics (GA4) --}}
  <x-google-analytics />

  {{-- JSON-LD global (Organization + WebSite + SearchAction) --}}
  <x-seo.global />
  @stack('schema')
  @php
    use App\Models\Setting;
    $adsenseEnabled = Setting::adsenseEnabled();
  @endphp
  @if($adsenseEnabled)
    {!! Setting::adsenseMetaTag() !!}
    {!! Setting::adsenseScript() !!}
  @endif
</head>
<body class="bg-slate-50" x-data="{ mobileMenuOpen: false }">

  <!-- HEADER ESPECÍFICO PARA FRASES (Verde Moderno) -->
  <header class="bg-[#25D366] sticky top-0 z-50 shadow-md">
    <div class="max-w-[1400px] mx-auto px-4 py-3 flex items-center justify-between gap-4">
      
      <!-- Logo -->
      <a href="{{ route('phrases.index') }}" class="flex items-center gap-2 text-white decoration-none hover:opacity-90 transition-opacity">
        <x-heroicon-s-chat-bubble-left-ellipsis class="w-7 h-7 sm:w-8 sm:h-8" />
        <div class="flex flex-col leading-tight">
          <span class="font-black text-base sm:text-lg tracking-tight">FRASES<span class="text-green-100 font-bold">PARA</span>STATUS</span>
          <span class="text-[9px] sm:text-[10px] font-semibold text-green-100 opacity-90 -mt-1 tracking-widest">BY WHATSGRUPOS</span>
        </div>
      </a>

      <!-- Ações da Direita -->
      <div class="flex items-center gap-2 sm:gap-4">
        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-2">
          <a href="{{ route('phrases.create') }}" class="bg-white text-[#25D366] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
            <x-heroicon-s-pencil-square class="w-4 h-4" />
            Enviar Frase
          </a>
          <a href="{{ route('phrases.myPhrases') }}" class="bg-transparent border border-white/30 text-white px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider hover:bg-white/10 transition-colors flex items-center gap-1.5">
            <x-heroicon-s-bookmark class="w-4 h-4" />
            Minhas Frases
          </a>
        </nav>

        <!-- Sandwich Menu -->
        <button @click="mobileMenuOpen = true" class="text-white hover:bg-white/10 p-1.5 rounded-md transition-colors">
          <x-heroicon-o-bars-3 class="w-7 h-7 sm:w-8 sm:h-8" />
        </button>
      </div>

    </div>
  </header>

  <!-- ADSENSE TOP SLOT -->
  <div class="max-w-[1400px] mx-auto px-4 w-full">
    <x-adsense class="my-3" />
  </div>

  <!-- BODY PRINCIPAL -->
  <div class="max-w-[1400px] mx-auto px-4 pb-24 md:pb-12 min-h-[calc(100vh-250px)]">
    @if(session('success'))
      <div class="bg-green-100 border border-green-500 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 font-bold text-sm">
        <x-heroicon-s-check-circle class="w-5 h-5" /> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 font-bold text-sm">
        <x-heroicon-s-x-circle class="w-5 h-5" /> {{ session('error') }}
      </div>
    @endif

    @yield('content')
  </div>

  <!-- ADSENSE BOTTOM SLOT -->
  <div class="max-w-[1400px] mx-auto px-4 w-full">
    <x-adsense class="my-3" />
  </div>

  <!-- FOOTER -->
  <x-footer />

  <!-- Off-Canvas Menu Sidebar -->
  <x-offcanvas />
  </div>

  <!-- MOBILE APP-LIKE BOTTOM NAVIGATION (Apenas Mobile) -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 shadow-2xl md:hidden z-40">
    <div class="flex items-center justify-around h-20 max-w-full px-2">
      
      <!-- Home Frases -->
      <a href="/frases" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('frases') ? 'text-[#25D366] bg-green-50' : '' }}">
        <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6" />
        <span class="text-[10px] font-bold mt-0.5">Frases</span>
      </a>

      <!-- Minhas Frases -->
      <a href="/minhas-frases" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('minhas-frases') ? 'text-[#25D366] bg-green-50' : '' }}">
        <x-heroicon-o-bookmark class="w-6 h-6" />
        <span class="text-[10px] font-bold mt-0.5">Minhas</span>
      </a>

      <!-- FAB Enviar Frase - Centro destacado -->
      <a href="{{ route('phrases.create') }}" class="flex flex-col items-center justify-center -translate-y-4 relative">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
          <x-heroicon-s-pencil-square class="w-8 h-8 text-white" />
        </div>
        <span class="text-[10px] font-bold mt-1 text-slate-600">Enviar</span>
      </a>

      <!-- Grupos (Link de volta) -->
      <a href="/" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all">
        <x-heroicon-o-home class="w-6 h-6" />
        <span class="text-[10px] font-bold mt-0.5">Grupos</span>
      </a>

      <!-- Menu -->
      <button @click="mobileMenuOpen = true" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
        <span class="text-[10px] font-bold mt-0.5">Menu</span>
      </button>

    </div>
  </nav>

  @stack('scripts')
</body>
</html>



