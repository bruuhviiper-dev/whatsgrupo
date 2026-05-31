@extends('layouts.app')

@section('title', 'Anuncie Conosco | Divulgue no WhatsGrupos')
@section('description', 'Conquiste milhares de membros para seu grupo ou negócio! Conheça nossos pacotes Super VIP, Banners de Categoria Patrocinada e Selo de Grupo Verificado.')

@section('content')
<div class="space-y-10">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-text-muted flex-wrap" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-secondary transition-colors">Início</a>
        <span class="text-white/20">›</span>
        <span class="text-text-main font-medium">Anuncie Conosco</span>
    </nav>

    {{-- Header / Hero --}}
    <div class="rounded-2xl p-8 border border-white/5 relative overflow-hidden" style="background: linear-gradient(135deg, rgba(108,63,197,0.15), rgba(0,200,150,0.05));">
        <div class="relative z-10 max-w-3xl">
            <h1 class="text-3xl sm:text-4xl font-black text-text-main mb-3">🚀 Alcance Milhares de Pessoas Diariamente</h1>
            <p class="text-text-muted text-sm sm:text-base leading-relaxed">
                O <strong class="text-secondary">WhatsGrupos</strong> é o maior e mais ativo diretório de grupos de WhatsApp do Brasil. Se você quer atrair novos membros de forma imediata para o seu grupo ou promover sua marca/produto para um público extremamente engajado, nós temos a solução ideal!
            </p>
        </div>
    </div>

    {{-- Portfólio de Soluções --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Solução 1: Super VIP --}}
        <div class="bg-card rounded-2xl border border-white/5 p-6 flex flex-col justify-between" style="background:#1A1A2E;">
            <div class="space-y-4">
                <div class="text-4xl">⭐</div>
                <h3 class="text-text-main font-black text-lg">Super VIP</h3>
                <p class="text-text-muted text-xs leading-relaxed">
                    Coloque seu grupo no topo absoluto do diretório principal e de todas as categorias. Seus grupos ganham destaque visual exclusivo com bordas douradas pulsantes.
                </p>
                <ul class="text-text-muted text-[11px] space-y-1">
                    <li>✅ Destaque rotativo imediato</li>
                    <li>✅ Efeito visual ouro pulsante</li>
                    <li>✅ Mais de 10x mais cliques de entrada</li>
                </ul>
            </div>
            <a href="{{ route('boost.packages') }}" class="btn-vip w-full text-center mt-6 text-xs sm:text-sm">
                Comprar VIP Direto 🚀
            </a>
        </div>

        {{-- Solução 2: Categoria Patrocinada --}}
        <div class="bg-card rounded-2xl border border-white/5 p-6 flex flex-col justify-between" style="background:#1A1A2E;">
            <div class="space-y-4">
                <div class="text-4xl">📢</div>
                <h3 class="text-text-main font-black text-lg">Categoria Patrocinada</h3>
                <p class="text-text-muted text-xs leading-relaxed">
                    Fixe um banner publicitário horizontal e exclusivo no topo de uma categoria específica (ex: Namoro, Games, Investimentos). Segmentação perfeita para seu produto.
                </p>
                <ul class="text-text-muted text-[11px] space-y-1">
                    <li>✅ Banner horizontal em destaque</li>
                    <li>✅ Tráfego 100% qualificado</li>
                    <li>✅ Banner com link direto para seu site</li>
                </ul>
            </div>
            <a href="#contato-form" class="btn-primary w-full text-center mt-6 text-xs sm:text-sm">
                Solicitar Orçamento 💬
            </a>
        </div>

        {{-- Solução 3: Selo Verificado --}}
        <div class="bg-card rounded-2xl border border-white/5 p-6 flex flex-col justify-between" style="background:#1A1A2E;">
            <div class="space-y-4">
                <div class="text-4xl">✓</div>
                <h3 class="text-text-main font-black text-lg">Grupo Verificado</h3>
                <p class="text-text-muted text-xs leading-relaxed">
                    Adquira o selo oficial azul de verificação ao lado do nome do seu grupo. Transmita total segurança aos novos membros, aumentando significativamente as taxas de entrada.
                </p>
                <ul class="text-text-muted text-[11px] space-y-1">
                    <li>✅ Selo oficial de verificação azul</li>
                    <li>✅ Aumenta em até 70% a credibilidade</li>
                    <li>✅ Perfeito para grupos de nichos sérios</li>
                </ul>
            </div>
            <a href="#contato-form" class="btn-primary w-full text-center mt-6 text-xs sm:text-sm">
                Assinar Verificação 💳
            </a>
        </div>
    </div>

    {{-- Formulário de Contato Comercial --}}
    <div id="contato-form" class="bg-card rounded-2xl border border-white/5 p-6 md:p-8 space-y-6" style="background:#1A1A2E;">
        <div>
            <h2 class="text-xl font-black text-text-main flex items-center gap-2">
                <span>✉️</span> Fale com Nosso Comercial e Suporte
            </h2>
            <p class="text-text-muted text-xs mt-1">
                Deseja fechar um banner de patrocínio, obter o selo azul de verificação para o seu grupo ou possui alguma dúvida ou denúncia? Preencha o formulário e responderemos rapidamente.
            </p>
        </div>

        <form action="{{ route('advertise.submit') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Nome --}}
                <div class="space-y-1.5">
                    <label class="text-text-muted font-bold text-xs uppercase tracking-wider block">Seu Nome</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ex: Roberto Silva"
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-text-main placeholder-text-muted focus:outline-none focus:border-secondary transition-colors">
                </div>

                {{-- E-mail --}}
                <div class="space-y-1.5">
                    <label class="text-text-muted font-bold text-xs uppercase tracking-wider block">Seu E-mail de Contato</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="Ex: comercial@exemplo.com"
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-text-main placeholder-text-muted focus:outline-none focus:border-secondary transition-colors">
                </div>

                {{-- Assunto --}}
                <div class="space-y-1.5">
                    <label class="text-text-muted font-bold text-xs uppercase tracking-wider block">Assunto do Contato</label>
                    <select name="subject" required
                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-text-main focus:outline-none focus:border-secondary transition-colors">
                        <option value="">Selecione o assunto...</option>
                        <option value="Publicidade" {{ old('subject') == 'Publicidade' ? 'selected' : '' }}>📢 Banners & Publicidade</option>
                        <option value="Suporte" {{ old('subject') == 'Suporte' ? 'selected' : '' }}>💳 Assinatura de Selo Azul (Verificado)</option>
                        <option value="Dúvida" {{ old('subject') == 'Dúvida' ? 'selected' : '' }}>❓ Dúvidas Gerais</option>
                        <option value="Denúncia" {{ old('subject') == 'Denúncia' ? 'selected' : '' }}>🚨 Denúncias de Grupos</option>
                        <option value="Outros" {{ old('subject') == 'Outros' ? 'selected' : '' }}>📦 Outros Assuntos</option>
                    </select>
                </div>
            </div>

            {{-- Mensagem --}}
            <div class="space-y-1.5">
                <label class="text-text-muted font-bold text-xs uppercase tracking-wider block">Escreva sua Mensagem / Detalhes do Pedido</label>
                <textarea name="message" rows="5" required placeholder="Forneça o máximo de detalhes possível. Se for publicidade, informe a categoria de interesse."
                          class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-text-main placeholder-text-muted focus:outline-none focus:border-secondary transition-colors">{{ old('message') }}</textarea>
            </div>

            <button type="submit"
                    class="bg-gradient-to-r from-secondary to-green-600 hover:from-secondary-hover hover:to-green-500 text-black font-black w-full py-4 rounded-xl text-sm transition-all shadow-lg shadow-green-600/10">
                📩 Enviar Mensagem Agora
            </button>
        </form>
    </div>
</div>
@endsection
