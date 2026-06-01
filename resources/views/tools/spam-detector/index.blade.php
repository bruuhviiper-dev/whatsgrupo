@extends('layouts.tools')

@section('navbar_color', 'bg-[#14532d]')

@section('title', 'Detector de Spam de WhatsApp | WhatsGrupos')
@section('description', 'Sua mensagem de grupo do WhatsApp parece spam? Use nossa ferramenta gratuita para analisar e melhorar a atratividade do seu texto.')

@section('tool_icon')
<x-heroicon-s-shield-exclamation class="w-7 h-7 sm:w-8 sm:h-8" />
@endsection

@section('tool_logo_html')
ANTI<span class="text-teal-100 font-bold">SPAM</span>
@endsection

@section('tool_action_btn')
<a href="{{ route('tools.spam-detector') }}" class="bg-white text-[#0f766e] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
  <x-heroicon-s-shield-check class="w-4 h-4" />
  Nova Análise
</a>
@endsection

@section('tool_mobile_home')
<a href="{{ route('tools.spam-detector') }}" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#0f766e] hover:bg-teal-50 transition-all {{ request()->is('ferramentas/detector-de-spam*') ? 'text-[#0f766e] bg-teal-50' : '' }}">
  <x-heroicon-o-shield-exclamation class="w-6 h-6" />
  <span class="text-[10px] font-bold mt-0.5">Anti-Spam</span>
</a>
@endsection

@section('tool_mobile_fab')
<a href="{{ route('tools.spam-detector') }}" class="flex flex-col items-center justify-center -translate-y-4 relative">
  <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#0f766e] to-[#042f2e] shadow-lg shadow-teal-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
    <x-heroicon-s-shield-check class="w-8 h-8 text-white" />
  </div>
  <span class="text-[10px] font-bold mt-1 text-slate-600">Analisar</span>
</a>
@endsection

@section('content')
<div class="py-8 md:py-12" x-data="spamDetector()">
  <div class="text-center max-w-2xl mx-auto mb-10">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-red-50 border-2 border-red-100 text-red-500 mb-6 shadow-[0_0_25px_rgba(239,68,68,0.2)] rotate-3">
        <x-heroicon-s-shield-exclamation class="w-10 h-10 -rotate-3" />
    </div>
    <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">
      Detector de <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-rose-500">Spam</span>
    </h1>
    <p class="text-slate-500 text-lg">As pessoas estão ignorando sua mensagem? Cole o texto de divulgação do seu grupo abaixo e descubra se ele tem cara de spam ou se está atrativo.</p>
  </div>

  <div class="max-w-[700px] mx-auto">
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        
        <label class="block text-sm font-bold text-slate-700 mb-3">Cole a mensagem que você envia para divulgar seu grupo:</label>
        
        <div class="relative">
            <textarea 
                x-model="message" 
                rows="6"
                placeholder="Exemplo: GANHE DINHEIRO FÁCIL! Clique no link agora e mude de vida!!! 🤑🤑🤑 https://chat.whatsapp.com/..." 
                class="w-full p-4 rounded-xl border-2 border-slate-200 focus:ring-0 focus:border-[#0f766e] transition-all font-medium text-slate-800 outline-none text-base resize-y"></textarea>
            
            <button x-show="message.length > 0" @click="message = ''; result = null; error = false;" class="absolute right-3 top-3 text-slate-400 hover:text-red-500 transition-colors bg-white rounded-full" style="display: none;">
                <x-heroicon-s-x-circle class="w-6 h-6" />
            </button>
        </div>

        <p class="text-xs text-slate-400 mt-3 flex items-center gap-1 font-medium mb-6">
            <x-heroicon-s-information-circle class="w-4 h-4"/>
            A mensagem não é salva. A análise é imediata e privada.
        </p>

        <button 
            @click="analyze()" 
            :disabled="loading || message.trim().length === 0"
            class="w-full bg-[#0f766e] hover:bg-[#0d9488] text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-teal-500/30 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="!loading" class="flex items-center gap-2">
                <x-heroicon-s-magnifying-glass class="w-5 h-5" /> Analisar Mensagem
            </span>
            <span x-show="loading" class="flex items-center gap-2" style="display: none;">
                <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" /> Analisando...
            </span>
        </button>

        <!-- Mensagem de Erro de API -->
        <div x-show="error" class="mt-4 p-4 bg-red-50 text-red-600 rounded-xl text-sm font-bold flex items-center gap-2" style="display: none;">
             <x-heroicon-s-x-circle class="w-5 h-5" />
             Ocorreu um erro ao analisar a mensagem. Tente novamente.
        </div>

        <!-- Resultados da Validação -->
        <div x-show="result !== null" x-transition.opacity class="mt-8" style="display: none;">
            
            <div :class="'p-6 rounded-xl border text-center relative overflow-hidden ' + result?.colorClass">
                <div class="absolute -right-4 -top-4 opacity-10">
                    <template x-if="result?.icon === 'check-badge'">
                        <x-heroicon-s-check-badge class="w-32 h-32"/>
                    </template>
                    <template x-if="result?.icon === 'exclamation-circle'">
                        <x-heroicon-s-exclamation-circle class="w-32 h-32"/>
                    </template>
                    <template x-if="result?.icon === 'exclamation-triangle'">
                        <x-heroicon-s-exclamation-triangle class="w-32 h-32"/>
                    </template>
                </div>
                
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/40 mb-4 relative z-10 shadow-sm">
                    <template x-if="result?.icon === 'check-badge'">
                        <x-heroicon-s-check-badge class="w-8 h-8"/>
                    </template>
                    <template x-if="result?.icon === 'exclamation-circle'">
                        <x-heroicon-s-exclamation-circle class="w-8 h-8"/>
                    </template>
                    <template x-if="result?.icon === 'exclamation-triangle'">
                        <x-heroicon-s-exclamation-triangle class="w-8 h-8"/>
                    </template>
                </div>
                
                <h3 class="text-3xl font-black mb-1 relative z-10" x-text="result ? result.score + '/100' : ''"></h3>
                <h4 class="text-xl font-bold mb-4 relative z-10 uppercase tracking-widest opacity-80" x-text="result?.classification"></h4>
                
                <!-- Indicadores -->
                <div class="flex flex-wrap gap-2 justify-center relative z-10 mb-6">
                    <template x-for="ind in (result ? result.indicators : [])">
                        <span :class="{
                            'bg-green-100 text-green-800 border-green-200': ind.type === 'success',
                            'bg-amber-100 text-amber-800 border-amber-200': ind.type === 'warning',
                            'bg-red-100 text-red-800 border-red-200': ind.type === 'danger',
                        }" class="text-xs font-bold px-3 py-1.5 rounded-full border flex items-center gap-1 shadow-sm">
                            <template x-if="ind.icon === 'check'">
                                <x-heroicon-s-check class="w-3 h-3" />
                            </template>
                            <template x-if="ind.icon === 'warning'">
                                <x-heroicon-s-exclamation-triangle class="w-3 h-3" />
                            </template>
                            <span x-text="ind.text"></span>
                        </span>
                    </template>
                </div>

                <!-- Sugestões -->
                <template x-if="result && result.suggestions.length > 0">
                    <div class="bg-white/60 p-4 rounded-xl text-left text-sm relative z-10">
                        <p class="font-bold mb-2 opacity-90">O que melhorar:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <template x-for="sug in result.suggestions">
                                <li x-text="sug" class="font-medium opacity-80"></li>
                            </template>
                        </ul>
                    </div>
                </template>
                
            </div>
            
            <div class="mt-6 flex justify-center relative z-10">
                <a href="/enviar-grupo" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <x-heroicon-s-rocket-launch class="w-5 h-5" /> Divulgar Grupo Agora
                </a>
            </div>

        </div>

    </div>
  </div>
  <div class="max-w-[1100px] mx-auto mt-10">
    <x-adsense class="my-4" />
  </div>
  <div class="mt-12 max-w-4xl mx-auto">
    <x-publish-invite />
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('spamDetector', () => ({
    message: '',
    loading: false,
    error: false,
    result: null,

    async analyze() {
        if(this.message.trim().length === 0) return;
        
        this.loading = true;
        this.error = false;
        this.result = null;

        try {
            const response = await fetch('{{ route('tools.spam-detector.analyze') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: this.message })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            this.result = await response.json();
        } catch (e) {
            console.error(e);
            this.error = true;
        } finally {
            this.loading = false;
        }
    }
  }));
});
</script>
@endpush
@endsection
