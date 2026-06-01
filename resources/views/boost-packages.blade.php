@extends('layouts.app')

@section('title', 'Pacotes Super VIP — Impulsione seu Grupo de WhatsApp | WhatsGrupos')
@section('description', 'Coloque seu grupo de WhatsApp no topo do diretório! Escolha seu pacote VIP e atraia mais membros com o Super Impulso do WhatsGrupos.')

@section('content')

{{-- ===== HERO ===== --}}
<div class="relative py-16 px-4 max-w-6xl mx-auto mb-20">
    <!-- Background decorativo -->
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-100 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-orange-100 rounded-full opacity-20 blur-3xl"></div>
    </div>
    
    <div class="text-center">
        <div class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-full px-6 py-3 text-amber-700 text-sm font-bold mb-8 shadow-md">
            <x-heroicon-s-star class="w-5 h-5 text-amber-500 animate-pulse" />
            Super VIP — Destaque Premium
        </div>
        
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-slate-900 mb-6 tracking-tight leading-tight relative">
            Coloque seu Grupo
            <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 inline-block transform -skew-x-3">no Topo do Site!</span>
        </h1>
        
        <p class="text-slate-600 text-lg sm:text-xl max-w-3xl mx-auto mb-12 font-medium leading-relaxed">
            Apareça em <strong>posição fixa</strong> no topo, com <strong>borda dourada, selo VIP exclusivo</strong> e <strong>até 10x mais cliques</strong> nos seus grupos. Centenas de administradores já aumentaram seus membros.
        </p>
        
        {{-- Estatísticas de prova social --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-2xl mx-auto mb-12">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all">
                <div class="text-3xl font-black text-amber-600 mb-2">+2.5K</div>
                <p class="text-slate-600 text-sm font-semibold">Grupos Impulsionados</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all">
                <div class="text-3xl font-black text-green-600 mb-2">+150K</div>
                <p class="text-slate-600 text-sm font-semibold">Novos Membros Totais</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all">
                <div class="text-3xl font-black text-blue-600 mb-2">10x+</div>
                <p class="text-slate-600 text-sm font-semibold">Aumento Médio em Cliques</p>
            </div>
        </div>
        
        {{-- CTA Primário --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#packages" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-black text-lg px-10 py-4 rounded-xl shadow-lg shadow-amber-500/30 hover:shadow-xl transition-all transform hover:-translate-y-1 inline-flex items-center gap-2">
                <x-heroicon-s-rocket-launch class="w-6 h-6" />
                Ver Pacotes Agora
            </a>
            <button onclick="window.open('{{ route('home') }}', '_blank')" class="border-2 border-slate-300 hover:border-amber-500 text-slate-900 hover:text-amber-600 font-bold px-8 py-3.5 rounded-xl transition-all inline-flex items-center gap-2">
                Ver Exemplos de Sucesso
                <x-heroicon-o-arrow-right class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>

{{-- ===== GRID DE PACOTES ===== --}}
<div class="max-w-[1400px] mx-auto px-4 mb-24" id="packages">
    <div class="text-center mb-16">
        <span class="inline-block text-amber-600 font-black text-sm uppercase tracking-widest mb-3">Escolha o Melhor Pacote</span>
        <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-4">Invista no Crescimento do Seu Grupo</h2>
        <p class="text-slate-600 text-lg max-w-2xl mx-auto">Pacotes maiores = descontos maiores. Quanto mais impulsos, melhor o preço por unidade.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 xl:gap-4 items-end">
        @foreach ($packages as $package)
        @php
            $name = strtolower($package->name);
            $theme = match(true) {
                str_contains($name, 'bronze') => ['border' => 'border-orange-900/20 hover:border-orange-900/40', 'bg' => 'bg-orange-900', 'text' => 'text-orange-900', 'button' => 'bg-orange-900 hover:bg-orange-800 text-white shadow-orange-900/20', 'icon' => 'text-orange-700', 'ribbon' => 'bg-gradient-to-r from-orange-700 to-orange-900 text-white', 'shadow' => 'shadow-orange-900/10 hover:shadow-orange-900/20', 'light_bg' => 'bg-orange-50', 'badge' => 'bg-orange-100 text-orange-700'],
                str_contains($name, 'prata') => ['border' => 'border-slate-300 hover:border-slate-400', 'bg' => 'bg-slate-400', 'text' => 'text-slate-500', 'button' => 'bg-slate-500 hover:bg-slate-600 text-white shadow-slate-500/20', 'icon' => 'text-slate-400', 'ribbon' => 'bg-gradient-to-r from-slate-400 to-slate-500 text-white', 'shadow' => 'shadow-slate-400/10 hover:shadow-slate-400/20', 'light_bg' => 'bg-slate-50', 'badge' => 'bg-slate-100 text-slate-700'],
                str_contains($name, 'ouro') => ['border' => 'border-amber-300', 'bg' => 'bg-amber-500', 'text' => 'text-amber-500', 'button' => 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/30', 'icon' => 'text-amber-500', 'ribbon' => 'bg-gradient-to-r from-amber-400 to-amber-600 text-white', 'shadow' => 'shadow-amber-500/30', 'light_bg' => 'bg-amber-50', 'badge' => 'bg-amber-100 text-amber-700'],
                str_contains($name, 'diamante') => ['border' => 'border-cyan-300 hover:border-cyan-400', 'bg' => 'bg-cyan-500', 'text' => 'text-cyan-500', 'button' => 'bg-cyan-500 hover:bg-cyan-600 text-white shadow-cyan-500/20', 'icon' => 'text-cyan-500', 'ribbon' => 'bg-gradient-to-r from-cyan-400 to-cyan-600 text-white', 'shadow' => 'shadow-cyan-500/20 hover:shadow-cyan-500/30', 'light_bg' => 'bg-cyan-50', 'badge' => 'bg-cyan-100 text-cyan-700'],
                str_contains($name, 'estrela') => ['border' => 'border-purple-300 hover:border-purple-400', 'bg' => 'bg-purple-600', 'text' => 'text-purple-600', 'button' => 'bg-purple-600 hover:bg-purple-700 text-white shadow-purple-600/20', 'icon' => 'text-purple-500', 'ribbon' => 'bg-gradient-to-r from-purple-500 to-purple-700 text-white', 'shadow' => 'shadow-purple-600/20 hover:shadow-purple-600/30', 'light_bg' => 'bg-purple-50', 'badge' => 'bg-purple-100 text-purple-700'],
                default => ['border' => 'border-slate-200 hover:border-slate-300', 'bg' => 'bg-slate-900', 'text' => 'text-slate-500', 'button' => 'bg-slate-900 hover:bg-slate-800 text-white shadow-slate-900/20', 'icon' => 'text-green-500', 'ribbon' => 'bg-slate-800 text-white', 'shadow' => 'shadow-slate-200/50 hover:shadow-slate-300/50', 'light_bg' => 'bg-slate-50', 'badge' => 'bg-slate-100 text-slate-700']
            };
        @endphp
        <div class="relative flex flex-col rounded-2xl border bg-white transition-all duration-300 hover:-translate-y-2 group
                    {{ $package->is_popular
                        ? 'border-amber-400 shadow-2xl shadow-amber-500/30 scale-100 xl:scale-105 z-10 ring-2 ring-amber-100'
                        : $theme['border'] . ' shadow-md ' . $theme['shadow'] }}">
            
            {{-- Tarja Diagonal --}}
            <div class="absolute top-0 right-0 w-20 h-20 overflow-hidden rounded-tl-2xl z-0 pointer-events-none">
                <div class="absolute top-3 -right-6 w-28 transform rotate-45 {{ $theme['ribbon'] }} text-[7px] font-black py-0.5 text-center shadow-md uppercase tracking-wider">
                    {{ $package->name }}
                </div>
            </div>

            {{-- Badge popular com animação --}}
            @if ($package->is_popular)
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-20">
                    <span class="bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 text-white text-xs font-black px-5 py-2 rounded-full uppercase tracking-widest shadow-lg shadow-amber-500/40 inline-flex items-center gap-1.5 animate-pulse">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        🔥 MAIS POPULAR
                    </span>
                </div>
            @endif

            <div class="p-6 pb-5 flex flex-col h-full {{ $package->is_popular ? 'pt-8' : '' }}">
                {{-- Nome --}}
                <div class="text-center mb-4 relative z-10">
                    <div class="{{ $theme['light_bg'] }} {{ $theme['badge'] }} w-fit mx-auto px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider mb-2">
                        {{ $package->name }}
                    </div>
                </div>

                {{-- Impulsos --}}
                <div class="text-center mb-4">
                    <div class="flex justify-center items-baseline gap-0.5">
                        <span class="text-4xl font-black tracking-tight text-slate-900">{{ $package->boosts_count }}</span>
                        <span class="text-slate-500 text-xs font-bold">{{ $package->boosts_count > 1 ? 'impulsos' : 'impulso' }}</span>
                    </div>
                </div>

                {{-- Preço --}}
                <div class="text-center mb-5 pb-5 border-b border-slate-100">
                    @if ($package->savings_percent > 0)
                        <div class="flex justify-center items-center gap-1.5 mb-1">
                            <span class="text-slate-400 text-xs line-through font-bold">{{ $package->formatted_original_price }}</span>
                            <span class="bg-green-500 text-white rounded-full px-2 py-0.5 text-xs font-black">
                                {{ $package->discount_label }}
                            </span>
                        </div>
                    @endif
                    <p class="text-3xl font-black text-slate-900">{{ $package->formatted_price }}</p>
                </div>

                {{-- Benefícios compactos --}}
                <ul class="space-y-2 mb-5 flex-1 text-xs">
                    <li class="flex items-center gap-2 text-slate-600 font-medium">
                        <div class="w-4 h-4 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-2.5 h-2.5 text-green-600"><path d="M20.293 4.293l-10.293 10.293-4.293-4.293a1 1 0 0 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l11-11a1 1 0 1 0-1.414-1.414z"/></svg>
                        </div>
                        <span><strong>{{ $package->boosts_count }}x</strong> no topo (12h cada)</span>
                    </li>
                    <li class="flex items-center gap-2 text-slate-600 font-medium">
                        <div class="w-4 h-4 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-2.5 h-2.5 text-green-600"><path d="M20.293 4.293l-10.293 10.293-4.293-4.293a1 1 0 0 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l11-11a1 1 0 1 0-1.414-1.414z"/></svg>
                        </div>
                        <span>Borda dourada + badge ⭐</span>
                    </li>
                    <li class="flex items-center gap-2 text-slate-600 font-medium">
                        <div class="w-4 h-4 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-2.5 h-2.5 text-green-600"><path d="M20.293 4.293l-10.293 10.293-4.293-4.293a1 1 0 0 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l11-11a1 1 0 1 0-1.414-1.414z"/></svg>
                        </div>
                        <span>~<strong>10x</strong> mais cliques</span>
                    </li>
                    <li class="flex items-center gap-2 text-slate-600 font-medium">
                        <div class="w-4 h-4 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-2.5 h-2.5 text-green-600"><path d="M20.293 4.293l-10.293 10.293-4.293-4.293a1 1 0 0 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l11-11a1 1 0 1 0-1.414-1.414z"/></svg>
                        </div>
                        <span>Sem prazo de validade</span>
                    </li>
                </ul>

                {{-- Botão --}}
                <a href="{{ route('boost.checkout', $package->slug) }}"
                   class="w-full text-center py-3 px-4 rounded-lg font-bold transition-all duration-300 flex items-center justify-center gap-2 shadow-sm relative z-10 hover:-translate-y-0.5 hover:shadow-md text-sm group/btn
                          {{ $package->is_popular 
                             ? 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white' 
                             : $theme['button'] }}">
                    Comprar 
                    <x-heroicon-o-arrow-right class="w-4 h-4 group-hover/btn:translate-x-0.5 transition-transform" />
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ===== COMO FUNCIONA ===== --}}
<div class="bg-gradient-to-b from-slate-50 via-slate-50 to-white border-y border-slate-200 py-24 mb-24 relative overflow-hidden">
    <div class="absolute inset-0 -z-10">
        <div class="absolute top-10 right-10 w-80 h-80 bg-blue-100 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-80 h-80 bg-amber-100 rounded-full opacity-20 blur-3xl"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-4xl font-black text-slate-900 text-center mb-4 flex flex-col items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 text-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                <x-heroicon-o-rocket-launch class="w-8 h-8" />
            </div>
            Como Funciona o VIP em 6 Passos
        </h2>
        <p class="text-center text-slate-600 text-lg mb-16 max-w-2xl mx-auto">Rápido, seguro e transparente. Do pagamento até aparecer no topo leva menos de 5 minutos!</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach ([
                ['🛒', 'Escolha o Pacote', 'Selecione a quantidade de impulsos que melhor se adapta ao seu orçamento.', '1'],
                ['💳', 'Pagamento Seguro', 'Pague via PIX (2 segundos) ou Cartão com total segurança e garantia.', '2'],
                ['📨', 'Receba o Código', 'Seu código único de 12 dígitos chega no seu e-mail em segundos.', '3'],
                ['👤', 'Acesse Meus Grupos', 'Faça login em "Meus Grupos" com o e-mail que você usou na compra.', '4'],
                ['⚡', 'Aplique o Impulso', 'Cole o código no botão VIP do seu grupo e clique em confirmar.', '5'],
                ['🏆', 'Apareça no Topo!', 'Seu grupo salta para o topo com borda dourada e badge VIP exclusivo!', '6'],
            ] as $step)
            <div class="group relative">
                <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 h-full">
                    <div class="absolute -top-4 -left-4 w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 text-white rounded-xl flex items-center justify-center font-black text-lg shadow-lg">
                        {{ $step[3] }}
                    </div>
                    <div class="text-3xl mb-3">{{ $step[0] }}</div>
                    <p class="text-slate-900 font-bold text-lg mb-2">{{ $step[1] }}</p>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">{{ $step[2] }}</p>
                </div>
                @if ($loop->index < 5)
                    <div class="hidden lg:flex absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-1 bg-gradient-to-r from-amber-300 to-orange-300"></div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Timeline visual --}}
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-8 text-center">
            <div class="flex items-center justify-center gap-4 flex-wrap mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold">✓</div>
                    <span class="text-slate-700 font-bold">Pagamento instantâneo</span>
                </div>
                <span class="text-slate-400">→</span>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold">✓</div>
                    <span class="text-slate-700 font-bold">Código no e-mail (segundos)</span>
                </div>
                <span class="text-slate-400">→</span>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold">✓</div>
                    <span class="text-slate-700 font-bold">Apareça no topo (em minutos)</span>
                </div>
            </div>
            <p class="text-amber-900 font-bold">⏱️ Tudo acontece em menos de 5 minutos!</p>
        </div>
    </div>
</div>

{{-- ===== DEPOIMENTOS E FAQ LADO A LADO ===== --}}
<div class="max-w-[1400px] mx-auto px-4 mb-20 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8">
    
    {{-- Depoimentos --}}
    <div>
        <h2 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center">
                <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6" />
            </div>
            O que dizem os administradores
        </h2>
        
        <div class="space-y-4">
            @foreach ([
                ['Carlos M.', 'Administrador de grupo de games', 'Comprei o pacote Ouro e em menos de 1 hora meu grupo saiu de 50 para mais de 200 membros! Vale muito a pena!'],
                ['Ana P.', 'Dona de loja virtual', 'Uso o WhatsGrupos para divulgar meu grupo de ofertas. O VIP é incrível, apareço sempre em primeiro!'],
                ['Pedro H.', 'Youtuber e criador de conteúdo', 'Compro o pacote Diamante todo mês. O retorno em visualizações e membros é impressionante. Recomendo fortemente!'],
            ] as $t)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex gap-1 mb-3">
                    <x-heroicon-s-star class="w-4 h-4 text-amber-400" />
                    <x-heroicon-s-star class="w-4 h-4 text-amber-400" />
                    <x-heroicon-s-star class="w-4 h-4 text-amber-400" />
                    <x-heroicon-s-star class="w-4 h-4 text-amber-400" />
                    <x-heroicon-s-star class="w-4 h-4 text-amber-400" />
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-4 italic font-medium">"{{ $t[2] }}"</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold uppercase border border-slate-200">
                        {{ substr($t[0], 0, 1) }}
                    </div>
                    <div>
                        <p class="text-slate-900 font-bold text-sm">{{ $t[0] }}</p>
                        <p class="text-slate-500 text-xs font-semibold">{{ $t[1] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- FAQ --}}
    <div>
        <h2 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                <x-heroicon-o-question-mark-circle class="w-6 h-6" />
            </div>
            Perguntas Frequentes
        </h2>
        
        <div class="space-y-3" x-data="{ open: 0 }">
            @foreach ([
                ['Por quanto tempo meu grupo fica em destaque?', 'Cada impulso deixa seu grupo destacado por 12 horas no topo da página inicial e da categoria correspondente, com borda dourada e badge VIP.'],
                ['Posso usar os impulsos em vários grupos?', 'Sim! Se você for dono de vários grupos aprovados, pode distribuir os impulsos entre eles usando o mesmo código VIP livremente.'],
                ['Meu grupo precisa já estar aprovado?', 'Sim, apenas grupos com status "Aprovado" no site podem receber impulsos. Se o seu acabou de ser enviado, aguarde a aprovação.'],
                ['O que acontece quando as 12 horas acabam?', 'O seu grupo volta ao posicionamento padrão na lista. Você pode aplicar outro impulso na hora que quiser para voltar ao topo!'],
                ['Os impulsos do pacote expiram?', 'Não. Os impulsos adquiridos no código não possuem prazo de validade. Você compra hoje e usa só quando achar necessário.'],
            ] as $i => $faq)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition-all">
                <button type="button"
                        class="w-full flex items-center justify-between gap-4 p-5 text-left focus:outline-none hover:bg-slate-50 transition-colors"
                        @click="open = open === {{ $i }} ? null : {{ $i }}">
                    <span class="text-slate-900 font-bold text-sm">{{ $faq[0] }}</span>
                    <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform duration-300" x-bind:class="{ 'rotate-180': open === {{ $i }} }" />
                </button>
                <div x-show="open === {{ $i }}" x-collapse>
                    <div class="px-5 pb-5 pt-1">
                        <p class="text-slate-600 text-sm leading-relaxed font-medium">{{ $faq[1] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- CTA Final --}}
<div class="max-w-4xl mx-auto px-4 mb-20">
    <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-3xl p-10 text-center shadow-xl shadow-amber-500/20 text-white relative overflow-hidden">
        {{-- Efeito visual de brilho no fundo --}}
        <div class="absolute top-0 left-0 w-full h-full opacity-10" style="background-image: radial-gradient(circle at top right, white, transparent 70%);"></div>
        
        <div class="bg-white/20 p-4 rounded-full mb-6 inline-flex backdrop-blur-md border border-white/30 shadow-lg">
            <x-heroicon-s-rocket-launch class="w-10 h-10 text-white" />
        </div>
        <h2 class="text-3xl sm:text-4xl font-black mb-4 tracking-tight drop-shadow-md">Pronto para dominar o topo?</h2>
        <p class="text-amber-50 text-lg mb-8 max-w-xl mx-auto font-medium drop-shadow-sm">
            Milhares de administradores já multiplicaram o tamanho dos seus grupos usando o recurso VIP. Escolha o melhor pacote para você agora mesmo.
        </p>
        <button onclick="window.scrollTo({top:0, behavior:'smooth'})" class="bg-white text-amber-600 hover:text-amber-700 hover:scale-105 transition-all text-lg px-10 py-4 font-black rounded-xl shadow-lg hover:shadow-xl inline-flex items-center gap-2">
            Ver Pacotes VIP
            <x-heroicon-o-arrow-up class="w-5 h-5" />
        </button>
    </div>
</div>

@endsection
