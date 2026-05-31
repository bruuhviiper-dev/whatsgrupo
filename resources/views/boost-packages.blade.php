@extends('layouts.app')

@section('title', 'Pacotes Super VIP — Impulsione seu Grupo de WhatsApp | WhatsGrupos')
@section('description', 'Coloque seu grupo de WhatsApp no topo do diretório! Escolha seu pacote VIP e atraia mais membros com o Super Impulso do WhatsGrupos.')

@push('head')
<style>
  :root { --vip-page-bg: #0f1117; }

  .vip-page-wrapper {
    background: #0f1117;
    min-height: 100vh;
    position: relative;
  }
  .vip-page-wrapper::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
      radial-gradient(ellipse 80% 50% at 50% -10%, rgba(234,179,8,0.12) 0%, transparent 60%),
      radial-gradient(ellipse 60% 40% at 80% 80%, rgba(168,85,247,0.07) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
  }

  .vip-section { position: relative; z-index: 1; }

  /* Hero badge pulse */
  .badge-live { animation: badgePulse 2.5s ease-in-out infinite; }
  @keyframes badgePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
    50% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
  }

  /* Cards */
  .pkg-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 1.5rem;
    transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
    position: relative;
    overflow: hidden;
  }
  .pkg-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, transparent 50%);
    pointer-events: none;
  }
  .pkg-card:hover { transform: translateY(-6px); }

  .pkg-card.popular {
    background: rgba(234,179,8,0.06);
    border-color: rgba(234,179,8,0.35);
    box-shadow: 0 0 40px rgba(234,179,8,0.12), 0 20px 60px rgba(0,0,0,0.4);
  }
  .pkg-card.popular:hover {
    box-shadow: 0 0 60px rgba(234,179,8,0.2), 0 30px 80px rgba(0,0,0,0.5);
  }

  /* Ribbon / Tarja */
  .ribbon-wrap {
    position: absolute;
    top: 0; right: 0;
    width: 90px; height: 90px;
    overflow: hidden;
    border-radius: 0 1.5rem 0 0;
    z-index: 2;
    pointer-events: none;
  }
  .ribbon-label {
    position: absolute;
    top: 22px; right: -26px;
    width: 110px;
    transform: rotate(45deg);
    font-size: 8px;
    font-weight: 900;
    text-align: center;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 4px 0;
    color: #fff;
  }

  /* Price */
  .price-amount { font-variant-numeric: tabular-nums; }

  /* CTA buttons */
  .btn-cta {
    display: flex; align-items: center; justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 14px 20px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 15px;
    letter-spacing: 0.01em;
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
    position: relative; z-index: 1;
    cursor: pointer;
    text-decoration: none;
    border: none;
  }
  .btn-cta:hover { transform: translateY(-2px); }
  .btn-cta:active { transform: translateY(0); }

  .btn-popular {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    box-shadow: 0 8px 30px rgba(245,158,11,0.4);
  }
  .btn-popular:hover { box-shadow: 0 12px 40px rgba(245,158,11,0.55); }

  /* How-it-works dark */
  .how-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 1rem;
  }
  .how-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }

  /* Testimonials */
  .testi-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 1rem;
  }

  /* FAQ */
  .faq-item {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 1rem;
    overflow: hidden;
  }

  /* Scrollbar */
  ::-webkit-scrollbar { width: 6px; }
  ::-webkit-scrollbar-track { background: #0f1117; }
  ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }

  /* Mobile bottom padding */
  @media (max-width: 767px) {
    .mobile-pb { padding-bottom: 90px; }
  }
</style>
@endpush

@section('content')
<div class="vip-page-wrapper mobile-pb">

  {{-- ===== HERO ===== --}}
  <div class="vip-section text-center pt-12 pb-10 px-4 max-w-3xl mx-auto">

    {{-- Live badge --}}
    @if ($boostedThisMonth > 0)
    <div class="inline-flex items-center gap-2 bg-green-500/10 border border-green-500/30 rounded-full px-4 py-1.5 text-green-400 text-xs font-bold mb-6 badge-live">
      <span class="w-2 h-2 rounded-full bg-green-400 animate-ping"></span>
      {{ number_format($boostedThisMonth) }} grupos impulsionados este mês
    </div>
    @else
    <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 rounded-full px-4 py-1.5 text-amber-400 text-xs font-bold mb-6">
      <x-heroicon-s-star class="w-4 h-4 text-amber-400" /> Super VIP — Destaque Premium
    </div>
    @endif

    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-4 leading-tight tracking-tight">
      Seu Grupo no
      <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500"> Topo do Site</span><br>
      em Minutos
    </h1>
    <p class="text-slate-400 text-base sm:text-lg max-w-xl mx-auto font-medium leading-relaxed mb-2">
      Borda dourada, badge VIP e primeiras posições por <strong class="text-white">12h por impulso</strong>. Mais membros garantidos.
    </p>
  </div>

  {{-- ===== GRID DE PACOTES ===== --}}
  <div class="vip-section max-w-[1400px] mx-auto px-4 pb-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5 items-end">
      @foreach ($packages as $package)
      @php
        $name = strtolower($package->name);
        $theme = match(true) {
          str_contains($name, 'bronze')   => ['ribbon' => 'linear-gradient(135deg,#92400e,#b45309)', 'accent' => '#b45309', 'btnBg' => 'linear-gradient(135deg,#92400e,#b45309)', 'glow' => 'rgba(180,83,9,0.3)', 'text' => '#f97316', 'badge' => 'rgba(180,83,9,0.15)'],
          str_contains($name, 'prata')    => ['ribbon' => 'linear-gradient(135deg,#475569,#64748b)', 'accent' => '#94a3b8', 'btnBg' => 'linear-gradient(135deg,#475569,#64748b)', 'glow' => 'rgba(100,116,139,0.3)', 'text' => '#94a3b8', 'badge' => 'rgba(100,116,139,0.15)'],
          str_contains($name, 'ouro')     => ['ribbon' => 'linear-gradient(135deg,#b45309,#d97706)', 'accent' => '#f59e0b', 'btnBg' => 'linear-gradient(135deg,#d97706,#f59e0b)', 'glow' => 'rgba(245,158,11,0.4)', 'text' => '#fbbf24', 'badge' => 'rgba(245,158,11,0.15)'],
          str_contains($name, 'diamante') => ['ribbon' => 'linear-gradient(135deg,#0e7490,#06b6d4)', 'accent' => '#22d3ee', 'btnBg' => 'linear-gradient(135deg,#0891b2,#22d3ee)', 'glow' => 'rgba(34,211,238,0.3)', 'text' => '#22d3ee', 'badge' => 'rgba(34,211,238,0.1)'],
          str_contains($name, 'estrela')  => ['ribbon' => 'linear-gradient(135deg,#7c3aed,#a855f7)', 'accent' => '#a855f7', 'btnBg' => 'linear-gradient(135deg,#7c3aed,#a855f7)', 'glow' => 'rgba(168,85,247,0.35)', 'text' => '#c084fc', 'badge' => 'rgba(168,85,247,0.12)'],
          default => ['ribbon' => 'linear-gradient(135deg,#334155,#475569)', 'accent' => '#64748b', 'btnBg' => 'linear-gradient(135deg,#334155,#475569)', 'glow' => 'rgba(100,116,139,0.2)', 'text' => '#94a3b8', 'badge' => 'rgba(100,116,139,0.1)'],
        };
      @endphp

      <div class="pkg-card {{ $package->is_popular ? 'popular' : '' }} flex flex-col"
           style="{{ $package->is_popular ? 'transform: scale(1.03);' : '' }}">

        {{-- Ribbon --}}
        <div class="ribbon-wrap">
          <div class="ribbon-label" style="background: {{ $theme['ribbon'] }};">{{ $package->name }}</div>
        </div>

        {{-- Popular badge --}}
        @if ($package->is_popular)
          <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 z-10 whitespace-nowrap">
            <span class="inline-flex items-center gap-1.5 bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-900 text-[11px] font-black px-4 py-1.5 rounded-full shadow-lg shadow-amber-500/40 uppercase tracking-widest">
              <x-heroicon-s-sparkles class="w-3.5 h-3.5" /> Mais Popular
            </span>
          </div>
        @endif

        <div class="p-6 flex flex-col h-full {{ $package->is_popular ? 'pt-8' : 'pt-6' }}">

          {{-- Ícone e nome --}}
          <div class="text-center mb-5 relative z-10">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-3"
                 style="background: {{ $theme['badge'] }};">
              <span class="text-2xl">
                @if(str_contains($name,'bronze')) 🥉
                @elseif(str_contains($name,'prata')) 🥈
                @elseif(str_contains($name,'ouro')) 🥇
                @elseif(str_contains($name,'diamante')) 💎
                @elseif(str_contains($name,'estrela')) ⭐
                @else 🚀
                @endif
              </span>
            </div>
            <p class="text-xs font-black uppercase tracking-widest mb-2" style="color: {{ $theme['text'] }}">{{ $package->name }}</p>
            <div class="text-white">
              <span class="text-5xl font-black price-amount">{{ $package->boosts_count }}</span>
            </div>
            <p class="text-slate-500 text-xs font-semibold mt-0.5 uppercase tracking-wider">impulso{{ $package->boosts_count > 1 ? 's' : '' }} VIP</p>
          </div>

          {{-- Preço --}}
          <div class="text-center mb-6 pb-5 border-b border-white/[0.07]">
            @if ($package->savings_percent > 0)
              <div class="flex justify-center items-center gap-2 mb-1">
                <span class="text-slate-600 text-sm line-through font-semibold">{{ $package->formatted_original_price }}</span>
                <span class="text-[10px] font-black px-2 py-0.5 rounded-full text-emerald-400 bg-emerald-500/10 border border-emerald-500/20">
                  {{ $package->discount_label }}
                </span>
              </div>
            @else
              <div class="h-5 mb-1"></div>
            @endif
            <p class="text-3xl font-black text-white price-amount">{{ $package->formatted_price }}</p>
            <p class="text-slate-600 text-[10px] font-semibold mt-1 uppercase tracking-wider">pagamento único</p>
          </div>

          {{-- Benefícios --}}
          <ul class="space-y-2.5 mb-6 flex-1">
            @foreach([
              [$package->boosts_count . ' uso' . ($package->boosts_count > 1 ? 's' : '') . ' de impulso', '⚡'],
              ['12h de destaque VIP por uso', '⏱'],
              ['Borda dourada em destaque', '✨'],
              ['Badge VIP exclusivo', '🏅'],
              ['Código no e-mail imediatamente', '📧'],
              ['Sem validade — use quando quiser', '♾️'],
            ] as [$benefit, $emoji])
            <li class="flex items-center gap-2.5 text-slate-400 text-xs font-medium">
              <span class="text-sm leading-none">{{ $emoji }}</span>
              <span>{{ $benefit }}</span>
            </li>
            @endforeach
          </ul>

          {{-- CTA --}}
          <a href="{{ route('boost.checkout', $package->slug) }}"
             id="btn-comprar-{{ $package->slug }}"
             class="btn-cta {{ $package->is_popular ? 'btn-popular' : '' }}"
             style="{{ !$package->is_popular ? 'background: ' . $theme['btnBg'] . '; color: #fff; box-shadow: 0 6px 20px ' . $theme['glow'] . ';' : '' }}">
            Comprar Agora
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
          </a>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Garantias embaixo dos cards --}}
    <div class="flex flex-wrap justify-center items-center gap-x-8 gap-y-3 mt-8 text-slate-500 text-xs font-semibold">
      <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg> Pagamento 100% seguro</span>
      <span class="flex items-center gap-2"><svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Código no e-mail imediatamente</span>
      <span class="flex items-center gap-2"><svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Sem prazo de validade</span>
      <span class="flex items-center gap-2"><svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Ativação instantânea</span>
    </div>
  </div>

  {{-- ===== COMO FUNCIONA ===== --}}
  <div class="vip-section border-y border-white/[0.06] py-16 mb-16" style="background: rgba(255,255,255,0.02);">
    <div class="max-w-5xl mx-auto px-4">
      <div class="text-center mb-10">
        <p class="text-amber-400 text-xs font-black uppercase tracking-widest mb-2">Como funciona</p>
        <h2 class="text-2xl sm:text-3xl font-black text-white">Do pagamento ao topo em minutos</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach([
          ['🛒', 'amber', 'Escolha o Pacote', 'Selecione a quantidade de impulsos. Pacotes maiores = descontos maiores.', '01'],
          ['💳', 'blue', 'Pagamento Seguro', 'PIX aprovado em segundos ou Cartão de Crédito com segurança total.', '02'],
          ['📧', 'emerald', 'Receba o Código', 'Código exclusivo de 12 dígitos enviado automaticamente ao seu e-mail.', '03'],
          ['📋', 'amber', 'Acesse Meus Grupos', 'Na aba "Meus Grupos", informe seu e-mail para ver seus grupos cadastrados.', '04'],
          ['⚡', 'orange', 'Aplique o Impulso', 'Clique em VIP no seu grupo e cole o código de 12 dígitos.', '05'],
          ['🏆', 'yellow', 'Apareça no Topo!', 'Seu grupo pula para as primeiras posições por 12 horas inteiras!', '06'],
        ] as [$emoji, $color, $title, $desc, $step])
        @php
          $colors = ['amber'=>'rgba(245,158,11,0.15)', 'blue'=>'rgba(59,130,246,0.15)', 'emerald'=>'rgba(16,185,129,0.15)', 'orange'=>'rgba(249,115,22,0.15)', 'yellow'=>'rgba(234,179,8,0.15)', 'purple'=>'rgba(168,85,247,0.15)'];
        @endphp
        <div class="how-card p-5 flex gap-4">
          <div class="how-icon" style="background: {{ $colors[$color] ?? 'rgba(255,255,255,0.08)' }}">
            <span class="text-xl">{{ $emoji }}</span>
          </div>
          <div class="min-w-0">
            <p class="text-slate-600 text-[10px] font-black uppercase tracking-widest mb-0.5">Passo {{ $step }}</p>
            <p class="text-white font-bold text-sm mb-1 leading-tight">{{ $title }}</p>
            <p class="text-slate-500 text-xs leading-relaxed font-medium">{{ $desc }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ===== DEPOIMENTOS + FAQ ===== --}}
  <div class="vip-section max-w-[1200px] mx-auto px-4 mb-16 grid grid-cols-1 lg:grid-cols-2 gap-10">

    {{-- Depoimentos --}}
    <div>
      <p class="text-amber-400 text-xs font-black uppercase tracking-widest mb-2">Depoimentos</p>
      <h2 class="text-xl font-black text-white mb-6">O que dizem os admins</h2>
      <div class="space-y-3">
        @foreach([
          ['Carlos M.', 'Admin de grupo de games', 'Comprei o pacote Ouro e em menos de 1 hora meu grupo foi de 50 para mais de 200 membros! Vale muito a pena!', '🎮'],
          ['Ana P.', 'Dona de loja virtual', 'Uso o WhatsGrupos para divulgar meu grupo de ofertas. O VIP é incrível, apareço sempre em primeiro!', '🛍️'],
          ['Pedro H.', 'Youtuber e criador de conteúdo', 'Compro o Diamante todo mês. O retorno em visualizações e membros é impressionante. Recomendo!', '🎬'],
        ] as $t)
        <div class="testi-card p-5">
          <div class="flex gap-0.5 mb-3">
            @for($s=0;$s<5;$s++) <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> @endfor
          </div>
          <p class="text-slate-400 text-sm leading-relaxed mb-4 font-medium">"{{ $t[2] }}"</p>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-base" style="background: rgba(255,255,255,0.07);">{{ $t[3] }}</div>
            <div>
              <p class="text-white font-bold text-sm">{{ $t[0] }}</p>
              <p class="text-slate-600 text-xs font-semibold">{{ $t[1] }}</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- FAQ --}}
    <div>
      <p class="text-purple-400 text-xs font-black uppercase tracking-widest mb-2">Dúvidas</p>
      <h2 class="text-xl font-black text-white mb-6">Perguntas Frequentes</h2>
      <div class="space-y-2" x-data="{ open: 0 }">
        @foreach([
          ['Por quanto tempo meu grupo fica em destaque?', 'Cada impulso deixa seu grupo destacado por 12 horas no topo da página inicial e da sua categoria, com borda dourada e badge VIP visível.'],
          ['Posso usar impulsos em vários grupos?', 'Sim! Se você for dono de vários grupos aprovados, pode distribuir os impulsos entre eles usando o mesmo código VIP.'],
          ['Meu grupo precisa estar aprovado?', 'Sim, apenas grupos com status "Aprovado" podem receber impulsos. Se acabou de ser enviado, aguarde a aprovação.'],
          ['Os impulsos expiram?', 'Não! Os impulsos não têm prazo de validade. Compre hoje e use quando quiser, sem pressa.'],
          ['O que acontece após as 12h?', 'O grupo volta ao posicionamento padrão. Aplique outro impulso a qualquer momento para voltar ao topo!'],
        ] as $i => $faq)
        <div class="faq-item">
          <button type="button"
                  class="w-full flex items-center justify-between gap-4 p-4 text-left focus:outline-none hover:bg-white/[0.03] transition-colors"
                  @click="open = open === {{ $i }} ? null : {{ $i }}">
            <span class="text-white font-semibold text-sm">{{ $faq[0] }}</span>
            <svg class="w-4 h-4 text-slate-500 shrink-0 transition-transform duration-300" x-bind:class="{ 'rotate-180': open === {{ $i }} }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div x-show="open === {{ $i }}" x-collapse>
            <div class="px-4 pb-4 pt-0">
              <p class="text-slate-500 text-sm leading-relaxed font-medium">{{ $faq[1] }}</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ===== CTA FINAL ===== --}}
  <div class="vip-section max-w-3xl mx-auto px-4 pb-20">
    <div class="relative rounded-3xl overflow-hidden text-center px-6 py-12 sm:px-12"
         style="background: linear-gradient(135deg, rgba(245,158,11,0.15) 0%, rgba(168,85,247,0.1) 100%); border: 1px solid rgba(245,158,11,0.25);">
      <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse 60% 60% at 50% 0%, rgba(245,158,11,0.15), transparent);"></div>
      <div class="relative z-10">
        <span class="text-4xl mb-4 block">🚀</span>
        <h2 class="text-2xl sm:text-3xl font-black text-white mb-3 tracking-tight">Pronto para dominar o topo?</h2>
        <p class="text-slate-400 text-base mb-8 max-w-lg mx-auto font-medium">
          Milhares de admins já multiplicaram o tamanho dos seus grupos. Escolha o seu pacote agora.
        </p>
        <button onclick="window.scrollTo({top:0,behavior:'smooth'})"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-900 font-black px-8 py-4 rounded-xl text-base shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 hover:-translate-y-1 transition-all">
          Ver Pacotes VIP
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
        </button>
      </div>
    </div>
  </div>

</div>
@endsection
