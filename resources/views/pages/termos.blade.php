@extends('layouts.app')

@section('title', 'Termos de Uso | WhatsGrupos')
@section('description', 'Termos de Uso e Condições do WhatsGrupos.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 mt-8">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-900 font-medium">Termos de Uso</span>
    </nav>

    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm">
        <h1 class="text-3xl font-black text-slate-900 mb-6">Termos de Uso</h1>
        <p class="text-slate-500 text-sm mb-8">Última atualização: {{ date('d/m/Y') }}</p>

        <div class="prose prose-slate max-w-none prose-p:text-sm prose-li:text-sm">
            <p>Bem-vindo ao <strong>WhatsGrupos</strong>. Ao acessar e usar nossa plataforma, você concorda em cumprir e ficar vinculado aos seguintes termos de uso.</p>
            
            <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">1. Aceitação dos Termos</h3>
            <p>O WhatsGrupos é um diretório e indexador de links públicos de grupos de WhatsApp. Nós não hospedamos mensagens, fotos, vídeos ou qualquer mídia enviada nos grupos. Ao utilizar o site, você aceita que a responsabilidade sobre o conteúdo dos grupos é exclusivamente dos seus administradores.</p>
            
            <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">2. Regras de Utilização</h3>
            <p>Você concorda em NÃO utilizar o site para:</p>
            <ul class="list-disc pl-5 space-y-2 mt-4 text-slate-600">
                <li>Divulgar links falsos ou enganosos;</li>
                <li>Divulgar conteúdo adulto, ilícito, drogas ou pirataria;</li>
                <li>Fazer spam submetendo o mesmo grupo repetidas vezes.</li>
            </ul>

            <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">3. Moderação</h3>
            <p>O WhatsGrupos reserva-se o direito de remover qualquer grupo cadastrado na plataforma sem aviso prévio, caso identifiquemos violação de nossas diretrizes ou comportamento malicioso.</p>

            <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">4. Isenção de Responsabilidade</h3>
            <p>O uso dos links e a interação nos grupos listados são de total risco do usuário. Não nos responsabilizamos por perdas, danos ou ações que ocorram dentro do aplicativo WhatsApp após o clique em nossa plataforma.</p>
        </div>
    </div>
</div>
@endsection
