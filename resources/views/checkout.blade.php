@php
    $theme = [
        'bg' => 'bg-amber-400',
        'text' => 'text-amber-500',
        'border' => 'border-amber-200',
        'gradient_from' => 'from-amber-50',
        'text_dark' => 'text-amber-600',
        'alert_bg' => 'bg-amber-50',
        'alert_border' => 'border-amber-200',
        'alert_text' => 'text-amber-800',
        'btn_from' => 'from-amber-500',
        'btn_to' => 'to-amber-600',
        'btn_hover_from' => 'hover:from-amber-600',
        'btn_hover_to' => 'hover:to-amber-700',
        'btn_outline_border' => 'border-amber-300',
        'btn_outline_bg' => 'bg-amber-50',
        'btn_outline_text' => 'text-amber-700',
        'btn_outline_hover' => 'hover:bg-amber-100'
    ];

    $name = strtolower($package->name);
    if (str_contains($name, 'bronze')) {
        $theme = ['bg' => 'bg-orange-500', 'text' => 'text-orange-500', 'border' => 'border-orange-200', 'gradient_from' => 'from-orange-50', 'text_dark' => 'text-orange-600', 'alert_bg' => 'bg-orange-50', 'alert_border' => 'border-orange-200', 'alert_text' => 'text-orange-800', 'btn_from' => 'from-orange-500', 'btn_to' => 'to-orange-600', 'btn_hover_from' => 'hover:from-orange-600', 'btn_hover_to' => 'hover:to-orange-700', 'btn_outline_border' => 'border-orange-300', 'btn_outline_bg' => 'bg-orange-50', 'btn_outline_text' => 'text-orange-700', 'btn_outline_hover' => 'hover:bg-orange-100'];
    } elseif (str_contains($name, 'prata')) {
        $theme = ['bg' => 'bg-slate-500', 'text' => 'text-slate-500', 'border' => 'border-slate-200', 'gradient_from' => 'from-slate-50', 'text_dark' => 'text-slate-600', 'alert_bg' => 'bg-slate-50', 'alert_border' => 'border-slate-200', 'alert_text' => 'text-slate-800', 'btn_from' => 'from-slate-500', 'btn_to' => 'to-slate-600', 'btn_hover_from' => 'hover:from-slate-600', 'btn_hover_to' => 'hover:to-slate-700', 'btn_outline_border' => 'border-slate-300', 'btn_outline_bg' => 'bg-slate-50', 'btn_outline_text' => 'text-slate-700', 'btn_outline_hover' => 'hover:bg-slate-100'];
    } elseif (str_contains($name, 'diamante')) {
        $theme = ['bg' => 'bg-cyan-500', 'text' => 'text-cyan-500', 'border' => 'border-cyan-200', 'gradient_from' => 'from-cyan-50', 'text_dark' => 'text-cyan-600', 'alert_bg' => 'bg-cyan-50', 'alert_border' => 'border-cyan-200', 'alert_text' => 'text-cyan-800', 'btn_from' => 'from-cyan-500', 'btn_to' => 'to-cyan-600', 'btn_hover_from' => 'hover:from-cyan-600', 'btn_hover_to' => 'hover:to-cyan-700', 'btn_outline_border' => 'border-cyan-300', 'btn_outline_bg' => 'bg-cyan-50', 'btn_outline_text' => 'text-cyan-700', 'btn_outline_hover' => 'hover:bg-cyan-100'];
    } elseif (str_contains($name, 'estrela')) {
        $theme = ['bg' => 'bg-purple-500', 'text' => 'text-purple-500', 'border' => 'border-purple-200', 'gradient_from' => 'from-purple-50', 'text_dark' => 'text-purple-600', 'alert_bg' => 'bg-purple-50', 'alert_border' => 'border-purple-200', 'alert_text' => 'text-purple-800', 'btn_from' => 'from-purple-500', 'btn_to' => 'to-purple-600', 'btn_hover_from' => 'hover:from-purple-600', 'btn_hover_to' => 'hover:to-purple-700', 'btn_outline_border' => 'border-purple-300', 'btn_outline_bg' => 'bg-purple-50', 'btn_outline_text' => 'text-purple-700', 'btn_outline_hover' => 'hover:bg-purple-100'];
    }
@endphp

@extends('layouts.app')

@section('title', 'Checkout — Pacote ' . $package->name . ' | WhatsGrupos')
@section('description', 'Finalize sua compra do pacote ' . $package->name . ' com ' . $package->boosts_count . ' impulsos VIP.')

@push('head')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')

<div class="max-w-4xl mx-auto py-8 px-4" x-data="{
    paymentMethod: 'pix',
    copied: false,
    
    // Stripe Embedded Modal
    stripeModalOpen: false,
    stripeLoading: false,
    stripeClientSecret: '',
    stripePublishableKey: '',
    stripeError: '',
    stripeSimulated: false,
    stripeRedirectUrl: '',
    
    // Asaas PIX Modal
    pixModalOpen: false,
    pixLoading: false,
    pixGenerated: false,
    pixQrCode: '',
    pixCopyPaste: '',
    pixOrderId: null,
    pixInterval: null,
    
    // Form inputs
    buyerName: '{{ old('buyer_name', '') }}',
    buyerEmail: '{{ old('buyer_email', '') }}',
    
    initStripeCheckout() {
        if (!this.buyerName || !this.buyerName.trim()) {
            alert('Por favor, preencha o seu Nome Completo.');
            return;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.buyerEmail || !this.buyerEmail.trim() || !emailRegex.test(this.buyerEmail)) {
            alert('Por favor, preencha um endereço de E-mail válido.');
            return;
        }
        
        this.stripeLoading = true;
        this.stripeModalOpen = true;
        this.stripeError = '';
        this.stripeSimulated = false;
        
        fetch('{{ route('boost.checkout-stripe-embedded', $package->slug) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                buyer_name: this.buyerName,
                buyer_email: this.buyerEmail
            })
        })
        .then(r => r.json())
        .then(data => {
            this.stripeLoading = false;
            if (data.success) {
                if (data.is_simulated) {
                    this.stripeSimulated = true;
                    this.stripeRedirectUrl = data.redirect_url;
                } else {
                    if (!data.publishable_key) {
                        this.stripeError = 'Erro: A chave pública da Stripe (STRIPE_KEY) não está configurada no seu arquivo .env. Por favor, insira-a para carregar o checkout real.';
                        return;
                    }
                    
                    this.stripeClientSecret = data.client_secret;
                    this.stripePublishableKey = data.publishable_key;
                    
                    // Inicializa o Stripe Embedded Checkout
                    setTimeout(() => {
                        try {
                            const stripe = Stripe(data.publishable_key);
                            stripe.initEmbeddedCheckout({
                                clientSecret: data.client_secret
                            }).then(checkout => {
                                checkout.mount('#stripe-checkout-container');
                            }).catch(err => {
                                this.stripeError = 'Erro ao montar o checkout da Stripe: ' + err.message;
                            });
                        } catch (err) {
                            this.stripeError = 'Erro ao inicializar o SDK da Stripe: ' + err.message;
                        }
                    }, 200);
                }
            } else {
                this.stripeError = data.message || 'Erro ao inicializar o processador de pagamentos.';
            }
        })
        .catch(() => {
            this.stripeLoading = false;
            this.stripeError = 'Erro de conexão com o servidor da Stripe.';
        });
    },
    
    generateAsaasPix() {
        if (!this.buyerName || !this.buyerName.trim()) {
            alert('Por favor, preencha o seu Nome Completo.');
            return;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!this.buyerEmail || !this.buyerEmail.trim() || !emailRegex.test(this.buyerEmail)) {
            alert('Por favor, preencha um endereço de E-mail válido.');
            return;
        }
        
        this.pixLoading = true;
        this.pixGenerated = false;
        
        fetch('{{ route('boost.checkout-asaas-pix', $package->slug) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                buyer_name: this.buyerName,
                buyer_email: this.buyerEmail,
            })
        })
        .then(r => r.json())
        .then(data => {
            this.pixLoading = false;
            if (data.success) {
                this.pixGenerated = true;
                this.pixQrCode = data.qr_code;
                this.pixCopyPaste = data.copy_paste;
                this.pixOrderId = data.order_id;
                
                // Polling do pagamento de 5 em 5 segundos
                this.startPolling(data.order_id);
            } else {
                alert('Erro ao gerar cobrança PIX no Asaas. Tente novamente.');
            }
        })
        .catch(() => {
            this.pixLoading = false;
            alert('Erro de conexão ao gerar o PIX.');
        });
    },
    
    startPolling(orderId) {
        if (this.pixInterval) clearInterval(this.pixInterval);
        this.pixInterval = setInterval(() => {
            fetch('/pagamento/pix-status/' + orderId)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'paid' && data.redirect_url) {
                        clearInterval(this.pixInterval);
                        window.location.href = data.redirect_url;
                    }
                })
                .catch(() => {});
        }, 5000);
    },
    
    copyPixCode() {
        navigator.clipboard.writeText(this.pixCopyPaste).then(() => {
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        });
    }
}">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 font-semibold mb-8">
        <a href="{{ route('boost.packages') }}" class="hover:text-[#25D366] transition-colors">Pacotes VIP</a>
        <x-heroicon-m-chevron-right class="w-4 h-4 text-slate-300" />
        <span class="text-slate-900">Checkout</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- Formulário (Esquerda) --}}
        <div class="lg:col-span-8 order-2 lg:order-1">
            
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                    <x-heroicon-s-shopping-bag class="w-5 h-5" />
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">Finalizar Compra <x-heroicon-s-lock-closed class="w-5 h-5 text-slate-300" /></h1>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-10 shadow-2xl shadow-slate-200/50">
                
                <div class="space-y-6">
                    {{-- Nome --}}
                    <div>
                        <label for="buyer_name" class="block text-slate-700 font-bold text-sm mb-2">Nome Completo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-o-user class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" id="buyer_name" name="buyer_name" x-model="buyerName" required
                                   placeholder="Seu nome completo"
                                   class="w-full rounded-xl pl-10 pr-4 py-3 text-sm text-slate-900 border border-slate-300 focus:border-[#25D366] focus:ring focus:ring-green-200 focus:ring-opacity-50 outline-none transition-all bg-slate-50">
                        </div>
                    </div>

                    {{-- E-mail --}}
                    <div>
                        <label for="buyer_email" class="block text-slate-700 font-bold text-sm mb-2">E-mail <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-o-envelope class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="email" id="buyer_email" name="buyer_email" x-model="buyerEmail" required
                                   placeholder="seu@email.com"
                                   class="w-full rounded-xl pl-10 pr-4 py-3 text-sm text-slate-900 border border-slate-300 focus:border-[#25D366] focus:ring focus:ring-green-200 focus:ring-opacity-50 outline-none transition-all bg-slate-50">
                        </div>
                        <p class="text-slate-500 text-[11px] font-semibold mt-1.5 flex items-center gap-1">
                            <x-heroicon-s-information-circle class="w-3.5 h-3.5 text-blue-500" /> O código de impulso será enviado para este e-mail imediatamente após o pagamento.
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-100"></div>

                    {{-- Método de pagamento --}}
                    <div>
                        <label class="block text-slate-700 font-bold text-sm mb-3">Forma de Pagamento <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="payment_method" value="pix" x-model="paymentMethod" class="peer hidden">
                                <div class="rounded-2xl p-5 border-2 transition-all flex flex-col items-center justify-center gap-3 h-full bg-white peer-checked:border-[#25D366] peer-checked:bg-green-50/50 peer-checked:shadow-xl peer-checked:shadow-green-500/10 border-slate-200 hover:border-slate-300 hover:shadow-md hover:-translate-y-0.5">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
                                        <svg class="w-6 h-6 text-[#32BCAD]" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-slate-900 font-black text-sm">PIX</p>
                                        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-0.5">Aprovação imediata via Asaas</p>
                                    </div>
                                </div>
                                <div class="absolute top-2 right-2 text-[#25D366] opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <x-heroicon-s-check-circle class="w-5 h-5" />
                                </div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="peer hidden">
                                <div class="rounded-2xl p-5 border-2 transition-all flex flex-col items-center justify-center gap-3 h-full bg-white peer-checked:border-blue-500 peer-checked:bg-blue-50/50 peer-checked:shadow-xl peer-checked:shadow-blue-500/10 border-slate-200 hover:border-slate-300 hover:shadow-md hover:-translate-y-0.5">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
                                        <x-heroicon-s-credit-card class="w-6 h-6 text-blue-500" />
                                    </div>
                                    <div class="text-center">
                                        <p class="text-slate-900 font-black text-sm">Cartão de Crédito</p>
                                        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-0.5">Modal Integrado Stripe</p>
                                    </div>
                                </div>
                                <div class="absolute top-2 right-2 text-blue-500 opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <x-heroicon-s-check-circle class="w-5 h-5" />
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Botão de submit --}}
                <div class="mt-8">
                    <button type="button"
                            @click="paymentMethod === 'pix' ? (pixModalOpen = true) : initStripeCheckout()"
                            class="w-full py-4 rounded-xl font-black text-lg transition-all shadow-sm text-white flex items-center justify-center gap-2"
                            :class="paymentMethod === 'pix'
                                ? 'bg-[#25D366] hover:bg-[#20bd5a]'
                                : 'bg-blue-600 hover:bg-blue-700'">
                        <span x-show="paymentMethod === 'pix'" class="flex items-center gap-2">Pagar R$ {{ number_format($package->price, 2, ',', '.') }} no PIX <x-heroicon-s-arrow-right class="w-5 h-5" /></span>
                        <span x-show="paymentMethod === 'card'" class="flex items-center gap-2">Pagar com Cartão <x-heroicon-s-arrow-right class="w-5 h-5" /></span>
                    </button>
                    
                    <div class="flex items-center justify-center gap-1.5 mt-4 text-slate-400 text-xs font-bold uppercase tracking-widest">
                        <x-heroicon-s-lock-closed class="w-3.5 h-3.5" /> Pagamento 100% seguro via Stripe / Asaas
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumo do pacote (Direita Sidebar) --}}
        <div class="lg:col-span-4 order-1 lg:order-2">
            <div class="bg-white rounded-3xl border border-slate-100 sticky top-24 shadow-2xl shadow-slate-200/50 relative overflow-hidden">

                {{-- Header do Pacote com gradiente temático --}}
                @php
                    $headerClass = match(true) {
                        str_contains(strtolower($package->name), 'bronze')   => 'from-orange-800 via-orange-700 to-amber-600',
                        str_contains(strtolower($package->name), 'prata')    => 'from-slate-500 via-slate-400 to-slate-300',
                        str_contains(strtolower($package->name), 'ouro')     => 'from-yellow-500 via-amber-400 to-yellow-300',
                        str_contains(strtolower($package->name), 'diamante') => 'from-cyan-600 via-cyan-400 to-teal-300',
                        str_contains(strtolower($package->name), 'estrela')  => 'from-purple-700 via-purple-500 to-pink-400',
                        default                                               => 'from-slate-700 via-slate-500 to-slate-400',
                    };
                    $medalIcon = match(true) {
                        str_contains(strtolower($package->name), 'bronze')   => '🥉',
                        str_contains(strtolower($package->name), 'prata')    => '🥈',
                        str_contains(strtolower($package->name), 'ouro')     => '🥇',
                        str_contains(strtolower($package->name), 'diamante') => '💎',
                        str_contains(strtolower($package->name), 'estrela')  => '⭐',
                        default                                               => '🚀',
                    };
                @endphp

                <div class="bg-gradient-to-br {{ $headerClass }} p-6 relative overflow-hidden">
                    {{-- Ornamento decorativo --}}
                    <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-white/10 blur-xl"></div>
                    <div class="absolute -left-4 bottom-0 w-20 h-20 rounded-full bg-black/10 blur-lg"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-3xl">{{ $medalIcon }}</span>
                            <div>
                                <p class="text-white/70 text-[10px] font-bold uppercase tracking-widest">Pacote VIP</p>
                                <h3 class="text-white font-black text-xl leading-tight">{{ $package->name }}</h3>
                            </div>
                        </div>
                        <div class="flex items-end gap-2 mt-4">
                            @if ($package->savings_percent > 0)
                                <p class="text-white/50 font-bold text-sm line-through">{{ $package->formatted_original_price }}</p>
                                <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full border border-white/30">
                                    {{ $package->discount_label }}
                                </span>
                            @endif
                        </div>
                        <p class="text-white font-black text-4xl tracking-tight mt-1">{{ $package->formatted_price }}</p>
                        <p class="text-white/60 text-xs font-semibold mt-1">pagamento único · sem mensalidade</p>
                    </div>
                </div>

                <div class="p-6">
                    {{-- O que está incluído --}}
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-4">O que está incluído</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-3 text-slate-700 text-sm font-medium">
                            <span class="w-5 h-5 rounded-full bg-orange-100 flex items-center justify-center shrink-0 mt-0.5">
                                <x-heroicon-s-bolt class="w-3 h-3 text-orange-500" />
                            </span>
                            <span><strong class="text-slate-900">{{ $package->boosts_count }} impulso{{ $package->boosts_count > 1 ? 's' : '' }} VIP</strong> · 12h de destaque cada</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-700 text-sm font-medium">
                            <span class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                                <x-heroicon-s-envelope class="w-3 h-3 text-blue-500" />
                            </span>
                            Código enviado por e-mail imediatamente após o pagamento
                        </li>
                        <li class="flex items-start gap-3 text-slate-700 text-sm font-medium">
                            <span class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center shrink-0 mt-0.5">
                                <x-heroicon-s-shield-check class="w-3 h-3 text-green-500" />
                            </span>
                            Sem prazo de validade — use quando quiser
                        </li>
                        <li class="flex items-start gap-3 text-slate-700 text-sm font-medium">
                            <span class="w-5 h-5 rounded-full bg-purple-100 flex items-center justify-center shrink-0 mt-0.5">
                                <x-heroicon-s-user-group class="w-3 h-3 text-purple-500" />
                            </span>
                            Válido para qualquer grupo cadastrado na sua conta
                        </li>
                    </ul>

                    {{-- Divider --}}
                    <div class="border-t border-slate-100 my-5"></div>

                    {{-- Garantia / Confiança --}}
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-4">
                        <x-heroicon-s-lock-closed class="w-5 h-5 text-slate-400 shrink-0" />
                        <p class="text-slate-500 text-xs font-semibold leading-snug">
                            Pagamento 100% seguro via <span class="text-slate-700 font-bold">Stripe</span> ou <span class="text-slate-700 font-bold">Asaas</span>. Seus dados são criptografados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================================= -->
    <!-- MODAL PREMIUM 1: CARTÃO DE CRÉDITO (STRIPE EMBEDDED CHECKOUT) -->
    <!-- ============================================================================= -->
    <div x-show="stripeModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="relative w-full max-w-2xl bg-white border border-slate-200 rounded-3xl shadow-2xl p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
            <!-- Botão de Fechar -->
            <button @click="stripeModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 transition-colors">
                <x-heroicon-o-x-mark class="w-7 h-7" />
            </button>

            <!-- Título -->
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                    <x-heroicon-s-credit-card class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">Pagamento com Cartão</h2>
                    <p class="text-xs text-slate-500">Pacote {{ $package->name }} · Total: <span class="font-extrabold text-blue-600">R$ {{ number_format($package->price, 2, ',', '.') }}</span></p>
                </div>
            </div>

            <!-- Estado de Carregamento -->
            <div x-show="stripeLoading" class="py-12 flex flex-col items-center justify-center space-y-4">
                <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-bold text-slate-600">Inicializando checkout seguro da Stripe...</p>
            </div>

            <!-- Mensagem de Erro -->
            <div x-show="stripeError" class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm font-semibold text-center mb-6">
                <p x-text="stripeError"></p>
            </div>

            <!-- Caso Modo Simulação Local (Dev) -->
            <div x-show="stripeSimulated && !stripeLoading" class="py-6 space-y-6 text-center">
                <div class="bg-amber-50 border {{ $theme['border'] }} rounded-2xl p-5 {{ $theme['alert_text'] }} text-sm font-semibold max-w-md mx-auto shadow-sm">
                    <h3 class="font-black text-base flex items-center justify-center gap-1.5 mb-2">💡 Modo Simulação (Ambiente Local)</h3>
                    <p class="leading-relaxed">Stripe rodando em modo sandbox ou chaves não inseridas. Você pode simular o pagamento aprovado imediatamente clicando no botão abaixo!</p>
                </div>
                <a :href="stripeRedirectUrl" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r {{ $theme['gradient_from'] }}0 to-amber-600 {{ $theme['btn_hover_from'] }} {{ $theme['btn_hover_to'] }} text-white font-extrabold px-8 py-3 rounded-xl shadow-md transition-all">
                    <x-heroicon-s-beaker class="w-5 h-5" /> Simular Pagamento Aprovado
                </a>
            </div>

            <!-- Formulário Real Stripe Embedded -->
            <div x-show="!stripeSimulated && !stripeLoading && !stripeError" class="w-full min-h-[300px]">
                <div id="stripe-checkout-container">
                    <!-- Stripe Checkout Session mounts here -->
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-400">
                <span class="flex items-center gap-1"><x-heroicon-s-lock-closed class="w-3.5 h-3.5" /> Conexão Criptografada SSL</span>
                <span class="font-extrabold uppercase tracking-widest text-[9px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded">Stripe</span>
            </div>
        </div>
    </div>

    <!-- ============================================================================= -->
    <!-- MODAL PREMIUM 2: PIX ASAAS -->
    <!-- ============================================================================= -->
    <div x-show="pixModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="relative w-full max-w-md bg-white border border-slate-200 rounded-3xl shadow-2xl p-6 sm:p-8 overflow-y-auto max-h-[95vh]">
            <!-- Botão de Fechar -->
            <button @click="pixModalOpen = false; if (pixInterval) clearInterval(pixInterval);" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 transition-colors">
                <x-heroicon-o-x-mark class="w-7 h-7" />
            </button>

            <!-- Título com branding Asaas -->
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-10 h-10 bg-[#00BFA5]/10 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#32BCAD]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">Pagamento com PIX</h2>
                    <p class="text-xs text-slate-500">Pacote {{ $package->name }} · Total: <span class="font-extrabold text-[#25D366]">R$ {{ number_format($package->price, 2, ',', '.') }}</span></p>
                </div>
            </div>

            <!-- FASE 1: Form de dados para gerar o PIX -->
            <div x-show="!pixGenerated && !pixLoading" class="space-y-4">
                {{-- Nome completo --}}
                <div>
                    <label for="pix_buyer_name" class="block text-slate-700 font-bold text-xs uppercase tracking-wider mb-1.5">Nome Completo</label>
                    <input type="text" id="pix_buyer_name" x-model="buyerName" placeholder="Seu nome completo"
                           class="w-full rounded-xl px-4 py-3 text-sm text-slate-900 border border-slate-300 focus:border-[#25D366] focus:ring focus:ring-green-200 outline-none transition-all bg-slate-50">
                </div>

                {{-- E-mail --}}
                <div>
                    <label for="pix_buyer_email" class="block text-slate-700 font-bold text-xs uppercase tracking-wider mb-1.5">E-mail</label>
                    <input type="email" id="pix_buyer_email" x-model="buyerEmail" placeholder="seu@email.com"
                           class="w-full rounded-xl px-4 py-3 text-sm text-slate-900 border border-slate-300 focus:border-[#25D366] focus:ring focus:ring-green-200 outline-none transition-all bg-slate-50">
                    <p class="text-slate-400 text-[10px] font-semibold mt-1.5 flex items-center gap-1">
                        <x-heroicon-s-information-circle class="w-3 h-3 text-blue-400" /> O código VIP será enviado para este e-mail após o pagamento.
                    </p>
                </div>

                <button type="button"
                        @click="generateAsaasPix()"
                        class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-extrabold py-4 rounded-xl text-sm transition-all shadow-md mt-2 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36ZM18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56ZM1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156L1.64 12.015ZM22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156l4.56 4.56Z"/></svg>
                    Gerar QR Code PIX
                </button>
            </div>

            <!-- FASE 2: Carregamento do PIX -->
            <div x-show="pixLoading" class="py-12 flex flex-col items-center justify-center space-y-4">
                <svg class="animate-spin h-10 w-10 text-[#25D366]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-bold text-slate-600">Gerando cobrança no Asaas...</p>
                <p class="text-xs text-slate-400">Isso leva apenas alguns segundos</p>
            </div>

            <!-- FASE 3: QR Code e Código de Cópia Exibidos -->
            <div x-show="pixGenerated && !pixLoading" class="space-y-6 text-center">
                <div class="inline-flex items-center gap-1.5 bg-green-50 border border-green-200 rounded-full px-4 py-1 text-green-700 text-xs font-bold mb-2">
                    <x-heroicon-s-clock class="w-3.5 h-3.5" /> Aguardando confirmação do pagamento PIX
                </div>

                <!-- Imagem QR Code -->
                <div class="flex justify-center">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm inline-block">
                        <img :src="pixQrCode" alt="QR Code PIX" class="w-48 h-48 mix-blend-multiply">
                    </div>
                </div>

                <!-- Chave copia e cola -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-left shadow-sm">
                    <p class="text-slate-500 text-[10px] mb-1.5 font-bold uppercase tracking-widest flex items-center gap-1">
                        <x-heroicon-o-document-duplicate class="w-3 h-3" /> Pix Copia e Cola:
                    </p>
                    <p x-text="pixCopyPaste" class="text-slate-900 text-xs font-mono font-semibold break-all leading-normal select-all"></p>
                </div>

                <!-- Botão de copiar -->
                <button type="button"
                        @click="copyPixCode()"
                        class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-extrabold py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                    <span x-show="!copied" class="flex items-center gap-2"><x-heroicon-o-clipboard-document class="w-5 h-5" /> Copiar Código PIX</span>
                    <span x-show="copied" class="flex items-center gap-2"><x-heroicon-s-check-circle class="w-5 h-5" /> Copiado!</span>
                </button>

                <!-- Simulação de Pagamento para Dev local -->
                @if(app()->environment('local'))
                    <div class="border-t border-slate-100 pt-4 mt-4">
                        <a :href="'/pagamento/sucesso/' + pixOrderId + '?simulated=asaas-pix'"
                           class="w-full inline-flex py-3.5 rounded-xl font-extrabold text-xs border {{ $theme['btn_outline_border'] }} {{ $theme['btn_outline_bg'] }} {{ $theme['btn_outline_text'] }} {{ $theme['btn_outline_hover'] }} transition-colors items-center justify-center gap-2">
                            <x-heroicon-s-beaker class="w-4 h-4" /> Simular Pagamento Aprovado (Dev)
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-400">
                <span class="flex items-center gap-1"><x-heroicon-s-lock-closed class="w-3.5 h-3.5" /> Transação Segura via Asaas</span>
                <span class="font-extrabold uppercase tracking-widest text-[9px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded">Asaas</span>
            </div>
        </div>
    </div>

</div>

@endsection


