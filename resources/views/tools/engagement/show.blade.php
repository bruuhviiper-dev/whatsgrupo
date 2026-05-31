@extends('layouts.analyzer')

@section('navbar_color', 'bg-[#16a34a]')

@section('title', "Análise do Grupo {$analysis->group_name} | WhatsGrupos")
@section('description', $analysis->public_summary)

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8 relative" x-data="{ copied: false }">
    
    <div class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600 shadow-sm shrink-0">
                <x-heroicon-s-chart-pie class="w-6 h-6" />
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Relatório de Engajamento</p>
                <h1 class="text-2xl font-black text-slate-900 leading-tight truncate max-w-[250px] sm:max-w-md" title="{{ $analysis->group_name }}">{{ $analysis->group_name }}</h1>
            </div>
        </div>
        
        <span class="px-4 py-1.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-full border border-slate-200 uppercase tracking-widest shrink-0">
            Categoria: {{ $analysis->category }}
        </span>
    </div>

    <!-- Resumo Principal -->
    <div class="bg-gradient-to-br from-green-500 to-[#128C7E] rounded-3xl p-6 sm:p-8 text-white shadow-lg shadow-green-500/20 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -z-0"></div>
        <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center md:items-stretch">
            
            <!-- Círculo de Nota -->
            <div class="flex flex-col items-center justify-center bg-white/10 rounded-2xl p-6 border border-white/20 w-full md:w-1/3 backdrop-blur-md">
                <p class="text-white/80 text-xs font-bold uppercase tracking-widest mb-2">❤️ Saúde do Grupo</p>
                
                @php
                    $emoji = '💀';
                    if($analysis->health_score >= 10.0) $emoji = '🚀';
                    elseif($analysis->health_score >= 8.0) $emoji = '🔥';
                    elseif($analysis->health_score >= 6.0) $emoji = '😊';
                    elseif($analysis->health_score >= 4.0) $emoji = '😴';
                @endphp

                <div class="flex items-end gap-2 mb-1" 
                     x-data="{ score: 0 }" 
                     x-init="
                       let target = {{ $analysis->health_score }};
                       let step = target / 30;
                       let interval = setInterval(() => {
                         score = Math.min(parseFloat((score + step).toFixed(1)), target);
                         if (score >= target) clearInterval(interval);
                       }, 40);
                     ">
                    <span x-text="score.toFixed(1)" class="text-6xl font-black leading-none">0.0</span>
                    <span class="text-xl font-bold text-white/50 mb-1">/10 {{ $emoji }}</span>
                </div>
                
                @php
                    $healthColor = 'bg-red-400';
                    if($analysis->health_score >= 8.0) $healthColor = 'bg-green-300';
                    elseif($analysis->health_score >= 6.0) $healthColor = 'bg-amber-300';
                @endphp
                <div class="w-full bg-black/20 rounded-full h-2 mt-4 overflow-hidden">
                    <div class="{{ $healthColor }} h-2 rounded-full transition-all duration-1000 ease-out" 
                         x-data="{ width: 0 }"
                         x-init="setTimeout(() => width = {{ $analysis->health_score * 10 }}, 200)"
                         :style="'width: ' + width + '%'">
                    </div>
                </div>
            </div>

            <!-- Texto de Resumo -->
            <div class="w-full md:w-2/3 flex flex-col justify-center">
                <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                    <x-heroicon-s-sparkles class="w-5 h-5 text-green-200" /> Veredito da IA
                </h3>
                <p class="text-white/90 text-sm sm:text-base leading-relaxed font-medium">
                    {{ $analysis->public_summary }}
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @php
                        $levelBadge = '🔴 Baixo';
                        if($analysis->engagement_level == 'Médio') $levelBadge = '🟡 Médio';
                        elseif($analysis->engagement_level == 'Alto') $levelBadge = '🟢 Alto';
                        elseif($analysis->engagement_level == 'Muito Alto') $levelBadge = '⚡ Muito Alto';

                        $trendEmoji = '➡️';
                        if($analysis->growth_trend == 'Crescendo') $trendEmoji = '📈';
                        elseif($analysis->growth_trend == 'Declinando') $trendEmoji = '📉';
                    @endphp
                    <span class="px-3 py-1 bg-black/20 rounded-lg text-xs font-bold border border-white/10">Nível: {{ $levelBadge }}</span>
                    <span class="px-3 py-1 bg-black/20 rounded-lg text-xs font-bold border border-white/10">Tendência: {{ $trendEmoji }} {{ $analysis->growth_trend }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas detalhadas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm text-center">
            <div class="text-3xl mb-3">👥</div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Taxa de Ativos</p>
            <p class="text-3xl font-black text-slate-900">{{ $analysis->engagement_percent }}%</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm text-center">
            <div class="text-3xl mb-3">💬</div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Msgs Estimadas/Dia</p>
            <p class="text-3xl font-black text-slate-900">{{ number_format($analysis->msgs_per_day, 0, '', '.') }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm text-center">
            <div class="text-3xl mb-3">⏰</div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Horário de Pico</p>
            <p class="text-3xl font-black text-slate-900">{{ $analysis->peak_time }}</p>
        </div>
    </div>

    <!-- Gráfico de Engajamento e Membros -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gráfico Semanal -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                <x-heroicon-s-chart-bar-square class="w-5 h-5 text-slate-500" /> Atividade na Semana
            </h3>
            <div class="flex items-end justify-between gap-2 h-40 mt-auto border-b border-slate-100 pb-2">
                @foreach($chartData as $day => $value)
                <div class="flex flex-col items-center gap-2 flex-1 group" style="height: 100%;">
                    <div class="w-full mt-auto relative flex items-end justify-center" style="height: {{ $value }}%">
                        <div class="absolute -top-6 text-[10px] font-bold text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">{{ $value }}%</div>
                        <div class="w-full bg-green-500 rounded-t-md transition-all group-hover:bg-green-600 shadow-sm" style="height: 100%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $day }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Membros Mais Ativos -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                <x-heroicon-s-users class="w-5 h-5 text-slate-500" /> Membros Mais Ativos
            </h3>
            <div class="space-y-4">
                @foreach($activeMembers as $index => $member)
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <img src="{{ $member['avatar'] }}" alt="Avatar" class="w-10 h-10 rounded-full bg-slate-100">
                        @if($index === 0)
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center border border-white">
                                <x-heroicon-s-star class="w-2.5 h-2.5 text-white" />
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-900">{{ $member['name'] }}</p>
                        <p class="text-xs text-slate-500">{{ $member['msgs'] }} mensagens detectadas</p>
                    </div>
                    <div class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">
                        #{{ $index + 1 }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Prós e Contras -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <!-- Positivos -->
        <div class="bg-green-50/50 border border-green-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-black text-green-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                <x-heroicon-s-hand-thumb-up class="w-5 h-5 text-green-600" /> Pontos Fortes
            </h3>
            <ul class="space-y-3">
                @foreach($analysis->pros as $pro)
                <li class="flex items-start gap-2 text-sm text-slate-700 font-medium">
                    <span class="mr-1">✅</span>
                    <span>{{ $pro }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        
        <!-- Negativos -->
        <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-black text-amber-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-amber-600" /> Pontos de Atenção
            </h3>
            <ul class="space-y-3">
                @foreach($analysis->cons as $con)
                <li class="flex items-start gap-2 text-sm text-slate-700 font-medium">
                    <span class="mr-1">⚠️</span>
                    <span>{{ $con }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Card de Compartilhamento -->
    <div class="bg-white border border-slate-200 rounded-3xl p-8 sm:p-10 text-center shadow-sm mb-10">
        <div class="text-4xl mb-4">📣</div>
        <h2 class="text-2xl font-black text-slate-900 mb-3">Mostre pro seu grupo!</h2>
        <p class="text-slate-500 text-sm mb-8 max-w-lg mx-auto">
            Seu grupo recebeu nota {{ number_format($analysis->health_score, 1, ',', '.') }}/10. Compartilhe esse resultado com os seus membros!
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="https://api.whatsapp.com/send?text={{ urlencode("🔥 Olha a análise de engajamento do nosso grupo no WhatsGrupos!\n\nNome: {$analysis->group_name}\nSaúde: {$analysis->health_score}/10\nNível: {$analysis->engagement_level}\n\nConfira o relatório completo: " . route('tools.engagement.show', $analysis->uuid)) }}" 
               target="_blank"
               class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-500/30">
                Compartilhar no WhatsApp 📲
            </a>
            
            <button @click="
                      navigator.clipboard.writeText('{{ url()->current() }}'); 
                      copied = true; 
                      setTimeout(() => copied = false, 2000);
                    "
               class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-8 py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                <span x-show="!copied">Copiar link 🔗</span>
                <span x-show="copied" x-cloak class="text-green-600">Copiado! ✅</span>
            </button>
        </div>
    </div>

    <!-- Ações e CTA -->
    <div class="bg-slate-900 rounded-3xl p-8 sm:p-10 text-center relative overflow-hidden shadow-xl mb-24">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at center, #25D366 0%, transparent 70%);"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-black text-white mb-3">Gostou desse engajamento?</h2>
            <p class="text-slate-400 text-sm mb-8 max-w-lg mx-auto">
                O seu grupo tem um ótimo potencial! Que tal colocá-lo no nosso diretório oficial para atrair novos membros qualificados todos os dias?
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('send-group.create') }}" class="w-full sm:w-auto bg-green-500 hover:bg-green-400 text-slate-900 font-black px-8 py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                    <x-heroicon-s-plus-circle class="w-5 h-5" /> Cadastrar Grupo Oficialmente
                </a>
            </div>
        </div>
    </div>

    <!-- Floating Share Button -->
    <a href="https://wa.me/?text={{ urlencode("Veja a análise do meu grupo no WhatsGrupos! " . url()->current()) }}"
       target="_blank"
       class="fixed bottom-6 right-6 bg-green-500 text-white px-5 py-3 rounded-full shadow-2xl flex items-center gap-2 hover:bg-green-600 transition-all z-50 font-bold hover:scale-105">
      📊 Compartilhar análise
    </a>

</div>
@endsection



