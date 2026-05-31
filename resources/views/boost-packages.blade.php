@extends('layouts.app')

@section('title', 'Pacotes Super VIP — Impulsione seu Grupo de WhatsApp | WhatsGrupos')
@section('description', 'Coloque seu grupo de WhatsApp no topo do diretório! Escolha seu pacote VIP e atraia mais membros com o Super Impulso do WhatsGrupos.')

@section('content')

{{-- ===== HERO ===== --}}
<div class="text-center mb-16 py-12 px-4 max-w-4xl mx-auto">
    <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-full px-5 py-2 text-amber-700 text-sm font-bold mb-8 shadow-sm">
        <x-heroicon-s-star class="w-5 h-5 text-amber-500" />
        Super VIP — Destaque Premium
    </div>
    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 mb-6 tracking-tight leading-tight">
        Coloque seu Grupo no
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600"> Topo!</span>
    </h1>
    <p class="text-slate-600 text-lg sm:text-xl max-w-2xl mx-auto mb-8 font-medium leading-relaxed">
        Seus grupos aparecem com borda dourada, selo VIP exclusivo e ficam nas primeiras posições por até 12 horas por cada impulso.
    </p>
    {{-- Contador de prova social --}}
    @if ($boostedThisMonth > 0)
    <div class="inline-flex items-center gap-2 bg-green-50 border border-green-200 rounded-full px-6 py-2.5 text-green-700 text-sm font-bold shadow-sm">
        <x-heroicon-s-rocket-launch class="w-5 h-5 text-green-500" />
        {{ number_format($boostedThisMonth) }} grupos impulsionados neste mês
    </div>
    @endif
</div>

{{-- ===== GRID DE PACOTES ===== --}}
<div class="max-w-[1400px] mx-auto px-4 mb-20">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 xl:gap-4 items-end">
        @foreach ($packages as $package)
        @php
            $name = strtolower($package->name);
            $theme = match(true) {
                str_contains($name, 'bronze') => ['border' => 'border-orange-900/20 hover:border-orange-900/40', 'bg' => 'bg-orange-900', 'text' => 'text-orange-900', 'button' => 'bg-orange-900 hover:bg-orange-800 text-white shadow-orange-900/20', 'icon' => 'text-orange-700', 'ribbon' => 'bg-gradient-to-r from-orange-700 to-orange-900 text-white', 'shadow' => 'shadow-orange-900/10 hover:shadow-orange-900/20'],
                str_contains($name, 'prata') => ['border' => 'border-slate-300 hover:border-slate-400', 'bg' => 'bg-slate-400', 'text' => 'text-slate-500', 'button' => 'bg-slate-500 hover:bg-slate-600 text-white shadow-slate-500/20', 'icon' => 'text-slate-400', 'ribbon' => 'bg-gradient-to-r from-slate-400 to-slate-500 text-white', 'shadow' => 'shadow-slate-400/10 hover:shadow-slate-400/20'],
                str_contains($name, 'ouro') => ['border' => 'border-amber-300', 'bg' => 'bg-amber-500', 'text' => 'text-amber-500', 'button' => 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/30', 'icon' => 'text-amber-500', 'ribbon' => 'bg-gradient-to-r from-amber-400 to-amber-600 text-white', 'shadow' => 'shadow-amber-500/30'],
                str_contains($name, 'diamante') => ['border' => 'border-cyan-300 hover:border-cyan-400', 'bg' => 'bg-cyan-500', 'text' => 'text-cyan-500', 'button' => 'bg-cyan-500 hover:bg-cyan-600 text-white shadow-cyan-500/20', 'icon' => 'text-cyan-500', 'ribbon' => 'bg-gradient-to-r from-cyan-400 to-cyan-600 text-white', 'shadow' => 'shadow-cyan-500/20 hover:shadow-cyan-500/30'],
                str_contains($name, 'estrela') => ['border' => 'border-purple-300 hover:border-purple-400', 'bg' => 'bg-purple-600', 'text' => 'text-purple-600', 'button' => 'bg-purple-600 hover:bg-purple-700 text-white shadow-purple-600/20', 'icon' => 'text-purple-500', 'ribbon' => 'bg-gradient-to-r from-purple-500 to-purple-700 text-white', 'shadow' => 'shadow-purple-600/20 hover:shadow-purple-600/30'],
                default => ['border' => 'border-slate-200 hover:border-slate-300', 'bg' => 'bg-slate-900', 'text' => 'text-slate-500', 'button' => 'bg-slate-900 hover:bg-slate-800 text-white shadow-slate-900/20', 'icon' => 'text-green-500', 'ribbon' => 'bg-slate-800 text-white', 'shadow' => 'shadow-slate-200/50 hover:shadow-slate-300/50']
            };
        @endphp
        <div class="relative flex flex-col rounded-3xl border bg-white transition-all duration-300 hover:-translate-y-2
                    {{ $package->is_popular
                        ? 'border-amber-300 shadow-2xl shadow-amber-500/20 scale-100 xl:scale-105 z-10'
                        : $theme['border'] . ' shadow-lg ' . $theme['shadow'] }}">
            
            {{-- Tarja Diagonal / Ribbon --}}
            <div class="absolute top-0 right-0 w-24 h-24 overflow-hidden rounded-tr-3xl z-0 pointer-events-none">
                <div class="absolute top-5 -right-7 w-32 transform rotate-45 {{ $theme['ribbon'] }} text-[8px] font-black py-1 text-center shadow-md uppercase tracking-widest">
                    {{ $package->name }}
                </div>
            </div>

            {{-- Badge popular --}}
            @if ($package->is_popular)
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-full text-center">
                    <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-black px-6 py-2.5 rounded-full uppercase tracking-widest shadow-lg shadow-amber-500/30 inline-flex items-center gap-1.5">
                        <x-heroicon-s-sparkles class="w-4 h-4" /> Mais Popular
                    </span>
                </div>
            @endif

            <div class="p-8 pb-6 flex flex-col h-full {{ $package->is_popular ? 'pt-10' : '' }}">
                {{-- Nome e impulsos --}}
                <div class="text-center mb-6 relative z-10">
                    <h3 class="{{ $theme['text'] }} text-sm font-bold uppercase tracking-widest mb-2 drop-shadow-sm">{{ $package->name }}</h3>
                    <div class="flex justify-center items-end gap-1 text-slate-900">
                        <span class="text-5xl font-black tracking-tight">{{ $package->boosts_count }}</span>
                    </div>
                    <p class="text-slate-500 text-sm font-medium mt-1">impulso{{ $package->boosts_count > 1 ? 's' : '' }}</p>
                </div>

                {{-- Preço --}}
                <div class="text-center mb-8 pb-8 border-b border-slate-100">
                    @if ($package->savings_percent > 0)
                        <div class="flex justify-center items-center gap-2 mb-1.5">
                            <span class="text-slate-400 text-sm line-through font-semibold">{{ $package->formatted_original_price }}</span>
                            <span class="bg-green-100 text-green-700 rounded-full px-2 py-0.5 text-xs font-bold">
                                {{ $package->discount_label }}
                            </span>
                        </div>
                    @else
                        <div class="h-6 mb-1.5"></div> {{-- Spacer to align grids --}}
                    @endif
                    <p class="text-3xl font-black text-slate-900">{{ $package->formatted_price }}</p>
                    <p class="text-slate-500 text-xs font-medium mt-1 uppercase tracking-wider">pagamento único</p>
                </div>

                {{-- Benefícios --}}
                <ul class="space-y-4 mb-8 flex-1">
                    <li class="flex items-start gap-3 text-sm text-slate-600 font-medium">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                        <span><strong class="text-slate-900">{{ $package->boosts_count }} uso{{ $package->boosts_count > 1 ? 's' : '' }}</strong> de impulso</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600 font-medium">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                        <span><strong>12h</strong> de destaque VIP por uso</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600 font-medium">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                        <span>Borda dourada em destaque</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600 font-medium">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                        <span>Badge VIP exclusivo</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600 font-medium">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                        <span>Código imediato no e-mail</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600 font-medium">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                        <span>PIX rápido ou Cartão</span>
                    </li>
                </ul>

                {{-- Botão --}}
                <a href="{{ route('boost.checkout', $package->slug) }}"
                   id="btn-comprar-{{ $package->slug }}"
                   class="w-full text-center py-4 px-6 rounded-xl font-bold transition-all duration-300 flex items-center justify-center gap-2 shadow-sm relative z-10 hover:-translate-y-0.5 hover:shadow-lg
                          {{ $package->is_popular 
                             ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/25' 
                             : $theme['button'] }}">
                    Comprar Agora 
                    <x-heroicon-o-arrow-right class="w-5 h-5" />
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ===== COMO FUNCIONA ===== --}}
<div class="bg-slate-50 border-y border-slate-200 py-20 mb-20">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-black text-slate-900 text-center mb-12 flex flex-col items-center gap-4">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center shadow-sm">
                <x-heroicon-o-rocket-launch class="w-8 h-8" />
            </div>
            Como Funciona o VIP
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <x-heroicon-o-shopping-cart class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-1">Passo 1</p>
                    <p class="text-slate-900 font-bold text-lg mb-2 leading-tight">Escolha o Pacote</p>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">Selecione a quantidade de impulsos. Pacotes maiores garantem descontos massivos!</p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <x-heroicon-o-credit-card class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-1">Passo 2</p>
                    <p class="text-slate-900 font-bold text-lg mb-2 leading-tight">Pagamento Seguro</p>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">Pague via PIX (aprovação em segundos) ou Cartão de Crédito pela Efí Bank.</p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <x-heroicon-o-envelope class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-1">Passo 3</p>
                    <p class="text-slate-900 font-bold text-lg mb-2 leading-tight">Receba o Código</p>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">Um código exclusivo de 12 dígitos será enviado automaticamente para o seu e-mail.</p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-1">Passo 4</p>
                    <p class="text-slate-900 font-bold text-lg mb-2 leading-tight">Acesse "Meus Grupos"</p>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">Na aba "Meus Grupos", digite seu e-mail para visualizar todos os grupos que você enviou.</p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <x-heroicon-o-bolt class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-1">Passo 5</p>
                    <p class="text-slate-900 font-bold text-lg mb-2 leading-tight">Aplique o Impulso</p>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">Clique no botão de VIP do seu grupo, cole o código de 12 dígitos e confirme.</p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-amber-300 shadow-md shadow-amber-500/10 flex gap-4 ring-2 ring-amber-100 ring-offset-2">
                <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <x-heroicon-o-trophy class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-amber-500 text-xs font-black tracking-widest uppercase mb-1">Passo 6</p>
                    <p class="text-slate-900 font-bold text-lg mb-2 leading-tight">Apareça no Topo!</p>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">Seu grupo pula para as primeiras posições de todo o site durante 12 horas inteiras!</p>
                </div>
            </div>
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
