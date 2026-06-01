@extends('layouts.app')

@section('title', 'Pacotes Super VIP — Impulsione seu Grupo de WhatsApp | WhatsGrupos')
@section('description', 'Coloque seu grupo de WhatsApp no topo do diretório! Escolha seu pacote VIP e atraia mais membros com o Super Impulso do WhatsGrupos.')

@section('content')

{{-- ===== HERO ===== --}}
<div class="max-w-4xl mx-auto text-center py-14 px-4 mb-16">
    <span class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-black px-4 py-2 rounded-full uppercase tracking-widest mb-6">
        <x-heroicon-s-star class="w-4 h-4 text-amber-500" />
        Super VIP — Destaque Premium
    </span>
    <h1 class="text-4xl sm:text-5xl font-black text-slate-900 mb-5 tracking-tight leading-tight">
        Coloque seu grupo<br>
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-500">no topo do site</span>
    </h1>
    <p class="text-slate-500 text-base sm:text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
        Posição fixa no topo, borda dourada e badge VIP exclusivo. <strong class="text-slate-700">Até 10x mais cliques</strong> nos seus grupos.
    </p>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 max-w-lg mx-auto mb-10">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-2xl font-black text-amber-600 mb-1">+2.5K</p>
            <p class="text-slate-500 text-xs font-semibold">Grupos Impulsionados</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-2xl font-black text-green-600 mb-1">+150K</p>
            <p class="text-slate-500 text-xs font-semibold">Novos Membros</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-2xl font-black text-blue-600 mb-1">10x+</p>
            <p class="text-slate-500 text-xs font-semibold">Mais Cliques</p>
        </div>
    </div>

    <a href="#packages" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl transition-all shadow-sm">
        <x-heroicon-s-rocket-launch class="w-5 h-5" />
        Ver Pacotes
    </a>
</div>

{{-- ===== GRID DE PACOTES ===== --}}
<div class="max-w-6xl mx-auto px-4 mb-20" id="packages">
    <div class="text-center mb-10">
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">Escolha o melhor pacote</h2>
        <p class="text-slate-500 text-sm">Pacotes maiores = melhor preço por impulso.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5 items-end">
        @foreach ($packages as $package)
        @php
            $n = strtolower($package->name);
            $gradHeader = match(true) {
                str_contains($n, 'bronze')   => 'from-orange-800 to-amber-600',
                str_contains($n, 'prata')    => 'from-slate-600 to-slate-400',
                str_contains($n, 'ouro')     => 'from-yellow-600 to-amber-400',
                str_contains($n, 'diamante') => 'from-cyan-700 to-teal-400',
                str_contains($n, 'estrela')  => 'from-purple-800 to-pink-500',
                default                      => 'from-slate-700 to-slate-500',
            };
            $btnClass = match(true) {
                str_contains($n, 'bronze')   => 'bg-orange-800 hover:bg-orange-900 text-white',
                str_contains($n, 'prata')    => 'bg-slate-600 hover:bg-slate-700 text-white',
                str_contains($n, 'ouro')     => 'bg-amber-500 hover:bg-amber-600 text-white',
                str_contains($n, 'diamante') => 'bg-cyan-600 hover:bg-cyan-700 text-white',
                str_contains($n, 'estrela')  => 'bg-purple-700 hover:bg-purple-800 text-white',
                default                      => 'bg-slate-900 hover:bg-slate-800 text-white',
            };
        @endphp

        <div class="relative flex flex-col bg-white border rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-1
                    {{ $package->is_popular
                        ? 'border-amber-400 shadow-xl shadow-amber-500/20 ring-2 ring-amber-100 xl:scale-105 z-10'
                        : 'border-slate-200 shadow-sm hover:shadow-md' }}">

            {{-- Badge popular --}}
            @if ($package->is_popular)
                <div class="absolute -top-px left-0 right-0 h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>
                <div class="absolute top-3 left-1/2 -translate-x-1/2 z-20 whitespace-nowrap">
                    <span class="bg-gradient-to-r from-amber-400 to-orange-500 text-white text-[10px] font-black px-4 py-1 rounded-full uppercase tracking-widest shadow-md inline-flex items-center gap-1">
                        <x-heroicon-s-fire class="w-3 h-3" /> Mais Popular
                    </span>
                </div>
            @endif

            {{-- Header com gradiente --}}
            <div class="bg-gradient-to-br {{ $gradHeader }} px-5 pt-{{ $package->is_popular ? '9' : '5' }} pb-5 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
                <div class="relative z-10 text-center">
                    <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest mb-0.5">Pacote</p>
                    <h3 class="text-white font-black text-lg">{{ $package->name }}</h3>
                    <div class="mt-3">
                        @if ($package->savings_percent > 0)
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <span class="text-white/50 text-xs line-through font-semibold">{{ $package->formatted_original_price }}</span>
                                <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full border border-white/30">{{ $package->discount_label }}</span>
                            </div>
                        @endif
                        <p class="text-white font-black text-3xl">{{ $package->formatted_price }}</p>
                        <p class="text-white/50 text-[10px] font-semibold mt-0.5">pagamento único</p>
                    </div>
                </div>
            </div>

            {{-- Corpo --}}
            <div class="p-5 flex flex-col flex-1">
                {{-- Impulsos destaque --}}
                <div class="text-center py-3 mb-4 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-3xl font-black text-slate-900">{{ $package->boosts_count }}</span>
                    <span class="text-slate-500 text-xs font-bold ml-1">{{ $package->boosts_count > 1 ? 'impulsos' : 'impulso' }}</span>
                    <p class="text-slate-400 text-[10px] font-semibold mt-0.5">12h de destaque cada</p>
                </div>

                {{-- Benefícios --}}
                <ul class="space-y-2 mb-5 flex-1 text-xs">
                    @foreach ([
                        ['Posição fixa no topo', 'green'],
                        ['Borda dourada + badge VIP', 'amber'],
                        ['~10x mais cliques', 'blue'],
                        ['Sem prazo de validade', 'purple'],
                        ['Código por e-mail', 'slate'],
                    ] as [$benefit, $color])
                    <li class="flex items-center gap-2 text-slate-600 font-medium">
                        <span class="w-4 h-4 rounded-full bg-{{ $color }}-100 flex items-center justify-center shrink-0">
                            <x-heroicon-s-check class="w-2.5 h-2.5 text-{{ $color }}-600" />
                        </span>
                        {{ $benefit }}
                    </li>
                    @endforeach
                </ul>

                {{-- CTA --}}
                <a href="{{ route('boost.checkout', $package->slug) }}"
                   class="w-full text-center py-3 rounded-xl font-black text-sm transition-all flex items-center justify-center gap-1.5
                          {{ $package->is_popular
                             ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white shadow-md shadow-amber-500/30'
                             : $btnClass }}">
                    Comprar
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Nota de pagamentos --}}
    <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400 font-semibold">
        <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4" viewBox="-1 -1 26 26" fill="#32BCAD">
                <path d="M5.283 18.36a3.505 3.505 0 0 0 2.493-1.032l3.6-3.6a.684.684 0 0 1 .946 0l3.613 3.613a3.504 3.504 0 0 0 2.493 1.032h.71l-4.56 4.56a3.647 3.647 0 0 1-5.156 0L4.85 18.36Z"/>
                <path d="M18.428 5.627a3.505 3.505 0 0 0-2.493 1.032l-3.613 3.614a.67.67 0 0 1-.946 0l-3.6-3.6A3.505 3.505 0 0 0 5.283 5.64h-.48l4.57-4.56a3.647 3.647 0 0 1 5.155 0l4.55 4.56Z"/>
                <path d="M1.64 12.015l4.56-4.56a3.505 3.505 0 0 0 1.032 2.493l3.6 3.6a.684.684 0 0 1 0 .946l-3.613 3.613a3.504 3.504 0 0 0-1.032 2.493h.48l4.56-4.56a3.647 3.647 0 0 1 0-5.156Z"/>
                <path d="M22.36 12.015l-4.56 4.56a3.505 3.505 0 0 0-1.032-2.493l-3.613-3.6a.684.684 0 0 1 0-.946l3.6-3.613a3.504 3.504 0 0 0 1.032-2.493h-.48l-4.56 4.56a3.647 3.647 0 0 1 0 5.156Z"/>
            </svg>
            PIX (aprovação imediata)
        </span>
        <span class="flex items-center gap-1.5">
            <x-heroicon-s-credit-card class="w-4 h-4 text-blue-400" />
            Cartão de Crédito via Stripe
        </span>
        <span class="flex items-center gap-1.5">
            <x-heroicon-s-lock-closed class="w-4 h-4 text-slate-400" />
            Pagamento 100% seguro
        </span>
    </div>
</div>

{{-- ===== COMO FUNCIONA ===== --}}
<div class="border-t border-slate-100 py-16 mb-16">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-black text-slate-900 mb-2">Como funciona em 4 passos</h2>
            <p class="text-slate-500 text-sm">Do pagamento até aparecer no topo em menos de 5 minutos.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([
                ['1', 'Escolha o Pacote', 'Selecione a quantidade de impulsos ideal para você.'],
                ['2', 'Pague com PIX ou Cartão', 'Pagamento seguro via Asaas (PIX) ou Stripe (Cartão).'],
                ['3', 'Receba o Código', 'Código VIP chega no e-mail em segundos.'],
                ['4', 'Apareça no Topo', 'Cole o código no seu grupo e pronto!'],
            ] as [$n, $title, $desc])
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm text-center relative">
                <div class="w-8 h-8 bg-slate-900 text-white rounded-xl flex items-center justify-center font-black text-sm mx-auto mb-3">{{ $n }}</div>
                <p class="text-slate-900 font-bold text-sm mb-1">{{ $title }}</p>
                <p class="text-slate-400 text-xs font-medium leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ===== DEPOIMENTOS E FAQ ===== --}}
<div class="max-w-5xl mx-auto px-4 mb-20 grid grid-cols-1 lg:grid-cols-2 gap-10">

    <div>
        <h2 class="text-xl font-black text-slate-900 mb-6">O que dizem os admins</h2>
        <div class="space-y-4">
            @foreach ([
                ['Carlos M.', 'Admin de grupo de games', 'Saí de 50 para mais de 200 membros em menos de 1 hora após o impulso VIP. Vale muito!'],
                ['Ana P.', 'Dona de loja virtual', 'Uso o WhatsGrupos para divulgar meu grupo de ofertas. Sempre apareço em primeiro!'],
                ['Pedro H.', 'Criador de conteúdo', 'Compro o pacote Diamante todo mês. O retorno é impressionante. Recomendo!'],
            ] as $t)
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex gap-0.5 mb-2">
                    @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-3.5 h-3.5 text-amber-400" />@endfor
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-4 italic">"{{ $t[2] }}"</p>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs border border-slate-200">{{ substr($t[0], 0, 1) }}</div>
                    <div>
                        <p class="text-slate-900 font-bold text-xs">{{ $t[0] }}</p>
                        <p class="text-slate-400 text-[10px] font-semibold">{{ $t[1] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="text-xl font-black text-slate-900 mb-6">Perguntas Frequentes</h2>
        <div class="space-y-2" x-data="{ open: 0 }">
            @foreach ([
                ['Por quanto tempo fico em destaque?', 'Cada impulso deixa seu grupo em destaque por 12 horas no topo da página inicial e da sua categoria, com borda dourada e badge VIP.'],
                ['Posso usar em vários grupos?', 'Sim! Os impulsos podem ser distribuídos entre qualquer grupo aprovado na sua conta usando o mesmo código.'],
                ['Meu grupo precisa estar aprovado?', 'Sim. Apenas grupos com status "Aprovado" podem receber impulsos. Aguarde a aprovação antes de usar.'],
                ['Os impulsos expiram?', 'Não. Os impulsos não têm prazo de validade. Compre hoje e use quando quiser.'],
                ['E se as 12h acabarem?', 'O grupo volta ao posicionamento padrão. Você pode aplicar outro impulso na hora que quiser!'],
            ] as $i => $faq)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <button type="button"
                        class="w-full flex items-center justify-between gap-3 px-4 py-4 text-left hover:bg-slate-50 transition-colors"
                        @click="open = open === {{ $i }} ? null : {{ $i }}">
                    <span class="text-slate-900 font-bold text-sm">{{ $faq[0] }}</span>
                    <x-heroicon-m-chevron-down class="w-4 h-4 text-slate-400 transition-transform shrink-0" x-bind:class="{ 'rotate-180': open === {{ $i }} }" />
                </button>
                <div x-show="open === {{ $i }}" x-collapse>
                    <div class="px-4 pb-4">
                        <p class="text-slate-500 text-sm leading-relaxed">{{ $faq[1] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA Final --}}
<div class="max-w-3xl mx-auto px-4 mb-20">
    <div class="bg-slate-900 rounded-3xl p-10 text-center text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
        <h2 class="text-2xl sm:text-3xl font-black mb-3 relative z-10">Pronto para dominar o topo?</h2>
        <p class="text-slate-400 text-sm mb-8 max-w-md mx-auto relative z-10">Escolha o pacote ideal e comece a atrair mais membros para o seu grupo hoje mesmo.</p>
        <button onclick="window.scrollTo({top:0, behavior:'smooth'})"
                class="inline-flex items-center gap-2 bg-white text-slate-900 hover:bg-slate-100 font-black px-8 py-3.5 rounded-xl transition-all relative z-10">
            Ver Pacotes
            <x-heroicon-o-arrow-up class="w-4 h-4" />
        </button>
    </div>
</div>

@endsection
