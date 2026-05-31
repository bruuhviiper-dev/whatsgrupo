@extends('layouts.app')

@section('title', 'Perguntas Frequentes (FAQ) | WhatsGrupos')
@section('description', 'Tire suas dúvidas sobre o WhatsGrupos e saiba como usar a plataforma.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 mt-8">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-900 font-medium">Perguntas Frequentes (FAQ)</span>
    </nav>

    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm">
        <h1 class="text-3xl font-black text-slate-900 mb-6">Perguntas Frequentes</h1>
        
        <div class="space-y-6 mt-8">
            <div class="border border-slate-100 rounded-2xl p-6 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900 mb-2">1. Como faço para enviar um grupo?</h3>
                <p class="text-slate-600 text-sm">Basta clicar no botão "Enviar Grupo" no menu superior, colar o link de convite oficial do WhatsApp e escolher a categoria correta. Nosso sistema validará o link automaticamente.</p>
            </div>

            <div class="border border-slate-100 rounded-2xl p-6 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900 mb-2">2. É gratuito usar o site?</h3>
                <p class="text-slate-600 text-sm">Sim! É 100% gratuito tanto para encontrar e entrar em grupos quanto para cadastrar seus próprios grupos. Oferecemos opções VIP opcionais apenas para quem deseja destacar os grupos no topo.</p>
            </div>

            <div class="border border-slate-100 rounded-2xl p-6 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900 mb-2">3. Por que meu grupo foi removido?</h3>
                <p class="text-slate-600 text-sm">Grupos podem ser removidos se o link de convite for revogado/expirar, se a categoria estiver errada, se houver denúncias de usuários ou violação das nossas políticas (spam, pirataria, +18).</p>
            </div>

            <div class="border border-slate-100 rounded-2xl p-6 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900 mb-2">4. Como excluo um grupo que cadastrei?</h3>
                <p class="text-slate-600 text-sm">Acesse a aba "Meus Grupos". Se você estiver no mesmo navegador em que cadastrou, poderá editar ou excluir seu grupo. Caso contrário, utilize a busca por e-mail na aba "Meus Grupos".</p>
            </div>
        </div>
    </div>
</div>
@endsection
