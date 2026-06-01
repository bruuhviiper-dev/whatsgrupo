@php
    $name = strtolower($package->name);
    $headerGradient = match(true) {
        str_contains($name, 'bronze')   => 'from-orange-800 via-orange-700 to-amber-600',
        str_contains($name, 'prata')    => 'from-slate-600 via-slate-500 to-slate-400',
        str_contains($name, 'ouro')     => 'from-yellow-600 via-amber-500 to-yellow-400',
        str_contains($name, 'diamante') => 'from-cyan-700 via-cyan-500 to-teal-400',
        str_contains($name, 'estrela')  => 'from-purple-800 via-purple-600 to-pink-500',
        default                         => 'from-slate-700 via-slate-600 to-slate-500',
    };
    $accentColor = match(true) {
        str_contains($name, 'bronze')   => 'text-orange-600',
        str_contains($name, 'prata')    => 'text-slate-500',
        str_contains($name, 'ouro')     => 'text-amber-500',
        str_contains($name, 'diamante') => 'text-cyan-500',
        str_contains($name, 'estrela')  => 'text-purple-600',
        default                         => 'text-green-600',
    };
@endphp

@extends('layouts.app')

@section('title', 'Checkout — Pacote ' . $package->name . ' | WhatsGrupos')
@section('description', 'Finalize sua compra do pacote ' . $package->name . ' com ' . $package->boosts_count . ' impulsos VIP.')

@push('head')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')

<div class="max-w-5xl mx-auto py-6 px-4" x-data="{
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
    buyerCpf: '',

    initStripeCheckout() {
        if (!this.buyerName || !this.buyerName.trim()) { alert('Preencha o seu Nome Completo.'); return; }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.buyerEmail || !emailRegex.test(this.buyerEmail)) { alert('Preencha um e-mail válido.'); return; }
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
                    if (!data.publishable_key) { this.stripeError = 'Chave pública Stripe não configurada no .env.'; return; }
                    this.stripeClientSecret = data.client_secret;
                    this.stripePublishableKey = data.publishable_key;
                    setTimeout(() => {
                        try {
                            const stripe = Stripe(data.publishable_key);
                            stripe.initEmbeddedCheckout({ clientSecret: data.client_secret }).then(checkout => {
                                checkout.mount('#stripe-checkout-container');
                            }).catch(err => { this.stripeError = 'Erro ao montar checkout Stripe: ' + err.message; });
                        } catch(err) { this.stripeError = 'Erro SDK Stripe: ' + err.message; }
                    }, 200);
                }
            } else { this.stripeError = data.message || 'Erro ao inicializar pagamento.'; }
        }).catch(() => { this.stripeLoading = false; this.stripeError = 'Erro de conexão com Stripe.'; });
    },

    generateMercadoPagoPix() {
        if (!this.buyerName || !this.buyerName.trim()) { alert('Preencha o seu Nome Completo.'); return; }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.buyerEmail || !emailRegex.test(this.buyerEmail)) { alert('Preencha um e-mail válido.'); return; }
        this.pixLoading = true; this.pixGenerated = false;
        fetch('{{ route('boost.checkout-mp-pix', $package->slug) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ buyer_name: this.buyerName, buyer_email: this.buyerEmail, buyer_cpf: this.buyerCpf })
        }).then(r => r.json()).then(data => {
            this.pixLoading = false;
            if (data.success) {
                this.pixGenerated = true; this.pixQrCode = data.qr_code;
                this.pixCopyPaste = data.copy_paste; this.pixOrderId = data.order_id;
                this.startPolling(data.order_id);
            } else { alert('Erro ao gerar cobrança PIX. Tente novamente.'); }
        }).catch(() => { this.pixLoading = false; alert('Erro de conexão ao gerar PIX.'); });
    },

    startPolling(orderId) {
        if (this.pixInterval) clearInterval(this.pixInterval);
        this.pixInterval = setInterval(() => {
            fetch('/pagamento/mp-pix-status/' + orderId).then(r => r.json()).then(data => {
                if (data.status === 'paid' && data.redirect_url) { clearInterval(this.pixInterval); window.location.href = data.redirect_url; }
            }).catch(() => {});
        }, 5000);
    },

    copyPixCode() {
        navigator.clipboard.writeText(this.pixCopyPaste).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2500); });
    }
}">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 font-semibold mb-8">
        <a href="{{ route('boost.packages') }}" class="hover:text-green-600 transition-colors">Pacotes VIP</a>
        <x-heroicon-m-chevron-right class="w-4 h-4 text-slate-300" />
        <span class="text-slate-900">Checkout</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ============================================================ --}}
        {{-- COLUNA ESQUERDA — FORMULÁRIO DE CHECKOUT --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-7 order-2 lg:order-1 space-y-5">

            {{-- Cabeçalho --}}
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <x-heroicon-s-lock-closed class="w-5 h-5 text-slate-400" />
                    Finalizar Compra
                </h1>
                <p class="text-slate-500 text-sm mt-1">Ambiente 100% seguro e criptografado.</p>
            </div>

            {{-- Card: Dados do comprador --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-5">Seus dados</h2>
                <div class="space-y-4">
                    <div>
                        <label for="buyer_name" class="block text-slate-700 font-semibold text-sm mb-1.5">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <x-heroicon-o-user class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input type="text" id="buyer_name" x-model="buyerName" required
                                   placeholder="Seu nome completo"
                                   class="w-full pl-9 pr-4 py-3 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-900 outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all placeholder:text-slate-400">
                        </div>
                    </div>
                    <div>
                        <label for="buyer_email" class="block text-slate-700 font-semibold text-sm mb-1.5">
                            E-mail <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <x-heroicon-o-envelope class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input type="email" id="buyer_email" x-model="buyerEmail" required
                                   placeholder="seu@email.com"
                                   class="w-full pl-9 pr-4 py-3 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-900 outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all placeholder:text-slate-400">
                        </div>
                        <p class="text-slate-400 text-xs mt-1.5 flex items-center gap-1">
                            <x-heroicon-s-information-circle class="w-3.5 h-3.5 text-blue-400 shrink-0" />
                            O código VIP será enviado para este e-mail após o pagamento.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Card: Forma de pagamento --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest">Forma de Pagamento</h2>

                {{-- Métodos ativos — cards horizontais --}}
                <div class="space-y-2.5">

                    {{-- PIX --}}
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method" value="pix" x-model="paymentMethod" class="sr-only">
                        <div class="flex items-center gap-4 px-4 py-3.5 rounded-xl border-2 transition-all"
                             :class="paymentMethod === 'pix'
                                ? 'border-[#32BCAD] bg-[#32BCAD]/5'
                                : 'border-slate-200 bg-white hover:border-slate-300'">
                            {{-- PIX icon oficial (Banco Central) --}}
                            <svg class="w-7 h-7 flex-shrink-0" viewBox="0 0 512 512" fill="#32BCAD" aria-label="PIX">
                                <path d="M112.57 391.19c20.056 0 38.928-7.808 53.12-21.984l76.693-76.692c5.385-5.404 14.765-5.384 20.15 0l76.989 76.989c14.191 14.177 33.045 21.985 53.12 21.985h15.098l-97.21 97.21c-30.328 30.328-79.476 30.328-109.806 0l-97.515-97.508h9.371z"/>
                                <path d="M398.766 120.792c-20.056 0-38.929 7.809-53.12 21.985l-76.99 76.988c-5.207 5.225-14.952 5.247-20.15-.001l-76.692-76.692c-14.192-14.177-33.063-21.985-53.12-21.985h-9.372l97.516-97.515c30.328-30.327 79.478-30.327 109.806 0l97.21 97.22h-15.098z"/>
                                <path d="M22.758 200.852l58.733-58.733c1.298.49 2.704.795 4.183.795h26.913c13.847 0 27.41 5.608 37.18 15.395l76.69 76.689c6.752 6.753 15.612 10.126 24.477 10.126 8.86 0 17.726-3.373 24.471-10.118l76.99-76.991c9.768-9.787 23.333-15.395 37.179-15.395h32.624c1.479 0 2.885-.306 4.183-.795l58.987 58.987c30.328 30.328 30.328 79.476 0 109.806l-58.98 58.979c-1.305-.482-2.717-.787-4.19-.787h-32.624c-13.846 0-27.411-5.609-37.18-15.396l-76.982-76.982c-13.077-13.084-35.866-13.092-48.96.008l-76.689 76.682c-9.768 9.787-23.333 15.395-37.18 15.395H85.674c-1.479 0-2.885.306-4.183.795l-58.733-58.74c-30.327-30.328-30.327-79.478 0-109.804z"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold leading-none mb-0.5"
                                   :class="paymentMethod === 'pix' ? 'text-[#32BCAD]' : 'text-slate-800'">PIX</p>
                                <p class="text-[11px] text-slate-400">Aprovação imediata · via Mercado Pago</p>
                            </div>
                            {{-- Radio visual --}}
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                 :class="paymentMethod === 'pix' ? 'border-[#32BCAD]' : 'border-slate-300'">
                                <div class="w-2 h-2 rounded-full bg-[#32BCAD] transition-all"
                                     :class="paymentMethod === 'pix' ? 'opacity-100' : 'opacity-0'"></div>
                            </div>
                        </div>
                    </label>

                    {{-- Cartão --}}
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="sr-only">
                        <div class="flex items-center gap-4 px-4 py-3.5 rounded-xl border-2 transition-all"
                             :class="paymentMethod === 'card'
                                ? 'border-blue-500 bg-blue-50'
                                : 'border-slate-200 bg-white hover:border-slate-300'">
                            {{-- Card icon --}}
                            <svg viewBox="0 0 40 26" fill="none" class="w-9 h-6 flex-shrink-0">
                                <rect width="40" height="26" rx="4" fill="#3B82F6"/>
                                <rect x="0" y="7" width="40" height="5" fill="#2563EB"/>
                                <rect x="4" y="15" width="8" height="5" rx="1.5" fill="#93C5FD"/>
                                <circle cx="33" cy="17.5" r="4" fill="#EF4444" opacity="0.85"/>
                                <circle cx="29" cy="17.5" r="4" fill="#F97316" opacity="0.85"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold leading-none mb-0.5"
                                   :class="paymentMethod === 'card' ? 'text-blue-600' : 'text-slate-800'">Cartão de Crédito / Débito</p>
                                <p class="text-[11px] text-slate-400">Visa, Mastercard, Elo, Amex · via Stripe</p>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                 :class="paymentMethod === 'card' ? 'border-blue-500' : 'border-slate-300'">
                                <div class="w-2 h-2 rounded-full bg-blue-500 transition-all"
                                     :class="paymentMethod === 'card' ? 'opacity-100' : 'opacity-0'"></div>
                            </div>
                        </div>
                    </label>
                </div>

                {{-- Info do método selecionado --}}
                <div x-show="paymentMethod === 'pix'" x-transition
                     class="flex items-start gap-2.5 rounded-xl p-3 border border-[#32BCAD]/20 bg-[#32BCAD]/5">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-[#32BCAD]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <p class="text-[11px] font-medium leading-relaxed text-[#0f5e58]">
                        Gere o QR Code, pague em qualquer banco e o código VIP chega no e-mail em segundos.
                    </p>
                </div>
                <div x-show="paymentMethod === 'card'" x-transition
                     class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl p-3">
                    <x-heroicon-s-shield-check class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" />
                    <p class="text-[11px] font-medium leading-relaxed text-blue-700">
                        Ambiente seguro Stripe. Seus dados de cartão nunca passam pelo nosso servidor.
                    </p>
                </div>

                {{-- Em breve --}}
                <div class="pt-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-300 mb-2">Em breve</p>
                    <div class="flex gap-2">
                        {{-- Nubank --}}
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-dashed border-slate-200 opacity-50 cursor-not-allowed">
                            <svg viewBox="0 0 20 20" class="w-4 h-4 flex-shrink-0">
                                <rect width="20" height="20" rx="5" fill="#820AD1"/>
                                <path d="M5 14V6h1.6l5.4 5.6V6H14v8h-1.6L7 8.4V14H5z" fill="white"/>
                            </svg>
                            <span class="text-[10px] font-semibold text-slate-500">Nubank</span>
                        </div>
                        {{-- PagSeguro --}}
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-dashed border-slate-200 opacity-50 cursor-not-allowed">
                            <svg viewBox="0 0 20 20" fill="none" class="w-4 h-4 flex-shrink-0">
                                <rect width="20" height="20" rx="5" fill="#F58220"/>
                                <path d="M7 10.5V9a3 3 0 016 0v1.5" stroke="white" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                                <rect x="5.5" y="10.5" width="9" height="6" rx="1.5" fill="white"/>
                                <circle cx="10" cy="13.5" r="1" fill="#F58220"/>
                            </svg>
                            <span class="text-[10px] font-semibold text-slate-500">PagSeguro</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botão de pagamento --}}
            <button type="button"
                    @click="paymentMethod === 'pix' ? (pixModalOpen = true) : initStripeCheckout()"
                    class="w-full py-4 rounded-xl font-black text-base transition-all flex items-center justify-center gap-2.5 text-white"
                    :class="paymentMethod === 'pix'
                        ? 'bg-[#32BCAD] hover:bg-[#28a89a] shadow-lg shadow-[#32BCAD]/25'
                        : 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20'">
                <template x-if="paymentMethod === 'pix'">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-5 h-5" viewBox="0 0 512 512" fill="white">
                            <path d="M112.57 391.19c20.056 0 38.928-7.808 53.12-21.984l76.693-76.692c5.385-5.404 14.765-5.384 20.15 0l76.989 76.989c14.191 14.177 33.045 21.985 53.12 21.985h15.098l-97.21 97.21c-30.328 30.328-79.476 30.328-109.806 0l-97.515-97.508h9.371z"/>
                            <path d="M398.766 120.792c-20.056 0-38.929 7.809-53.12 21.985l-76.99 76.988c-5.207 5.225-14.952 5.247-20.15-.001l-76.692-76.692c-14.192-14.177-33.063-21.985-53.12-21.985h-9.372l97.516-97.515c30.328-30.327 79.478-30.327 109.806 0l97.21 97.22h-15.098z"/>
                            <path d="M22.758 200.852l58.733-58.733c1.298.49 2.704.795 4.183.795h26.913c13.847 0 27.41 5.608 37.18 15.395l76.69 76.689c6.752 6.753 15.612 10.126 24.477 10.126 8.86 0 17.726-3.373 24.471-10.118l76.99-76.991c9.768-9.787 23.333-15.395 37.179-15.395h32.624c1.479 0 2.885-.306 4.183-.795l58.987 58.987c30.328 30.328 30.328 79.476 0 109.806l-58.98 58.979c-1.305-.482-2.717-.787-4.19-.787h-32.624c-13.846 0-27.411-5.609-37.18-15.396l-76.982-76.982c-13.077-13.084-35.866-13.092-48.96.008l-76.689 76.682c-9.768 9.787-23.333 15.395-37.18 15.395H85.674c-1.479 0-2.885.306-4.183.795l-58.733-58.74c-30.327-30.328-30.327-79.478 0-109.804z"/>
                        </svg>
                        Pagar R$ {{ number_format($package->price, 2, ',', '.') }} com PIX
                    </span>
                </template>
                <template x-if="paymentMethod === 'card'">
                    <span class="flex items-center gap-2.5">
                        <x-heroicon-s-credit-card class="w-5 h-5" />
                        Pagar R$ {{ number_format($package->price, 2, ',', '.') }} com Cartão
                    </span>
                </template>
            </button>

            {{-- Selos de segurança --}}
            <div class="flex flex-wrap items-center justify-center gap-5 pt-1">
                <span class="flex items-center gap-1.5 text-slate-400 text-xs font-medium">
                    <x-heroicon-s-lock-closed class="w-3.5 h-3.5" /> SSL Criptografado
                </span>
                <span class="flex items-center gap-1.5 text-slate-400 text-xs font-medium">
                    <x-heroicon-s-shield-check class="w-3.5 h-3.5" /> Pagamento Seguro
                </span>
                <span class="flex items-center gap-1.5 text-slate-400 text-xs font-medium">
                    <x-heroicon-s-envelope class="w-3.5 h-3.5" /> Código por E-mail
                </span>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- COLUNA DIREITA — RESUMO DO PEDIDO --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-5 order-1 lg:order-2">
            <div class="sticky top-24 space-y-4">

                {{-- Card do pacote --}}
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-gradient-to-br {{ $headerGradient }} p-5 relative overflow-hidden">
                        <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest mb-0.5">Pacote VIP</p>
                                <h3 class="text-white font-black text-xl">{{ $package->name }}</h3>
                            </div>
                            <div class="text-right">
                                @if ($package->savings_percent > 0)
                                    <p class="text-white/50 text-xs line-through font-semibold">{{ $package->formatted_original_price }}</p>
                                    <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full border border-white/30">{{ $package->discount_label }}</span>
                                @endif
                                <p class="text-white font-black text-3xl mt-1">{{ $package->formatted_price }}</p>
                                <p class="text-white/50 text-[10px] font-semibold">pagamento único</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-3">O que está incluído</p>
                        <ul class="space-y-2.5">
                            <li class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                                <span class="w-5 h-5 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-s-bolt class="w-3 h-3 text-orange-500" />
                                </span>
                                <span><strong class="text-slate-900">{{ $package->boosts_count }} impulso{{ $package->boosts_count > 1 ? 's' : '' }} VIP</strong> · 12h cada</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                                <span class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-s-envelope class="w-3 h-3 text-blue-500" />
                                </span>
                                Código enviado por e-mail imediatamente
                            </li>
                            <li class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                                <span class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-s-shield-check class="w-3 h-3 text-green-500" />
                                </span>
                                Sem prazo de validade
                            </li>
                            <li class="flex items-center gap-3 text-sm text-slate-700 font-medium">
                                <span class="w-5 h-5 rounded-full bg-purple-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-s-user-group class="w-3 h-3 text-purple-500" />
                                </span>
                                Válido para qualquer grupo da sua conta
                            </li>
                        </ul>

                        <div class="border-t border-slate-100 mt-4 pt-4 flex items-center justify-between text-sm">
                            <span class="text-slate-500 font-semibold">Total a pagar</span>
                            <span class="font-black text-slate-900 text-lg">{{ $package->formatted_price }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card de segurança --}}
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-start gap-3">
                    <x-heroicon-s-lock-closed class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" />
                    <div>
                        <p class="text-slate-700 text-xs font-bold mb-0.5">Compra 100% segura</p>
                        <p class="text-slate-400 text-xs font-medium leading-relaxed">Seus dados são protegidos por criptografia SSL. Processamento via <strong class="text-slate-600">Stripe</strong> e <strong class="text-slate-600">Mercado Pago</strong>.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- MODAL: CARTÃO (STRIPE) --}}
    {{-- ================================================================ --}}
    <div x-show="stripeModalOpen"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="relative w-full max-w-xl bg-white border border-slate-200 rounded-2xl shadow-2xl p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
            <button @click="stripeModalOpen = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                    <x-heroicon-s-credit-card class="w-5 h-5 text-blue-500" />
                </div>
                <div>
                    <h2 class="font-black text-slate-900">Pagamento com Cartão</h2>
                    <p class="text-xs text-slate-500">{{ $package->name }} · <span class="font-bold text-blue-600">{{ $package->formatted_price }}</span></p>
                </div>
            </div>
            <div x-show="stripeLoading" class="py-10 flex flex-col items-center gap-3">
                <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-bold text-slate-600">Inicializando Stripe...</p>
            </div>
            <div x-show="stripeError" class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm font-semibold mb-4">
                <p x-text="stripeError"></p>
            </div>
            <div x-show="stripeSimulated && !stripeLoading" class="text-center space-y-4 py-4">
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-amber-800 text-sm font-semibold">
                    <p class="font-black mb-1">Modo Simulação (Dev)</p>
                    <p>Stripe em modo sandbox. Simule o pagamento abaixo.</p>
                </div>
                <a :href="stripeRedirectUrl" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-black px-6 py-3 rounded-xl transition-all">
                    <x-heroicon-s-beaker class="w-4 h-4" /> Simular Pagamento Aprovado
                </a>
            </div>
            <div x-show="!stripeSimulated && !stripeLoading && !stripeError" class="w-full min-h-[300px]">
                <div id="stripe-checkout-container"></div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center gap-1"><x-heroicon-s-lock-closed class="w-3 h-3" /> SSL</span>
                <span class="bg-slate-100 text-slate-500 font-black px-2 py-0.5 rounded text-[9px] uppercase tracking-widest">Stripe</span>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- MODAL: PIX (ASAAS) --}}
    {{-- ================================================================ --}}
    <div x-show="pixModalOpen"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="relative w-full max-w-sm bg-white border border-slate-200 rounded-2xl shadow-2xl p-6 overflow-y-auto max-h-[95vh]">
            <button @click="pixModalOpen = false; if (pixInterval) clearInterval(pixInterval);" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>

            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#32BCAD1a;">
                    <svg class="w-5 h-5" viewBox="0 0 512 512" fill="#32BCAD">
                        <path d="M112.57 391.19c20.056 0 38.928-7.808 53.12-21.984l76.693-76.692c5.385-5.404 14.765-5.384 20.15 0l76.989 76.989c14.191 14.177 33.045 21.985 53.12 21.985h15.098l-97.21 97.21c-30.328 30.328-79.476 30.328-109.806 0l-97.515-97.508h9.371z"/>
                        <path d="M398.766 120.792c-20.056 0-38.929 7.809-53.12 21.985l-76.99 76.988c-5.207 5.225-14.952 5.247-20.15-.001l-76.692-76.692c-14.192-14.177-33.063-21.985-53.12-21.985h-9.372l97.516-97.515c30.328-30.327 79.478-30.327 109.806 0l97.21 97.22h-15.098z"/>
                        <path d="M22.758 200.852l58.733-58.733c1.298.49 2.704.795 4.183.795h26.913c13.847 0 27.41 5.608 37.18 15.395l76.69 76.689c6.752 6.753 15.612 10.126 24.477 10.126 8.86 0 17.726-3.373 24.471-10.118l76.99-76.991c9.768-9.787 23.333-15.395 37.179-15.395h32.624c1.479 0 2.885-.306 4.183-.795l58.987 58.987c30.328 30.328 30.328 79.476 0 109.806l-58.98 58.979c-1.305-.482-2.717-.787-4.19-.787h-32.624c-13.846 0-27.411-5.609-37.18-15.396l-76.982-76.982c-13.077-13.084-35.866-13.092-48.96.008l-76.689 76.682c-9.768 9.787-23.333 15.395-37.18 15.395H85.674c-1.479 0-2.885.306-4.183.795l-58.733-58.74c-30.327-30.328-30.327-79.478 0-109.804z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900">Pagamento via PIX</h2>
                    <p class="text-xs text-slate-500">{{ $package->name }} · <span class="font-bold" style="color:#32BCAD;">{{ $package->formatted_price }}</span> · Mercado Pago</p>
                </div>
            </div>

            {{-- Fase 1: Form --}}
            <div x-show="!pixGenerated && !pixLoading" class="space-y-4">
                <div>
                    <label class="block text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5">Nome Completo</label>
                    <input type="text" x-model="buyerName" placeholder="Seu nome completo"
                           class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-[#32BCAD] focus:ring-2 focus:ring-[#32BCAD]/10 transition-all">
                </div>
                <div>
                    <label class="block text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5">E-mail</label>
                    <input type="email" x-model="buyerEmail" placeholder="seu@email.com"
                           class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-[#32BCAD] focus:ring-2 focus:ring-[#32BCAD]/10 transition-all">
                </div>
                <div>
                    <label class="block text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5">
                        CPF <span class="text-slate-400 font-normal normal-case">(recomendado)</span>
                    </label>
                    <input type="text" x-model="buyerCpf" placeholder="000.000.000-00" maxlength="14"
                           @input="buyerCpf = buyerCpf.replace(/\D/g,'').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2')"
                           class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-[#32BCAD] focus:ring-2 focus:ring-[#32BCAD]/10 transition-all font-mono">
                    <p class="text-slate-400 text-[10px] mt-1">Exigido pelo Mercado Pago para processar o PIX.</p>
                </div>
                <button type="button" @click="generateMercadoPagoPix()"
                        class="w-full bg-[#32BCAD] hover:bg-[#28a89a] text-white font-black py-3.5 rounded-xl text-sm transition-all flex items-center justify-center gap-2 shadow-md shadow-[#32BCAD]/25">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="white">
                        <path d="M112.57 391.19c20.056 0 38.928-7.808 53.12-21.984l76.693-76.692c5.385-5.404 14.765-5.384 20.15 0l76.989 76.989c14.191 14.177 33.045 21.985 53.12 21.985h15.098l-97.21 97.21c-30.328 30.328-79.476 30.328-109.806 0l-97.515-97.508h9.371z"/>
                        <path d="M398.766 120.792c-20.056 0-38.929 7.809-53.12 21.985l-76.99 76.988c-5.207 5.225-14.952 5.247-20.15-.001l-76.692-76.692c-14.192-14.177-33.063-21.985-53.12-21.985h-9.372l97.516-97.515c30.328-30.327 79.478-30.327 109.806 0l97.21 97.22h-15.098z"/>
                        <path d="M22.758 200.852l58.733-58.733c1.298.49 2.704.795 4.183.795h26.913c13.847 0 27.41 5.608 37.18 15.395l76.69 76.689c6.752 6.753 15.612 10.126 24.477 10.126 8.86 0 17.726-3.373 24.471-10.118l76.99-76.991c9.768-9.787 23.333-15.395 37.179-15.395h32.624c1.479 0 2.885-.306 4.183-.795l58.987 58.987c30.328 30.328 30.328 79.476 0 109.806l-58.98 58.979c-1.305-.482-2.717-.787-4.19-.787h-32.624c-13.846 0-27.411-5.609-37.18-15.396l-76.982-76.982c-13.077-13.084-35.866-13.092-48.96.008l-76.689 76.682c-9.768 9.787-23.333 15.395-37.18 15.395H85.674c-1.479 0-2.885.306-4.183.795l-58.733-58.74c-30.327-30.328-30.327-79.478 0-109.804z"/>
                    </svg>
                    Gerar QR Code PIX
                </button>
            </div>

            {{-- Fase 2: Loading --}}
            <div x-show="pixLoading" class="py-10 flex flex-col items-center gap-3">
                <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-bold text-slate-600">Gerando PIX...</p>
            </div>

            {{-- Fase 3: QR Code --}}
            <div x-show="pixGenerated && !pixLoading" class="space-y-4 text-center">
                <div class="inline-flex items-center gap-1.5 bg-green-50 border border-green-200 rounded-full px-3 py-1 text-green-700 text-xs font-bold">
                    <x-heroicon-s-clock class="w-3.5 h-3.5" /> Aguardando pagamento...
                </div>
                <div class="flex justify-center">
                    <div class="p-3 bg-white border-2 border-slate-200 rounded-2xl shadow-sm inline-block">
                        <img :src="pixQrCode" alt="QR Code PIX" class="w-44 h-44">
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-left">
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1.5">PIX Copia e Cola</p>
                    <p x-text="pixCopyPaste" class="text-slate-700 text-xs font-mono break-all leading-relaxed select-all"></p>
                </div>
                <button type="button" @click="copyPixCode()"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-black py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
                    <span x-show="!copied" class="flex items-center gap-2"><x-heroicon-o-clipboard-document class="w-4 h-4" /> Copiar Código PIX</span>
                    <span x-show="copied" class="flex items-center gap-2"><x-heroicon-s-check-circle class="w-4 h-4" /> Copiado!</span>
                </button>
                @if(app()->environment('local'))
                    <a :href="'/pagamento/sucesso/' + pixOrderId + '?simulated=asaas-pix'"
                       class="w-full inline-flex items-center justify-center gap-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold py-3 rounded-xl transition-all">
                        <x-heroicon-s-beaker class="w-4 h-4" /> Simular Pagamento (Dev)
                    </a>
                @endif
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center gap-1"><x-heroicon-s-lock-closed class="w-3 h-3" /> Transação Segura</span>
                <span class="bg-slate-100 text-slate-500 font-black px-2 py-0.5 rounded text-[9px] uppercase tracking-widest">Mercado Pago</span>
            </div>
        </div>
    </div>

</div>
@endsection
