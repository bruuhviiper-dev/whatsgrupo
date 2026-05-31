@extends('layouts.app')

@section('title', 'Política de Privacidade | WhatsGrupos')
@section('description', 'Política de Privacidade do WhatsGrupos.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 mt-8">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-900 font-medium">Política de Privacidade</span>
    </nav>

    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm">
        <h1 class="text-3xl font-black text-slate-900 mb-6">Política de Privacidade</h1>
        <p class="text-slate-500 text-sm mb-8">Última atualização: {{ date('d/m/Y') }}</p>

        <div class="prose prose-slate max-w-none prose-p:text-sm prose-li:text-sm">
            <p>Sua privacidade é importante para nós. Esta política explica como lidamos com a coleta, uso e proteção de suas informações ao utilizar o WhatsGrupos.</p>
            
            <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">1. Informações que Coletamos</h3>
            <p>Quando você envia um grupo em nosso site, nós coletamos o link de convite fornecido e armazenamos no banco de dados. Nós também utilizamos cookies básicos e ferramentas de Analytics para entender o tráfego e melhorar a experiência.</p>
            
            <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">2. Como Usamos a Informação</h3>
            <p>As informações são usadas exclusivamente para:</p>
            <ul class="list-disc pl-5 space-y-2 mt-4 text-slate-600">
                <li>Exibir o grupo listado em nossa plataforma de forma pública;</li>
                <li>Monitorar métricas de visitas;</li>
                <li>Garantir a segurança e prevenir spam ou fraudes no site.</li>
            </ul>

            <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">3. Compartilhamento de Dados</h3>
            <p>Não vendemos, alugamos ou comercializamos seus dados pessoais ou as informações de seu grupo para terceiros, exceto no que diz respeito à exibição pública no próprio site para fins de divulgação, conforme proposto pela plataforma.</p>

            <h3 class="text-lg font-bold text-slate-800 mt-8 mb-4">4. Cookies</h3>
            <p>Usamos cookies para armazenar preferências, como sessões de login (caso aplicável) e dados do Google Analytics. Você pode desabilitar os cookies diretamente nas configurações de seu navegador.</p>
        </div>
    </div>
</div>
@endsection
