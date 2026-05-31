@extends('layouts.app')

@section('title', 'Pagamento Confirmado! — WhatsGrupos')
@section('description', 'Seu pagamento foi confirmado. Verifique seu e-mail para receber o código de impulso VIP.')

@section('content')

<div class="max-w-lg mx-auto text-center py-12 px-4">

    {{-- Ícone animado de sucesso --}}
    <div class="relative w-24 h-24 mx-auto mb-8">
        <div class="absolute inset-0 rounded-full animate-ping opacity-15 bg-[#25D366]"></div>
        <div class="relative w-24 h-24 rounded-full flex items-center justify-center shadow-md bg-gradient-to-br from-[#25D366] to-[#1da851] text-white">
            <x-heroicon-s-check-circle class="w-14 h-14" />
        </div>
    </div>

    {{-- Título --}}
    <h1 class="text-3xl font-black text-slate-900 mb-3 tracking-tight">
        Pagamento Confirmado!
    </h1>

    @if (session('simulated') || request()->query('simulated'))
        <div class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 rounded-full px-4 py-1.5 text-amber-800 text-xs font-black uppercase tracking-wider mb-6 shadow-sm">
            <x-heroicon-s-beaker class="w-4 h-4 text-amber-600" /> Modo Simulação (Dev)
        </div>
    @endif

    {{-- Mensagem principal --}}
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 mb-6 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-[#25D366]"></div>
        
        <div class="w-12 h-12 bg-green-50 text-[#25D366] rounded-full flex items-center justify-center mx-auto mb-4">
            <x-heroicon-o-envelope class="w-6 h-6" />
        </div>
        
        <p class="text-slate-800 font-black text-lg mb-1">
            Código VIP enviado!
        </p>
        <p class="text-slate-500 text-sm mb-4">Enviamos as instruções de ativação para:</p>
        <p class="inline-block bg-green-50 border border-green-200 text-[#1da851] font-extrabold text-sm px-4 py-1.5 rounded-full mb-6">{{ $order->buyer_email }}</p>

        {{-- Código visível na tela também --}}
        @if ($order->boost_code)
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-4 shadow-inner relative">
                <p class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest mb-1.5">Seu código de ativação</p>
                <div class="flex items-center justify-center gap-3">
                    <p class="text-slate-900 text-3xl font-black font-mono tracking-widest select-all">{{ $order->boost_code }}</p>
                </div>
                <p class="text-slate-500 text-xs font-bold mt-2.5 flex items-center justify-center gap-1">
                    <x-heroicon-s-sparkles class="w-4 h-4 text-amber-500" />
                    <span>{{ $order->boosts_total }} impulso{{ $order->boosts_total > 1 ? 's' : '' }} VIP garantido{{ $order->boosts_total > 1 ? 's' : '' }}!</span>
                </p>
            </div>
        @endif

        <p class="text-slate-500 text-xs leading-relaxed font-medium">
            Verifique sua caixa de entrada e a pasta de <strong>spam</strong> caso não encontre o e-mail em alguns minutos.
        </p>
    </div>

    {{-- Próximos passos --}}
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 mb-8 text-left shadow-sm">
        <h2 class="text-slate-900 font-black mb-6 text-center text-lg flex items-center justify-center gap-2">
            <x-heroicon-s-clipboard-document-list class="w-5 h-5 text-[#25D366]" /> Próximos Passos para Ativar
        </h2>
        <ol class="space-y-4">
            @foreach ([
                'Copie o código de ativação acima ou o recebido por e-mail',
                'Acesse o menu "Meus Grupos" clicando no botão abaixo',
                'Informe o seu e-mail para carregar a lista de seus grupos',
                'Clique em "SUPER VIP ⭐" ao lado do grupo que deseja impulsionar',
                'Cole o código de 12 dígitos e confirme — seu grupo vai ao topo!',
            ] as $i => $step)
            <li class="flex items-start gap-3.5">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black shrink-0 mt-0.5 bg-green-50 text-[#25D366] border border-green-200">
                    {{ $i + 1 }}
                </span>
                <span class="text-slate-600 text-sm leading-relaxed font-semibold">{{ $step }}</span>
            </li>
            @endforeach
        </ol>
    </div>

    {{-- Botões de ação --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('my-groups') }}"
           id="btn-ir-meus-grupos"
           class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#25D366] to-[#1da851] hover:from-[#20bd5a] hover:to-[#179c47] text-white text-base font-extrabold px-8 py-4 rounded-full shadow-md transition-all">
            <x-heroicon-s-star class="w-5 h-5" /> Ir para Meus Grupos
        </a>
        <a href="{{ route('home') }}"
           class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 text-base font-bold transition-all shadow-sm">
            <x-heroicon-o-home class="w-5 h-5 text-slate-500" /> Voltar ao início
        </a>
    </div>
</div>

@endsection
