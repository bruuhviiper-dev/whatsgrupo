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
    stripeCheckoutInstance: null,

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
        // Garante que não exista um Embedded Checkout anterior montado
        // (a Stripe não permite duas instâncias ao mesmo tempo).
        this.destroyStripeCheckout();
        this.stripeLoading = true; this.stripeModalOpen = true; this.stripeError = ''; this.stripeSimulated = false;
        fetch('{{ route('boost.checkout-stripe-embedded', $package->slug) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ buyer_name: this.buyerName, buyer_email: this.buyerEmail, method: this.paymentMethod })
        }).then(async (r) => {
            // Lê o corpo de forma segura, mesmo que não seja JSON (ex.: 500 em HTML).
            const raw = await r.text();
            let data = {};
            try { data = raw ? JSON.parse(raw) : {}; }
            catch (_) {
                console.error('[Stripe] Resposta não-JSON do servidor:', r.status, raw);
                throw new Error('Servidor respondeu ' + r.status + (raw ? (': ' + raw.slice(0, 300)) : '.'));
            }
            if (!r.ok || !data.success) {
                throw new Error(data.message || ('Servidor respondeu ' + r.status + '.'));
            }
            return data;
        }).then(data => {
            this.stripeLoading = false;
            if (data.is_simulated) { this.stripeSimulated = true; this.stripeRedirectUrl = data.redirect_url; }
            else {
                if (!data.publishable_key) { this.stripeError = 'Chave pública Stripe não configurada no .env.'; return; }
                this.stripeClientSecret = data.client_secret;
                this.stripePublishableKey = data.publishable_key;
                setTimeout(() => {
                    // Se o modal já foi fechado enquanto carregava, não monta.
                    if (!this.stripeModalOpen) return;
                    // Destrói qualquer instância remanescente antes de criar a nova.
                    this.destroyStripeCheckout();
                    try {
                        const stripe = Stripe(data.publishable_key);
                        stripe.initEmbeddedCheckout({ clientSecret: data.client_secret }).then(checkout => {
                            // Mais uma checagem: o usuário pode ter fechado durante o await.
                            if (!this.stripeModalOpen) { try { checkout.destroy(); } catch (_) {} return; }
                            this.stripeCheckoutInstance = checkout;
                            checkout.mount('#stripe-checkout-container');
                        }).catch(err => { this.stripeError = 'Erro ao montar checkout Stripe: ' + err.message; });
                    } catch(err) { this.stripeError = 'Erro SDK Stripe: ' + err.message; }
                }, 200);
            }
        }).catch((err) => {
            this.stripeLoading = false;
            console.error('[Stripe] Falha ao iniciar checkout:', err);
            this.stripeError = err && err.message ? err.message : 'Erro de conexão com Stripe.';
        });
    },

    // Destrói a instância do Embedded Checkout, se existir, e limpa o container.
    destroyStripeCheckout() {
        if (this.stripeCheckoutInstance) {
            try { this.stripeCheckoutInstance.destroy(); } catch (_) {}
            this.stripeCheckoutInstance = null;
        }
        const el = document.getElementById('stripe-checkout-container');
        if (el) el.innerHTML = '';
    },

    // Fecha o modal Stripe e libera a instância para permitir trocar de método.
    closeStripeModal() {
        this.destroyStripeCheckout();
        this.stripeModalOpen = false;
        this.stripeLoading = false;
        this.stripeError = '';
        this.stripeSimulated = false;
        this.stripeClientSecret = '';
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

                {{-- Métodos de pagamento --}}
                <div class="space-y-3">

                    {{-- PIX — destaque (mais usado) --}}
                    <label class="relative block cursor-pointer group">
                        <input type="radio" name="payment_method" value="pix" x-model="paymentMethod" class="sr-only">
                        <div class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl border-2 transition-all"
                             :class="paymentMethod === 'pix'
                                ? 'border-[#32BCAD] bg-[#32BCAD]/5 ring-2 ring-[#32BCAD]/15'
                                : 'border-slate-200 bg-white hover:border-[#32BCAD]/60 hover:bg-[#32BCAD]/[0.03]'">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                 :class="paymentMethod === 'pix' ? 'bg-[#32BCAD]/15' : 'bg-slate-50 group-hover:bg-[#32BCAD]/10'">
                                {{-- PIX icon oficial (Banco Central) --}}
                                <svg class="w-6 h-6" viewBox="0 0 512 512" fill="#32BCAD" aria-label="PIX">
                                    <path d="M112.57 391.19c20.056 0 38.928-7.808 53.12-21.984l76.693-76.692c5.385-5.404 14.765-5.384 20.15 0l76.989 76.989c14.191 14.177 33.045 21.985 53.12 21.985h15.098l-97.21 97.21c-30.328 30.328-79.476 30.328-109.806 0l-97.515-97.508h9.371z"/>
                                    <path d="M398.766 120.792c-20.056 0-38.929 7.809-53.12 21.985l-76.99 76.988c-5.207 5.225-14.952 5.247-20.15-.001l-76.692-76.692c-14.192-14.177-33.063-21.985-53.12-21.985h-9.372l97.516-97.515c30.328-30.327 79.478-30.327 109.806 0l97.21 97.22h-15.098z"/>
                                    <path d="M22.758 200.852l58.733-58.733c1.298.49 2.704.795 4.183.795h26.913c13.847 0 27.41 5.608 37.18 15.395l76.69 76.689c6.752 6.753 15.612 10.126 24.477 10.126 8.86 0 17.726-3.373 24.471-10.118l76.99-76.991c9.768-9.787 23.333-15.395 37.179-15.395h32.624c1.479 0 2.885-.306 4.183-.795l58.987 58.987c30.328 30.328 30.328 79.476 0 109.806l-58.98 58.979c-1.305-.482-2.717-.787-4.19-.787h-32.624c-13.846 0-27.411-5.609-37.18-15.396l-76.982-76.982c-13.077-13.084-35.866-13.092-48.96.008l-76.689 76.682c-9.768 9.787-23.333 15.395-37.18 15.395H85.674c-1.479 0-2.885.306-4.183.795l-58.733-58.74c-30.327-30.328-30.327-79.478 0-109.804z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-sm font-bold leading-none"
                                       :class="paymentMethod === 'pix' ? 'text-[#0f8a7e]' : 'text-slate-800'">PIX</p>
                                    <span class="text-[9px] font-black uppercase tracking-wide px-1.5 py-0.5 rounded-full bg-[#32BCAD]/10 text-[#0f8a7e]">Mais usado</span>
                                </div>
                                <p class="text-[11px] text-slate-400">Aprovação imediata · via Mercado Pago</p>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                                 :class="paymentMethod === 'pix' ? 'border-[#32BCAD] bg-[#32BCAD]' : 'border-slate-300 bg-white'">
                                <svg class="w-3 h-3 text-white transition-all" :class="paymentMethod === 'pix' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                    </label>

                    {{-- Demais métodos — grade 2 colunas --}}
                    <div class="grid grid-cols-2 gap-2.5">

                        {{-- Cartão --}}
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="sr-only">
                            <div class="relative h-full flex flex-col gap-2.5 p-3.5 rounded-xl border-2 transition-all"
                                 :class="paymentMethod === 'card'
                                    ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/15'
                                    : 'border-slate-200 bg-white hover:border-slate-300'">
                                <div class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all"
                                     :class="paymentMethod === 'card' ? 'border-blue-500 bg-blue-500' : 'border-slate-300 bg-white'">
                                    <svg class="w-2.5 h-2.5 text-white" :class="paymentMethod === 'card' ? 'opacity-100' : 'opacity-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <svg viewBox="0 0 40 26" fill="none" class="w-10 h-[26px]">
                                    <rect width="40" height="26" rx="4" fill="#3B82F6"/>
                                    <rect x="0" y="7" width="40" height="5" fill="#2563EB"/>
                                    <rect x="4" y="15" width="8" height="5" rx="1.5" fill="#93C5FD"/>
                                    <circle cx="33" cy="17.5" r="4" fill="#EF4444" opacity="0.85"/>
                                    <circle cx="29" cy="17.5" r="4" fill="#F97316" opacity="0.85"/>
                                </svg>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-bold leading-tight"
                                       :class="paymentMethod === 'card' ? 'text-blue-600' : 'text-slate-800'">Cartão</p>
                                    <p class="text-[10px] text-slate-400 leading-tight mt-0.5">Crédito/Débito · Stripe</p>
                                </div>
                            </div>
                        </label>

                        {{-- Google Pay --}}
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="payment_method" value="gpay" x-model="paymentMethod" class="sr-only">
                            <div class="relative h-full flex flex-col gap-2.5 p-3.5 rounded-xl border-2 transition-all"
                                 :class="paymentMethod === 'gpay'
                                    ? 'border-slate-800 bg-slate-50 ring-2 ring-slate-800/10'
                                    : 'border-slate-200 bg-white hover:border-slate-300'">
                                <div class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all"
                                     :class="paymentMethod === 'gpay' ? 'border-slate-800 bg-slate-800' : 'border-slate-300 bg-white'">
                                    <svg class="w-2.5 h-2.5 text-white" :class="paymentMethod === 'gpay' ? 'opacity-100' : 'opacity-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <svg viewBox="0 0 40 26" class="w-10 h-[26px]">
                                    <rect width="40" height="26" rx="4" fill="#fff" stroke="#E2E8F0"/>
                                    <path d="M19.4 13.2c0-.4 0-.8-.1-1.1h-4.1v2.1h2.4a2 2 0 0 1-.9 1.3v1.1h1.4c.8-.8 1.3-1.9 1.3-3.4z" fill="#4285F4"/>
                                    <path d="M15.2 17.5c1.2 0 2.2-.4 2.9-1.1l-1.4-1.1c-.4.3-.9.4-1.5.4-1.1 0-2.1-.8-2.4-1.8h-1.5v1.1a4.4 4.4 0 0 0 3.9 2.5z" fill="#34A853"/>
                                    <path d="M12.8 13c-.2-.5-.2-1.1 0-1.7v-1.1h-1.5a4.3 4.3 0 0 0 0 3.9l1.5-1.1z" fill="#FBBC04"/>
                                    <path d="M15.2 9.7c.6 0 1.2.2 1.6.6l1.2-1.2a4.2 4.2 0 0 0-2.8-1.1 4.4 4.4 0 0 0-3.9 2.5l1.5 1.1c.3-1 1.3-1.9 2.4-1.9z" fill="#EA4335"/>
                                    <text x="22" y="16.5" font-family="Arial, sans-serif" font-size="7.5" font-weight="700" fill="#5F6368">Pay</text>
                                </svg>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-bold leading-tight"
                                       :class="paymentMethod === 'gpay' ? 'text-slate-900' : 'text-slate-800'">Google Pay</p>
                                    <p class="text-[10px] text-slate-400 leading-tight mt-0.5">1 toque · Stripe</p>
                                </div>
                            </div>
                        </label>

                        {{-- Apple Pay --}}
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="payment_method" value="applepay" x-model="paymentMethod" class="sr-only">
                            <div class="relative h-full flex flex-col gap-2.5 p-3.5 rounded-xl border-2 transition-all"
                                 :class="paymentMethod === 'applepay'
                                    ? 'border-slate-800 bg-slate-50 ring-2 ring-slate-800/10'
                                    : 'border-slate-200 bg-white hover:border-slate-300'">
                                <div class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all"
                                     :class="paymentMethod === 'applepay' ? 'border-slate-800 bg-slate-800' : 'border-slate-300 bg-white'">
                                    <svg class="w-2.5 h-2.5 text-white" :class="paymentMethod === 'applepay' ? 'opacity-100' : 'opacity-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <svg viewBox="0 0 40 26" class="w-10 h-[26px]">
                                    <rect width="40" height="26" rx="4" fill="#000"/>
                                    <path d="M13.6 9.4c.4-.5.7-1.1.6-1.8-.6 0-1.3.4-1.7.9-.4.4-.7 1.1-.6 1.7.7 0 1.3-.3 1.7-.8z" fill="#fff"/>
                                    <path d="M14.2 10.3c-.9 0-1.7.5-2.1.5-.5 0-1.1-.5-1.9-.5-1 0-1.9.6-2.4 1.4-1 1.8-.3 4.4.7 5.9.5.7 1.1 1.5 1.8 1.5.7 0 1-.5 1.9-.5.9 0 1.1.5 1.9.4.8 0 1.3-.7 1.8-1.4.6-.8.8-1.6.8-1.6 0 0-1.5-.6-1.6-2.3 0-1.5 1.2-2.2 1.2-2.2-.6-1-1.7-1.1-2-1.1-.9-.1-1.7.5-2.1.5z" fill="#fff"/>
                                    <text x="22.5" y="16.5" font-family="Arial, sans-serif" font-size="7.5" font-weight="600" fill="#fff">Pay</text>
                                </svg>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-bold leading-tight"
                                       :class="paymentMethod === 'applepay' ? 'text-slate-900' : 'text-slate-800'">Apple Pay</p>
                                    <p class="text-[10px] text-slate-400 leading-tight mt-0.5">1 toque · Stripe</p>
                                </div>
                            </div>
                        </label>

                        {{-- Boleto --}}
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="payment_method" value="boleto" x-model="paymentMethod" class="sr-only">
                            <div class="relative h-full flex flex-col gap-2.5 p-3.5 rounded-xl border-2 transition-all"
                                 :class="paymentMethod === 'boleto'
                                    ? 'border-emerald-600 bg-emerald-50 ring-2 ring-emerald-600/15'
                                    : 'border-slate-200 bg-white hover:border-slate-300'">
                                <div class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all"
                                     :class="paymentMethod === 'boleto' ? 'border-emerald-600 bg-emerald-600' : 'border-slate-300 bg-white'">
                                    <svg class="w-2.5 h-2.5 text-white" :class="paymentMethod === 'boleto' ? 'opacity-100' : 'opacity-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <svg viewBox="0 0 40 26" class="w-10 h-[26px]">
                                    <rect width="40" height="26" rx="4" fill="#F8FAFC" stroke="#E2E8F0"/>
                                    <g fill="#0F172A">
                                        <rect x="5" y="6" width="1.5" height="14"/>
                                        <rect x="7.5" y="6" width="1" height="14"/>
                                        <rect x="10" y="6" width="2" height="14"/>
                                        <rect x="13.5" y="6" width="1" height="14"/>
                                        <rect x="16" y="6" width="1.5" height="14"/>
                                        <rect x="19" y="6" width="1" height="14"/>
                                        <rect x="21.5" y="6" width="2" height="14"/>
                                        <rect x="25" y="6" width="1" height="14"/>
                                        <rect x="27.5" y="6" width="1.5" height="14"/>
                                        <rect x="30.5" y="6" width="1" height="14"/>
                                        <rect x="33" y="6" width="2" height="14"/>
                                    </g>
                                </svg>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-bold leading-tight"
                                       :class="paymentMethod === 'boleto' ? 'text-emerald-700' : 'text-slate-800'">Boleto</p>
                                    <p class="text-[10px] text-slate-400 leading-tight mt-0.5">1–3 dias úteis · Stripe</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
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
                <div x-show="paymentMethod === 'gpay'" x-transition
                     class="flex items-start gap-2.5 bg-slate-50 border border-slate-200 rounded-xl p-3">
                    <x-heroicon-s-shield-check class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
                    <p class="text-[11px] font-medium leading-relaxed text-slate-600">
                        Finalize com o Google Pay direto na tela da Stripe. Disponível em navegadores compatíveis (ex.: Chrome com um cartão salvo).
                    </p>
                </div>
                <div x-show="paymentMethod === 'applepay'" x-transition
                     class="flex items-start gap-2.5 bg-slate-50 border border-slate-200 rounded-xl p-3">
                    <x-heroicon-s-shield-check class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
                    <p class="text-[11px] font-medium leading-relaxed text-slate-600">
                        Finalize com o Apple Pay direto na tela da Stripe. Disponível no Safari, em dispositivos Apple com um cartão na Wallet.
                    </p>
                </div>
                <div x-show="paymentMethod === 'boleto'" x-transition
                     class="flex items-start gap-2.5 bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                    <x-heroicon-s-information-circle class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                    <p class="text-[11px] font-medium leading-relaxed text-emerald-700">
                        Gere o boleto e pague em qualquer banco ou app. O código VIP é liberado após a compensação (1–3 dias úteis).
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
                        : (paymentMethod === 'boleto'
                            ? 'bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20'
                            : (paymentMethod === 'gpay' || paymentMethod === 'applepay'
                                ? 'bg-slate-900 hover:bg-black shadow-lg shadow-slate-900/20'
                                : 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20'))">
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
                <template x-if="paymentMethod === 'gpay'">
                    <span class="flex items-center gap-2.5">
                        <x-heroicon-s-device-phone-mobile class="w-5 h-5" />
                        Pagar R$ {{ number_format($package->price, 2, ',', '.') }} com Google Pay
                    </span>
                </template>
                <template x-if="paymentMethod === 'applepay'">
                    <span class="flex items-center gap-2.5">
                        <x-heroicon-s-device-phone-mobile class="w-5 h-5" />
                        Pagar R$ {{ number_format($package->price, 2, ',', '.') }} com Apple Pay
                    </span>
                </template>
                <template x-if="paymentMethod === 'boleto'">
                    <span class="flex items-center gap-2.5">
                        <x-heroicon-s-document-text class="w-5 h-5" />
                        Gerar Boleto de R$ {{ number_format($package->price, 2, ',', '.') }}
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
         @click.self="closeStripeModal()"
         @keydown.escape.window="stripeModalOpen && closeStripeModal()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="relative w-full max-w-xl bg-white border border-slate-200 rounded-2xl shadow-2xl p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
            <button @click="closeStripeModal()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     :class="paymentMethod === 'boleto' ? 'bg-emerald-50' : (paymentMethod === 'card' ? 'bg-blue-50' : 'bg-slate-100')">
                    <span x-show="paymentMethod === 'card'"><x-heroicon-s-credit-card class="w-5 h-5 text-blue-500" /></span>
                    <span x-show="paymentMethod === 'gpay' || paymentMethod === 'applepay'"><x-heroicon-s-device-phone-mobile class="w-5 h-5 text-slate-700" /></span>
                    <span x-show="paymentMethod === 'boleto'"><x-heroicon-s-document-text class="w-5 h-5 text-emerald-600" /></span>
                </div>
                <div>
                    <h2 class="font-black text-slate-900" x-text="
                        paymentMethod === 'gpay' ? 'Pagamento com Google Pay'
                        : paymentMethod === 'applepay' ? 'Pagamento com Apple Pay'
                        : paymentMethod === 'boleto' ? 'Pagamento com Boleto'
                        : 'Pagamento com Cartão'"></h2>
                    <p class="text-xs text-slate-500">{{ $package->name }} · <span class="font-bold text-slate-700">{{ $package->formatted_price }}</span></p>
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
