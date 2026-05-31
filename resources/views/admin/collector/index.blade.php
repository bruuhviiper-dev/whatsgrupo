@extends('admin.layout')

@section('title', 'Coletor Automático & Validador')
@section('page-title', 'Coletor Automático & Verificador')

@section('content')

{{-- Grid de Estatísticas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Card 1 --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Coletado por Bot</p>
        <h3 class="text-3xl font-black text-slate-900">{{ number_format($stats['bot_groups']) }}</h3>
        <p class="text-xs font-medium text-slate-500 mt-2">Grupos importados via DuckDuckGo</p>
    </div>

    {{-- Card 2 --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-50 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Mortos esta semana</p>
        <h3 class="text-3xl font-black text-red-500">{{ number_format($stats['deactivated_week']) }}</h3>
        <p class="text-xs font-medium text-slate-500 mt-2">Links inativados na faxina</p>
    </div>

    {{-- Card 3 --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-16 h-16 bg-[#25D366]/10 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-[#25D366]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Aprovados Ativos</p>
        <h3 class="text-3xl font-black text-[#25D366]">{{ number_format($stats['approved_total']) }}</h3>
        <p class="text-xs font-medium text-slate-500 mt-2">Total de links listados</p>
    </div>

    {{-- Card 4 --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Fila de Moderação</p>
        <h3 class="text-3xl font-black text-amber-500">{{ number_format($stats['pending_moderation']) }}</h3>
        <p class="text-xs font-medium text-slate-500 mt-2">Aguardando revisão manual</p>
    </div>
</div>

{{-- Painel de Ações do Bot --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    {{-- Bloco Coletor --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            Coletor Automático (Scraper)
        </h3>
        <p class="text-slate-600 text-sm font-medium leading-relaxed">
            Busca dinamicamente por novos links de grupos de WhatsApp indexados no DuckDuckGo para todas as 40 categorias. Eles entram como <strong>pendentes</strong> para sua revisão.
        </p>
        <form action="{{ route('admin.collector.run') }}" method="POST" class="pt-2">
            @csrf
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm py-3.5 px-4 rounded-xl transition-all shadow-sm shadow-blue-500/20 inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                Executar Coleta de Novos Grupos
            </button>
        </form>
    </div>

    {{-- Bloco Validador/Limpeza --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            Validador de Links Mortos
        </h3>
        <p class="text-slate-600 text-sm font-medium leading-relaxed">
            Examina todos os grupos aprovados. O script analisa a saúde do link no WhatsApp. Links inativos são marcados como <strong>rejeitados</strong> (link_expirado).
        </p>
        <form action="{{ route('admin.collector.check-links') }}" method="POST" class="pt-2">
            @csrf
            <button type="submit" 
                    class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm py-3.5 px-4 rounded-xl transition-all shadow-sm shadow-emerald-500/20 inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                Executar Faxina de Links Mortos
            </button>
        </form>
    </div>
</div>

{{-- Tabela de Histórico de Execuções --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            Histórico de Execuções
        </h3>
    </div>
    
    @if ($logs->isEmpty())
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <p class="text-slate-600 font-medium">Nenhum log de execução registrado ainda.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="admin-table w-full text-left">
                <thead>
                    <tr class="bg-white">
                        <th class="py-4 px-6 font-bold text-xs text-slate-400 uppercase tracking-wider border-b border-slate-100">Tarefa / Bot</th>
                        <th class="py-4 px-6 font-bold text-xs text-slate-400 uppercase tracking-wider border-b border-slate-100">Status</th>
                        <th class="py-4 px-6 font-bold text-xs text-slate-400 uppercase tracking-wider border-b border-slate-100">Resultado / Resumo</th>
                        <th class="py-4 px-6 font-bold text-xs text-slate-400 uppercase tracking-wider border-b border-slate-100">Executado Em</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap text-sm">
                                @if ($log->job_type === 'collect_groups')
                                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg> Coletor</span>
                                @elseif ($log->job_type === 'check_links')
                                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg> Limpeza</span>
                                @else
                                    {{ $log->job_type }}
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if ($log->status === 'success')
                                    <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 font-bold px-2.5 py-1 rounded-md text-xs border border-green-200">Sucesso</span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 font-bold px-2.5 py-1 rounded-md text-xs border border-red-200">Falha</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-slate-600 font-medium">
                                {{ $log->result_summary }}
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap text-sm text-slate-500 font-medium">
                                {{ $log->executed_at->format('d/m/Y H:i:s') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
