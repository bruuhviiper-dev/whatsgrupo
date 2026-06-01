@extends('layouts.tools')

@section('navbar_color', 'bg-[#0f766e]')



@section('title', 'Gerador de Enquete para WhatsApp | WhatsGrupos')
@section('description', 'Crie rapidamente enquetes interativas baseadas em texto para engajar seus membros em grupos e listas de transmissão.')

@section('tool_icon')
<x-heroicon-s-chart-bar class="w-7 h-7 sm:w-8 sm:h-8" />
@endsection

@section('tool_logo_html')
GERADOR<span class="text-green-100 font-bold">ENQUETE</span>
@endsection

@section('tool_action_btn')
<a href="{{ route('tools.poll-generator') }}" class="bg-white text-[#25D366] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
  <x-heroicon-s-chart-bar class="w-4 h-4" />
  Nova Enquete
</a>
@endsection

@section('tool_mobile_home')
<a href="{{ route('tools.poll-generator') }}" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('ferramentas/gerador-de-enquete*') ? 'text-[#25D366] bg-green-50' : '' }}">
  <x-heroicon-o-chart-bar class="w-6 h-6" />
  <span class="text-[10px] font-bold mt-0.5">Enquetes</span>
</a>
@endsection

@section('tool_mobile_fab')
<a href="{{ route('tools.poll-generator') }}" class="flex flex-col items-center justify-center -translate-y-4 relative">
  <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
    <x-heroicon-s-plus class="w-8 h-8 text-white" />
  </div>
  <span class="text-[10px] font-bold mt-1 text-slate-600">Criar</span>
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto" x-data="pollGenerator()">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-pink-50 border-2 border-pink-100 text-pink-500 mb-6 shadow-[0_0_25px_rgba(236,72,153,0.2)] rotate-3">
            <x-heroicon-s-chart-bar class="w-10 h-10 -rotate-3" />
        </div>
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">
            Gerador de <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-rose-500">Enquete</span>
        </h1>
        <p class="text-slate-600 text-lg font-medium max-w-2xl mx-auto">Crie enquetes textuais personalizadas prontas para copiar e colar em qualquer lugar do WhatsApp.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        <!-- Editor -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <x-heroicon-s-adjustments-horizontal class="w-6 h-6 text-pink-500" /> Montar Enquete
            </h2>

            <div class="space-y-6">
                <!-- Pergunta -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Qual a sua pergunta?</label>
                    <input type="text" x-model="question" placeholder="Ex: Qual o melhor dia para o encontro?" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-pink-500 focus:border-pink-500 block p-3 font-medium transition-colors outline-none">
                </div>

                <!-- Opções -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Opções de Resposta</label>
                    <div class="space-y-3">
                        <template x-for="(option, index) in options" :key="index">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-bold w-6 text-right" x-text="(index + 1) + '.'"></span>
                                <input type="text" x-model="options[index]" placeholder="Digite uma opção" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-pink-500 focus:border-pink-500 block p-3 font-medium transition-colors outline-none">
                                <button @click="removeOption(index)" :disabled="options.length <= 2" class="p-3 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                    <x-heroicon-s-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </template>
                    </div>
                    <button @click="addOption()" :disabled="options.length >= 10" class="mt-4 flex items-center gap-2 text-sm font-bold text-pink-600 hover:text-pink-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed bg-pink-50 px-4 py-2 rounded-xl hover:bg-pink-100">
                        <x-heroicon-s-plus-circle class="w-5 h-5" /> Adicionar Opção (Max 10)
                    </button>
                </div>

                <hr class="border-slate-100">

                <!-- Mensagem Extra -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Instrução para os membros (opcional)</label>
                    <input type="text" x-model="instruction" placeholder="Ex: Responda com o número da sua escolha!" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-pink-500 focus:border-pink-500 block p-3 font-medium transition-colors outline-none">
                </div>

                <div class="pt-4">
                    <button @click="copyToClipboard()" class="w-full bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-black text-lg py-4 px-6 rounded-2xl shadow-xl shadow-pink-500/20 transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5">
                        <x-heroicon-s-clipboard-document-check class="w-6 h-6" /> 
                        <span x-text="copied ? 'Copiado!' : 'Copiar Enquete'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden h-full flex flex-col min-h-[400px]">
            <div class="absolute top-0 right-0 w-32 h-32 bg-pink-500/20 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-rose-500/20 rounded-full blur-3xl -ml-10 -mb-10 pointer-events-none"></div>
            
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2 relative z-10">
                <x-heroicon-s-eye class="w-6 h-6 text-slate-400" /> Pré-visualização
            </h2>

            <div class="flex-1 bg-[#efeae2] bg-opacity-90 bg-[url('https://i.pinimg.com/736x/8c/98/99/8c98994518b575bfd8c949e91d20548b.jpg')] bg-cover bg-center bg-blend-soft-light rounded-2xl p-4 sm:p-6 relative z-10 border border-slate-700/50 flex flex-col justify-end">
                <!-- Chat Bubble -->
                <div class="bg-[#dcf8c6] rounded-2xl rounded-tr-sm p-3 sm:p-4 shadow-sm w-full max-w-[280px] self-end relative">
                    <div class="text-[#111b21] text-[15px] leading-snug font-medium whitespace-pre-wrap font-sans" x-text="generateText()"></div>
                    <div class="text-[10px] text-slate-500 text-right mt-1 font-semibold">12:00</div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <div class="max-w-[1100px] mx-auto mt-10">
        <x-adsense class="my-4" />
    </div>
    <div class="mt-6 max-w-4xl mx-auto">
        <x-publish-invite />
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pollGenerator', () => ({
            question: 'Qual o melhor dia para o nosso encontro?',
            options: ['Sábado de Manhã', 'Sábado à Tarde', 'Domingo'],
            instruction: 'Responda com o número da sua escolha! 👇',
            copied: false,
            numberEmojis: ['1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣','🔟'],

            addOption() {
                if(this.options.length < 10) {
                    this.options.push('');
                }
            },

            removeOption(index) {
                if(this.options.length > 2) {
                    this.options.splice(index, 1);
                }
            },

            generateText() {
                let text = `📊 *${this.question || 'Sua pergunta aqui?'}*\n\n`;
                
                this.options.forEach((opt, idx) => {
                    if(opt.trim() !== '') {
                        text += `${this.numberEmojis[idx]} ${opt}\n`;
                    }
                });

                if(this.instruction.trim() !== '') {
                    text += `\n_${this.instruction}_\n`;
                }
                
                return text;
            },

            copyToClipboard() {
                const text = this.generateText();
                navigator.clipboard.writeText(text).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                });
            }
        }))
    })
</script>
@endsection



