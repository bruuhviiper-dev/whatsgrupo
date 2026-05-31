@extends('layouts.app')

@section('title', 'Contato | WhatsGrupos')
@section('description', 'Entre em contato com a equipe do WhatsGrupos.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 mt-8">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Início</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-900 font-medium">Contato</span>
    </nav>

    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm text-center">
        <div class="w-20 h-20 bg-green-100 text-[#25D366] rounded-full flex items-center justify-center mx-auto mb-6">
            <x-heroicon-o-envelope class="w-10 h-10" />
        </div>
        
        <h1 class="text-3xl font-black text-slate-900 mb-4">Fale Conosco</h1>
        <p class="text-slate-600 max-w-lg mx-auto mb-8">
            Tem alguma dúvida, sugestão ou deseja falar sobre parcerias? Entre em contato através do nosso e-mail oficial. Responderemos o mais breve possível!
        </p>

        <a href="mailto:contato@whatsgrupos.com.br" class="inline-flex items-center justify-center gap-3 bg-[#25D366] hover:bg-green-600 text-white font-bold py-4 px-8 rounded-xl transition-all hover:-translate-y-1 hover:shadow-lg shadow-green-500/30">
            <x-heroicon-s-paper-airplane class="w-5 h-5" />
            contato@whatsgrupos.com.br
        </a>
    </div>
</div>
@endsection
