@php
    $name = strtolower($package->name);
    $theme = match(true) {
        str_contains($name, 'bronze')   => ['grad' => 'from-orange-800 via-orange-700 to-amber-600', 'accent' => '#f97316', 'accentBg' => 'rgba(249,115,22,0.1)', 'accentBorder' => 'rgba(249,115,22,0.25)', 'pill' => 'bg-orange-500/10 text-orange-400 border-orange-500/20'],
        str_contains($name, 'prata')    => ['grad' => 'from-slate-600 via-slate-500 to-slate-400', 'accent' => '#94a3b8', 'accentBg' => 'rgba(148,163,184,0.1)', 'accentBorder' => 'rgba(148,163,184,0.25)', 'pill' => 'bg-slate-500/10 text-slate-400 border-slate-500/20'],
        str_contains($name, 'ouro')     => ['grad' => 'from-yellow-600 via-amber-500 to-yellow-400', 'accent' => '#fbbf24', 'accentBg' => 'rgba(251,191,36,0.08)', 'accentBorder' => 'rgba(251,191,36,0.25)', 'pill' => 'bg-amber-500/10 text-amber-400 border-amber-500/20'],
        str_contains($name, 'diamante') => ['grad' => 'from-cyan-700 via-cyan-500 to-teal-400', 'accent' => '#22d3ee', 'accentBg' => 'rgba(34,211,238,0.08)', 'accentBorder' => 'rgba(34,211,238,0.25)', 'pill' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20'],
        str_contains($name, 'estrela')  => ['grad' => 'from-purple-800 via-purple-600 to-pink-500', 'accent' => '#c084fc', 'accentBg' => 'rgba(192,132,252,0.08)', 'accentBorder' => 'rgba(192,132,252,0.25)', 'pill' => 'bg-purple-500/10 text-purple-400 border-purple-500/20'],
        default                         => ['grad' => 'from-slate-700 via-slate-600 to-slate-500', 'accent' => '#64748b', 'accentBg' => 'rgba(100,116,139,0.08)', 'accentBorder' => 'rgba(100,116,139,0.25)', 'pill' => 'bg-slate-500/10 text-slate-400 border-slate-500/20'],
    };
    $medalIcon = match(true) {
        str_contains($name, 'bronze')   => '🥉',
        str_contains($name, 'prata')    => '🥈',
        str_contains($name, 'ouro')     => '🥇',
        str_contains($name, 'diamante') => '💎',
        str_contains($name, 'estrela')  => '⭐',
        default                         => '🚀',
    };
@endphp

@extends('layouts.app')

@section('title', 'Checkout — Pacote ' . $package->name . ' | WhatsGrupos')
@section('description', 'Finalize sua compra do pacote ' . $package->name . ' com ' . $package->boosts_count . ' impulsos VIP.')

@push('head')
    <script src="https://js.stripe.com/v3/"></script>
    <style>
      :root { --accent: {{ $theme['accent'] }}; }

      .checkout-wrapper {
        background: #0f1117;
        min-height: 100vh;
        position: relative;
      }
      .checkout-wrapper::before {
        content: '';
        position: fixed; inset: 0;
        background: radial-gradient(ellipse 70% 50% at 70% 0%, {{ $theme['accentBg'] }} 0%, transparent 60%);
        pointer-events: none; z-index: 0;
      }

      .checkout-section { position: relative; z-index: 1; }

      /* Form card */
      .form-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 1.5rem;
        backdrop-filter: blur(8px);
      }

      /* Inputs dark */
      .dark-input {
        width: 100%;
        background: rgba(255,255,255,0.05);
        border: 1.5px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem;
        padding: 12px 16px 12px 44px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
      }
      .dark-input::placeholder { color: rgba(255,255,255,0.3); }
      .dark-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(var(--accent), 0.15);
        background: rgba(255,255,255,0.07);
      }

      /* Payment method cards */
      .pay-option {
        background: rgba(255,255,255,0.04);
        border: 2px solid rgba(255,255,255,0.08);
        border-radius: 1rem;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex; align-items: center; gap: 12px;
      }
      .pay-option:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.16); }
      .pay-option.selected-pix { border-color: #22c55e; background: rgba(34,197,94,0.08); box-shadow: 0 0 20px rgba(34,197,94,0.12); }
      .pay-option.selected-card { border-color: #3b82f6; background: rgba(59,130,246,0.08); box-shadow: 0 0 20px rgba(59,130,246,0.12); }

      /* Submit button */
      .btn-pay {
        width: 100%;
        padding: 16px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 16px;
        letter-spacing: 0.01em;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
      }
      .btn-pix  { background: linear-gradient(135deg, #16a34a, #22c55e); color: #fff; box-shadow: 0 8px 25px rgba(34,197,94,0.3); }
      .btn-pix:hover  { box-shadow: 0 12px 35px rgba(34,197,94,0.45); transform: translateY(-2px); }
      .btn-card { background: linear-gradient(135deg, #1d4ed8, #3b82f6); color: #fff; box-shadow: 0 8px 25px rgba(59,130,246,0.3); }
      .btn-card:hover { box-shadow: 0 12px 35px rgba(59,130,246,0.45); transform: translateY(-2px); }

      /* Sidebar summary */
      .summary-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 1.5rem;
        overflow: hidden;
      }

      /* Modal */
      .modal-bg { background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); }
      .modal-box {
        background: #171a24;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 1.5rem;
        box-shadow: 0 25px 80px rgba(0,0,0,0.8);
      }

      /* Steps in modal */
      .step-dot {
        width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 900; flex-shrink: 0;
        background: rgba(255,255,255,0.07);
        color: rgba(255,255,255,0.5);
      }

      /* Mobile padding */
      @media (max-width: 767px) { .mobile-pb { padding-bottom: 90px; } }

      /* Label */
      .field-label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.45);
        margin-bottom: 8px;
      }

      /* Sticky sidebar */
      @media (min-width: 1024px) {
        .sidebar-sticky { position: sticky; top: 90px; }
      }
    </style>
@endpush

@section('content')
<div class="checkout-wrapper mobile-pb" x-data="{
    paymentMethod: 'pix',
    copied: false,

    stripeModalOpen: false,
    stripeLoading: false,
    stripeClientSecret: '',
    stripePublishableKey: '',
    stripeError: '',
    stripeSimulated: false,
    stripeRedirectUrl: '',

    pixModalOpen: false,
    pixLoading: false,
    pixGenerated: false,
    pixQrCode: '',
    pixCopyPaste: '',
    pixOrderId: null,
    pixInterval: null,

    buyerName: '{{ old('buyer_name', '') }}',
    buyerEmail: '{{ old('buyer_email', '') }}',

    initStripeCheckout() {
        if (!this.buyerName || !this.buyerName.trim()) { alert('Por favor, preencha o seu Nome Completo.'); return; }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.buyerEmail || !this.buyerEmail.trim() || !emailRegex.test(this.buyerEmail)) { alert('Por favor, preencha um endereço de E-mail válido.'); return; }
        this.stripeLoading = true; this.stripeModalOpen = true; this.stripeError = ''; this.stripeSimulated = false;
        fetch('{{ route('boost.checkout-stripe-embedded', $package->slug) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ buyer_name: this.buyerName, buyer_email: this.buyerEmail })
        }).then(r => r.json()).then(data => {
            this.stripeLoading = false;
            if (data.success) {
                if (data.is_simulated) { this.stripeSimulated = true; this.stripeRedirectUrl = data.redirect_url; }
                else {
                    if (!data.publishable_key) { this.stripeError = 'Erro: Chave pública Stripe não configurada.'; return; }
                    this.stripeClientSecret = data.client_secret; this.stripePublishableKey = data.publishable_key;
                    setTimeout(() => {
                        try {
                            const stripe = Stripe(data.publishable_key);
                            stripe.initEmbeddedCheckout({ clientSecret: data.client_secret }).then(checkout => { checkout.mount('#stripe-checkout-container'); }).catch(err => { this.stripeError = 'Erro ao montar checkout Stripe: ' + err.message; });
                        } catch (err) { this.stripeError = 'Erro ao inicializar SDK Stripe: ' + err.message; }
                    }, 200);
                }
            } else { this.stripeError = data.message || 'Erro ao inicializar o processador de pagamentos.'; }
        }).catch(() => { this.stripeLoading = false; this.stripeError = 'Erro de conexão com a Stripe.'; });
    },

    generateAsaasPix() {
        if (!this.buyerName || !this.buyerName.trim()) { alert('Por favor, preencha o seu Nome Completo.'); return; }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.buyerEmail || !this.buyerEmail.trim() || !emailRegex.test(this.buyerEmail)) { alert('Por favor, preencha um endereço de E-mail válido.'); return; }
        this.pixLoading = true; this.pixGenerated = false;
        fetch('{{ route('boost.checkout-asaas-pix', $package->slug) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ buyer_name: this.buyerName, buyer_email: this.buyerEmail })
        }).then(r => r.json()).then(data => {
            this.pixLoading = false;
            if (data.success) { this.pixGenerated = true; this.pixQrCode = data.qr_code; this.pixCopyPaste = data.copy_paste; this.pixOrderId = data.order_id; this.startPolling(data.order_id); }
            else { alert('Erro ao gerar cobrança PIX. Tente novamente.'); }
        }).catch(() => { this.pixLoading = false; alert('Erro de conexão ao gerar o PIX.'); });
    },

    startPolling(orderId) {
        if (this.pixInterval) clearInterval(this.pixInterval);
        this.pixInterval = setInterval(() => {
            fetch('/pagamento/pix-status/' + orderId).then(r => r.json()).then(data => {
                if (data.status === 'paid' && data.redirect_url) { clearInterval(this.pixInterval); window.location.href = data.redirect_url; }
            }).catch(() => {});
        }, 5000);
    },

    copyPixCode() {
        navigator.clipboard.writeText(this.pixCopyPaste).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2500); });
    }
}">

<div class="checkout-section max-w-5xl mx-auto px-4 py-8">

  {{-- Breadcrumb --}}
  <nav class="flex items-center gap-2 text-xs text-slate-600 font-semibold mb-8">
    <a href="{{ route('boost.packages') }}" class="hover:text-amber-400 transition-colors flex items-center gap-1">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Pacotes VIP
    </a>
    <span class="text-slate-700">/</span>
    <span class="text-white font-bold">Checkout</span>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    {{-- ===== FORMULÁRIO (ESQUERDA) ===== --}}
    <div class="lg:col-span-7 order-2 lg:order-1">

      {{-- Título --}}
      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
             style="background: {{ $theme['accentBg'] }}; border: 1px solid {{ $theme['accentBorder'] }};">
          🔒
        </div>
        <div>
          <h1 class="text-2xl font-black text-white leading-tight">Finalizar Compra</h1>
          <p class="text-slate-500 text-xs font-semibold">Pagamento 100% seguro e criptografado</p>
        </div>
      </div>

      <div class="form-card p-6 sm:p-8">

        {{-- Resumo mobile do pacote --}}
        <div class="lg:hidden mb-6 pb-6 border-b border-white/[0.06] flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
               style="background: {{ $theme['accentBg'] }}; border: 1px solid {{ $theme['accentBorder'] }};">
            {{ $medalIcon }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-white font-black text-sm">Pacote {{ $package->name }}</p>
            <p class="text-slate-500 text-xs">{{ $package->boosts_count }} impulso{{ $package->boosts_count > 1 ? 's' : '' }} VIP · 12h cada</p>
          </div>
          <div class="text-right shrink-0">
            <p class="text-white font-black text-lg">{{ $package->formatted_price }}</p>
            @if($package->savings_percent > 0)
            <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">{{ $package->discount_label }}</span>
            @endif
          </div>
        </div>

        <div class="space-y-5">

          {{-- Nome --}}
          <div>
            <label class="field-label" for="buyer_name">Nome Completo <span class="text-red-400">*</span></label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              </div>
              <input type="text" id="buyer_name" name="buyer_name" x-model="buyerName" required
                     placeholder="Seu nome completo"
                     class="dark-input" style="border-color: rgba(255,255,255,0.1);">
            </div>
          </div>

          {{-- E-mail --}}
          <div>
            <label class="field-label" for="buyer_email">E-mail <span class="text-red-400">*</span></label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <input type="email" id="buyer_email" name="buyer_email" x-model="buyerEmail" required
                     placeholder="seu@email.com"
                     class="dark-input" style="border-color: rgba(255,255,255,0.1);">
            </div>
            <p class="text-slate-600 text-[11px] font-medium mt-1.5 flex items-center gap-1">
              <svg class="w-3 h-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
              O código de impulso será enviado para este e-mail após o pagamento
            </p>
          </div>

          <div class="border-t border-white/[0.06] pt-5">
            <label class="field-label mb-3 block">Forma de Pagamento <span class="text-red-400">*</span></label>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              {{-- PIX --}}
              <label class="cursor-pointer block" @click="paymentMethod = 'pix'">
                <div class="pay-option" :class="paymentMethod === 'pix' ? 'selected-pix' : ''">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                       :class="paymentMethod === 'pix' ? 'bg-green-500/15' : 'bg-white/5'">
                    <svg class="w-6 h-6 text-[#32BCAD]" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-white font-black text-sm">PIX</p>
                    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-widest">Aprovação imediata</p>
                  </div>
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                       :class="paymentMethod === 'pix' ? 'border-green-400 bg-green-400' : 'border-white/20'">
                    <svg x-show="paymentMethod === 'pix'" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  </div>
                </div>
              </label>

              {{-- Cartão --}}
              <label class="cursor-pointer block" @click="paymentMethod = 'card'">
                <div class="pay-option" :class="paymentMethod === 'card' ? 'selected-card' : ''">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                       :class="paymentMethod === 'card' ? 'bg-blue-500/15' : 'bg-white/5'">
                    <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-white font-black text-sm">Cartão de Crédito</p>
                    <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-widest">Via Stripe Seguro</p>
                  </div>
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                       :class="paymentMethod === 'card' ? 'border-blue-400 bg-blue-400' : 'border-white/20'">
                    <svg x-show="paymentMethod === 'card'" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  </div>
                </div>
              </label>
            </div>
          </div>
        </div>

        {{-- Botão de pagamento --}}
        <div class="mt-7">
          <button type="button"
                  @click="paymentMethod === 'pix' ? (pixModalOpen = true) : initStripeCheckout()"
                  :class="paymentMethod === 'pix' ? 'btn-pix' : 'btn-card'"
                  class="btn-pay">
            <span x-show="paymentMethod === 'pix'" class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/></svg>
              Pagar R$ {{ number_format($package->price, 2, ',', '.') }} no PIX
            </span>
            <span x-show="paymentMethod === 'card'" class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
              Pagar R$ {{ number_format($package->price, 2, ',', '.') }} no Cartão
            </span>
          </button>

          <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-1.5 mt-4 text-slate-600 text-[10px] font-semibold uppercase tracking-wider">
            <span class="flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              SSL Criptografado
            </span>
            <span class="flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              Pagamento Seguro
            </span>
            <span class="flex items-center gap-1.5">
              <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
              Ativação Imediata
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- ===== SIDEBAR (DIREITA) ===== --}}
    <div class="lg:col-span-5 order-1 lg:order-2">
      <div class="summary-card sidebar-sticky">

        {{-- Header com gradiente --}}
        <div class="bg-gradient-to-br {{ $theme['grad'] }} p-7 relative overflow-hidden">
          <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
          <div class="absolute -left-6 bottom-0 w-24 h-24 rounded-full bg-black/15 blur-xl pointer-events-none"></div>
          <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
              <span class="text-4xl">{{ $medalIcon }}</span>
              <div>
                <p class="text-white/60 text-[10px] font-black uppercase tracking-widest">Pacote VIP</p>
                <h3 class="text-white font-black text-2xl leading-tight">{{ $package->name }}</h3>
              </div>
            </div>
            @if ($package->savings_percent > 0)
            <div class="flex items-center gap-2 mb-1">
              <p class="text-white/40 text-sm font-bold line-through">{{ $package->formatted_original_price }}</p>
              <span class="bg-white/15 text-white text-[10px] font-black px-2 py-0.5 rounded-full border border-white/20">{{ $package->discount_label }}</span>
            </div>
            @endif
            <p class="text-white font-black text-4xl tracking-tight">{{ $package->formatted_price }}</p>
            <p class="text-white/50 text-xs font-semibold mt-1">pagamento único · sem mensalidade</p>
          </div>
        </div>

        {{-- Detalhes --}}
        <div class="p-6">
          <p class="text-slate-600 text-[10px] font-black uppercase tracking-widest mb-4">Incluído no pacote</p>
          <ul class="space-y-3 mb-6">
            @foreach([
              ['⚡', $package->boosts_count . ' impulso' . ($package->boosts_count > 1 ? 's' : '') . ' VIP · 12h de destaque cada'],
              ['📧', 'Código enviado por e-mail imediatamente'],
              ['♾️', 'Sem prazo de validade — use quando quiser'],
              ['🏅', 'Badge VIP e borda dourada exclusivos'],
              ['👥', 'Válido em qualquer grupo cadastrado na sua conta'],
            ] as [$icon, $text])
            <li class="flex items-center gap-3 text-slate-400 text-sm font-medium">
              <span class="text-base shrink-0">{{ $icon }}</span>
              <span>{{ $text }}</span>
            </li>
            @endforeach
          </ul>

          <div class="border-t border-white/[0.06] my-5"></div>

          {{-- Trust badges --}}
          <div class="flex items-start gap-3 p-4 rounded-xl"
               style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);">
            <svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <p class="text-slate-500 text-xs font-medium leading-relaxed">
              Pagamento 100% seguro via <span class="text-white font-bold">Stripe</span> ou <span class="text-white font-bold">Asaas</span>. Seus dados nunca são armazenados.
            </p>
          </div>

          {{-- Urgência social proof --}}
          @if ($boostedThisMonth ?? 0 > 0)
          <div class="mt-3 flex items-center gap-2 p-3 rounded-xl" style="background: rgba(34,197,94,0.07); border: 1px solid rgba(34,197,94,0.15);">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-ping shrink-0"></span>
            <p class="text-green-400 text-xs font-bold">{{ number_format($boostedThisMonth) }} grupos impulsionados neste mês</p>
          </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>

{{-- ===== MODAL: CARTÃO (STRIPE) ===== --}}
<div x-show="stripeModalOpen"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-bg"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;">
  <div class="relative w-full max-w-2xl modal-box p-6 sm:p-8 max-h-[90vh] overflow-y-auto"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 scale-95 translate-y-4"
       x-transition:enter-end="opacity-100 scale-100 translate-y-0">
    <button @click="stripeModalOpen = false" class="absolute top-4 right-4 text-slate-600 hover:text-white transition-colors z-10">
      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <div class="flex items-center gap-3 pb-5 mb-5" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
      <div class="w-10 h-10 rounded-xl bg-blue-500/15 flex items-center justify-center">
        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      </div>
      <div>
        <h2 class="text-lg font-black text-white">Pagamento com Cartão</h2>
        <p class="text-slate-500 text-xs">Pacote {{ $package->name }} · <span class="text-blue-400 font-bold">R$ {{ number_format($package->price, 2, ',', '.') }}</span></p>
      </div>
    </div>

    <div x-show="stripeLoading" class="py-12 flex flex-col items-center gap-4">
      <svg class="animate-spin h-10 w-10 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-slate-400 text-sm font-bold">Inicializando checkout seguro da Stripe...</p>
    </div>

    <div x-show="stripeError" class="rounded-xl p-4 mb-5 text-red-400 text-sm font-semibold text-center" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);">
      <p x-text="stripeError"></p>
    </div>

    <div x-show="stripeSimulated && !stripeLoading" class="py-4 space-y-5 text-center">
      <div class="rounded-2xl p-5 text-amber-300 text-sm font-semibold" style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2);">
        <h3 class="font-black text-base mb-2">💡 Modo Simulação (Ambiente Local)</h3>
        <p class="text-slate-400 leading-relaxed">Stripe em sandbox. Simule o pagamento aprovado clicando abaixo.</p>
      </div>
      <a :href="stripeRedirectUrl" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-900 font-black px-8 py-3 rounded-xl shadow-lg">
        Simular Pagamento Aprovado
      </a>
    </div>

    <div x-show="!stripeSimulated && !stripeLoading && !stripeError" class="w-full min-h-[300px]">
      <div id="stripe-checkout-container"></div>
    </div>

    <div class="mt-5 pt-4 flex justify-between items-center text-[10px] text-slate-700" style="border-top: 1px solid rgba(255,255,255,0.06);">
      <span class="flex items-center gap-1">🔒 Conexão SSL Criptografada</span>
      <span class="font-black uppercase tracking-widest px-2 py-0.5 rounded text-[9px]" style="background: rgba(255,255,255,0.06);">Stripe</span>
    </div>
  </div>
</div>

{{-- ===== MODAL: PIX (ASAAS) ===== --}}
<div x-show="pixModalOpen"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-bg"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;">
  <div class="relative w-full max-w-md modal-box p-6 sm:p-8 max-h-[95vh] overflow-y-auto"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 scale-95 translate-y-4"
       x-transition:enter-end="opacity-100 scale-100 translate-y-0">
    <button @click="pixModalOpen = false; if (pixInterval) clearInterval(pixInterval);" class="absolute top-4 right-4 text-slate-600 hover:text-white transition-colors">
      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <div class="flex items-center gap-3 pb-5 mb-5" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(34,188,173,0.12);">
        <svg class="w-6 h-6 text-[#32BCAD]" fill="currentColor" viewBox="0 0 24 24">
          <path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/>
        </svg>
      </div>
      <div>
        <h2 class="text-lg font-black text-white">Pagamento com PIX</h2>
        <p class="text-slate-500 text-xs">Pacote {{ $package->name }} · <span class="text-green-400 font-bold">R$ {{ number_format($package->price, 2, ',', '.') }}</span></p>
      </div>
    </div>

    {{-- Fase 1: form --}}
    <div x-show="!pixGenerated && !pixLoading" class="space-y-4">
      <div>
        <label class="field-label" for="pix_buyer_name">Nome Completo</label>
        <input type="text" id="pix_buyer_name" x-model="buyerName" placeholder="Seu nome completo"
               class="dark-input" style="padding-left: 16px; border-color: rgba(255,255,255,0.1);">
      </div>
      <div>
        <label class="field-label" for="pix_buyer_email">E-mail</label>
        <input type="email" id="pix_buyer_email" x-model="buyerEmail" placeholder="seu@email.com"
               class="dark-input" style="padding-left: 16px; border-color: rgba(255,255,255,0.1);">
        <p class="text-slate-600 text-[11px] font-medium mt-1.5 flex items-center gap-1">
          <svg class="w-3 h-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
          O código VIP será enviado para este e-mail após o pagamento
        </p>
      </div>
      <button type="button" @click="generateAsaasPix()"
              class="w-full btn-pay btn-pix mt-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/></svg>
        Gerar QR Code PIX
      </button>
    </div>

    {{-- Fase 2: loading --}}
    <div x-show="pixLoading" class="py-12 flex flex-col items-center gap-4">
      <svg class="animate-spin h-10 w-10 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <div class="text-center">
        <p class="text-white text-sm font-bold">Gerando cobrança no Asaas...</p>
        <p class="text-slate-500 text-xs mt-1">Leva apenas alguns segundos</p>
      </div>
    </div>

    {{-- Fase 3: QR Code --}}
    <div x-show="pixGenerated && !pixLoading" class="space-y-5 text-center">
      <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-bold" style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80;">
        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
        Aguardando pagamento PIX
      </div>

      <div class="flex justify-center">
        <div class="p-3 rounded-2xl inline-block" style="background: #fff; border: 2px solid rgba(255,255,255,0.1);">
          <img :src="pixQrCode" alt="QR Code PIX" class="w-48 h-48">
        </div>
      </div>

      <div class="text-left rounded-xl p-4" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07);">
        <p class="text-slate-600 text-[10px] font-black uppercase tracking-widest mb-2">Pix Copia e Cola:</p>
        <p x-text="pixCopyPaste" class="text-slate-300 text-xs font-mono break-all leading-relaxed select-all"></p>
      </div>

      <button type="button" @click="copyPixCode()"
              class="w-full btn-pay"
              :class="copied ? 'btn-card' : 'btn-pix'">
        <span x-show="!copied" class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          Copiar Código PIX
        </span>
        <span x-show="copied" class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Copiado!
        </span>
      </button>

      @if(app()->environment('local'))
      <div class="pt-4" style="border-top: 1px solid rgba(255,255,255,0.07);">
        <a :href="'/pagamento/sucesso/' + pixOrderId + '?simulated=asaas-pix'"
           class="w-full inline-flex py-3 rounded-xl font-bold text-xs items-center justify-center gap-2 text-amber-400"
           style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
          🧪 Simular Pagamento Aprovado (Dev)
        </a>
      </div>
      @endif
    </div>

    <div class="mt-5 pt-4 flex justify-between items-center text-[10px] text-slate-700" style="border-top: 1px solid rgba(255,255,255,0.06);">
      <span class="flex items-center gap-1">🔒 Transação Segura via Asaas</span>
      <span class="font-black uppercase tracking-widest px-2 py-0.5 rounded text-[9px]" style="background: rgba(255,255,255,0.06);">Asaas</span>
    </div>
  </div>
</div>

</div>
@endsection
