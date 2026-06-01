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

    generateAsaasPix() {
        if (!this.buyerName || !this.buyerName.trim()) { alert('Preencha o seu Nome Completo.'); return; }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.buyerEmail || !emailRegex.test(this.buyerEmail)) { alert('Preencha um e-mail válido.'); return; }
        this.pixLoading = true; this.pixGenerated = false;
        fetch('{{ route('boost.checkout-asaas-pix', $package->slug) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ buyer_name: this.buyerName, buyer_email: this.buyerEmail })
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
            fetch('/pagamento/pix-status/' + orderId).then(r => r.json()).then(data => {
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
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-5">Forma de Pagamento</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

                    {{-- PIX --}}
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" value="pix" x-model="paymentMethod" class="peer hidden">
                        <div class="rounded-xl border-2 p-3.5 flex flex-col items-center gap-2 transition-all
                                    border-slate-200 bg-white hover:border-slate-300
                                    peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-sm">
                            <svg class="w-7 h-7 text-[#32BCAD]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/>
                            </svg>
                            <span class="text-xs font-black text-slate-700 peer-checked:text-green-700">PIX</span>
                            <span class="text-[10px] text-slate-400 font-semibold text-center leading-tight">Aprovação<br>imediata</span>
                        </div>
                        <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                            <x-heroicon-s-check class="w-3 h-3 text-white" />
                        </div>
                    </label>

                    {{-- Cartão de Crédito --}}
                    <label class="cursor-pointer relative">
                        <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="peer hidden">
                        <div class="rounded-xl border-2 p-3.5 flex flex-col items-center gap-2 transition-all
                                    border-slate-200 bg-white hover:border-slate-300
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-sm">
                            <x-heroicon-s-credit-card class="w-7 h-7 text-blue-500" />
                            <span class="text-xs font-black text-slate-700">Cartão</span>
                            <span class="text-[10px] text-slate-400 font-semibold text-center leading-tight">Crédito/<br>Débito</span>
                        </div>
                        <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                            <x-heroicon-s-check class="w-3 h-3 text-white" />
                        </div>
                    </label>

                    {{-- Nubank / Boleto (em breve) --}}
                    <label class="cursor-not-allowed relative opacity-60">
                        <div class="rounded-xl border-2 border-dashed border-slate-200 p-3.5 flex flex-col items-center gap-2">
                            <svg class="w-7 h-7" viewBox="0 0 50 50" fill="none">
                                <circle cx="25" cy="25" r="25" fill="#820AD1"/>
                                <path d="M25 12C17.82 12 12 17.82 12 25C12 32.18 17.82 38 25 38C32.18 38 38 32.18 38 25C38 17.82 32.18 12 25 12ZM25 35C19.48 35 15 30.52 15 25C15 19.48 19.48 15 25 15C30.52 15 35 19.48 35 25C35 30.52 30.52 35 25 35Z" fill="white"/>
                            </svg>
                            <span class="text-xs font-black text-slate-500">Nubank</span>
                            <span class="text-[9px] text-slate-400 font-bold bg-slate-100 px-1.5 py-0.5 rounded-full">Em breve</span>
                        </div>
                    </label>

                    {{-- Mercado Pago (em breve) --}}
                    <label class="cursor-not-allowed relative opacity-60">
                        <div class="rounded-xl border-2 border-dashed border-slate-200 p-3.5 flex flex-col items-center gap-2">
                            <svg class="w-7 h-7" viewBox="0 0 48 48" fill="none">
                                <circle cx="24" cy="24" r="24" fill="#009EE3"/>
                                <text x="24" y="30" text-anchor="middle" font-size="14" font-weight="bold" fill="white" font-family="Arial">MP</text>
                            </svg>
                            <span class="text-xs font-black text-slate-500">Mercado Pago</span>
                            <span class="text-[9px] text-slate-400 font-bold bg-slate-100 px-1.5 py-0.5 rounded-full">Em breve</span>
                        </div>
                    </label>

                    {{-- PagSeguro (em breve) --}}
                    <label class="cursor-not-allowed relative opacity-60">
                        <div class="rounded-xl border-2 border-dashed border-slate-200 p-3.5 flex flex-col items-center gap-2">
                            <svg class="w-7 h-7" viewBox="0 0 48 48" fill="none">
                                <circle cx="24" cy="24" r="24" fill="#009C38"/>
                                <text x="24" y="30" text-anchor="middle" font-size="11" font-weight="bold" fill="white" font-family="Arial">PAG</text>
                            </svg>
                            <span class="text-xs font-black text-slate-500">PagSeguro</span>
                            <span class="text-[9px] text-slate-400 font-bold bg-slate-100 px-1.5 py-0.5 rounded-full">Em breve</span>
                        </div>
                    </label>

                    {{-- Boleto (em breve) --}}
                    <label class="cursor-not-allowed relative opacity-60">
                        <div class="rounded-xl border-2 border-dashed border-slate-200 p-3.5 flex flex-col items-center gap-2">
                            <x-heroicon-o-document-text class="w-7 h-7 text-slate-400" />
                            <span class="text-xs font-black text-slate-500">Boleto</span>
                            <span class="text-[9px] text-slate-400 font-bold bg-slate-100 px-1.5 py-0.5 rounded-full">Em breve</span>
                        </div>
                    </label>

                </div>

                {{-- Info do método selecionado --}}
                <div x-show="paymentMethod === 'pix'" class="mt-4 flex items-start gap-2.5 bg-green-50 border border-green-100 rounded-xl p-3.5">
                    <x-heroicon-s-bolt class="w-4 h-4 text-green-500 shrink-0 mt-0.5" />
                    <p class="text-green-800 text-xs font-semibold leading-relaxed">
                        <strong>PIX é a forma mais rápida.</strong> Gere o QR Code, pague no seu banco e o código VIP chega no seu e-mail em segundos.
                    </p>
                </div>
                <div x-show="paymentMethod === 'card'" class="mt-4 flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl p-3.5">
                    <x-heroicon-s-shield-check class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" />
                    <p class="text-blue-800 text-xs font-semibold leading-relaxed">
                        <strong>Pagamento por cartão via Stripe.</strong> Aceita Visa, Mastercard, Elo e American Express. Transação criptografada e segura.
                    </p>
                </div>
            </div>

            {{-- Botão de pagamento --}}
            <button type="button"
                    @click="paymentMethod === 'pix' ? (pixModalOpen = true) : initStripeCheckout()"
                    class="w-full py-4 rounded-xl font-black text-base transition-all flex items-center justify-center gap-2 text-white shadow-lg"
                    :class="paymentMethod === 'pix'
                        ? 'bg-green-500 hover:bg-green-600 shadow-green-500/30'
                        : 'bg-blue-600 hover:bg-blue-700 shadow-blue-600/30'">
                <span x-show="paymentMethod === 'pix'" class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/></svg>
                    Pagar R$ {{ number_format($package->price, 2, ',', '.') }} com PIX
                </span>
                <span x-show="paymentMethod === 'card'" class="flex items-center gap-2">
                    <x-heroicon-s-credit-card class="w-5 h-5" />
                    Pagar R$ {{ number_format($package->price, 2, ',', '.') }} com Cartão
                </span>
            </button>

            {{-- Selos de segurança --}}
            <div class="flex flex-wrap items-center justify-center gap-4 pt-1">
                <span class="flex items-center gap-1.5 text-slate-400 text-xs font-semibold">
                    <x-heroicon-s-lock-closed class="w-3.5 h-3.5" /> SSL Criptografado
                </span>
                <span class="flex items-center gap-1.5 text-slate-400 text-xs font-semibold">
                    <x-heroicon-s-shield-check class="w-3.5 h-3.5" /> Pagamento Seguro
                </span>
                <span class="flex items-center gap-1.5 text-slate-400 text-xs font-semibold">
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
                        <p class="text-slate-400 text-xs font-medium leading-relaxed">Seus dados são protegidos por criptografia SSL. Processamento via <strong class="text-slate-600">Stripe</strong> e <strong class="text-slate-600">Asaas</strong>.</p>
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
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#32BCAD]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-900">Pagamento PIX</h2>
                    <p class="text-xs text-slate-500">{{ $package->name }} · <span class="font-bold text-green-600">{{ $package->formatted_price }}</span></p>
                </div>
            </div>

            {{-- Fase 1: Form --}}
            <div x-show="!pixGenerated && !pixLoading" class="space-y-4">
                <div>
                    <label class="block text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5">Nome Completo</label>
                    <input type="text" x-model="buyerName" placeholder="Seu nome completo"
                           class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all">
                </div>
                <div>
                    <label class="block text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5">E-mail</label>
                    <input type="email" x-model="buyerEmail" placeholder="seu@email.com"
                           class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all">
                </div>
                <button type="button" @click="generateAsaasPix()"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-black py-3.5 rounded-xl text-sm transition-all flex items-center justify-center gap-2 shadow-md shadow-green-500/20">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/></svg>
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
                <span class="bg-slate-100 text-slate-500 font-black px-2 py-0.5 rounded text-[9px] uppercase tracking-widest">Asaas</span>
            </div>
        </div>
    </div>

</div>
@endsection
