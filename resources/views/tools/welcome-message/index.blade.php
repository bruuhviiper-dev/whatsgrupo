@extends('layouts.tools')

@section('navbar_color', 'bg-[#0d9488]')





@section('title', 'Gerador de Mensagem de Boas-Vindas para WhatsApp | WhatsGrupos')
@section('description', 'Receba os novos membros do seu grupo de WhatsApp com uma mensagem de boas-vindas profissional, formatada e com emojis. Grátis e rápido!')

@section('tool_icon')
<x-heroicon-s-hand-raised class="w-7 h-7 sm:w-8 sm:h-8" />
@endsection

@section('tool_logo_html')
BOAS<span class="text-green-100 font-bold">VINDAS</span>
@endsection

@section('tool_action_btn')
<a href="{{ route('tools.welcome-message') }}" class="bg-white text-[#25D366] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
  <x-heroicon-s-hand-raised class="w-4 h-4" />
  Nova Mensagem
</a>
@endsection

@section('tool_mobile_home')
<a href="{{ route('tools.welcome-message') }}" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('ferramentas/mensagem-de-boas-vindas*') ? 'text-[#25D366] bg-green-50' : '' }}">
  <x-heroicon-o-hand-raised class="w-6 h-6" />
  <span class="text-[10px] font-bold mt-0.5">Mensagem</span>
</a>
@endsection

@section('tool_mobile_fab')
<a href="{{ route('tools.welcome-message') }}" class="flex flex-col items-center justify-center -translate-y-4 relative">
  <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
    <x-heroicon-s-hand-raised class="w-8 h-8 text-white" />
  </div>
  <span class="text-[10px] font-bold mt-1 text-slate-600">Criar</span>
</a>
@endsection

@section('content')
<div class="py-8 md:py-12" x-data="welcomeGenerator()">
  <div class="text-center max-w-2xl mx-auto mb-10">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-teal-50 border-2 border-teal-100 text-teal-600 mb-6 shadow-[0_0_25px_rgba(13,148,136,0.2)] rotate-3">
        <x-heroicon-s-hand-raised class="w-10 h-10 -rotate-3" />
    </div>
    <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">
      Mensagem de <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-emerald-600">Boas-Vindas</span>
    </h1>
    <p class="text-slate-500 text-lg">Crie uma primeira impressão incrível! Escolha o estilo do seu grupo e gere um texto de recepção perfeito para novos membros.</p>
  </div>

  <div class="max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Lado Esquerdo: Opções -->
    <div class="lg:col-span-6 space-y-6">
      
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
          <x-heroicon-s-pencil-square class="w-5 h-5 text-teal-500"/> 1. Informações Básicas
        </h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Nome do Grupo (Opcional)</label>
                <input type="text" x-model="groupName" placeholder="Ex: Clã dos Gamers" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-medium text-slate-700 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Link para as Regras (Opcional)</label>
                <input type="text" x-model="rulesLink" placeholder="Ex: link do documento de regras ou site" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-medium text-slate-700 outline-none text-sm">
            </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
          <x-heroicon-s-tag class="w-5 h-5 text-teal-500"/> 2. Estilo da Recepção
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <template x-for="cat in categories" :key="cat.id">
            <button 
              @click="selectedCategory = cat.id"
              :class="selectedCategory === cat.id ? 'border-teal-500 bg-teal-50 text-teal-700' : 'border-slate-200 bg-white text-slate-600 hover:border-teal-300 hover:bg-slate-50'"
              class="border-2 rounded-xl p-3 flex flex-col items-center justify-center gap-2 transition-all font-bold text-sm text-center">
              <span x-html="cat.icon" class="w-6 h-6"></span>
              <span x-text="cat.name"></span>
            </button>
          </template>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
         <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
          <x-heroicon-s-adjustments-horizontal class="w-5 h-5 text-teal-500"/> 3. Adicionais
        </h2>
        
        <label class="flex items-center gap-3 cursor-pointer group p-3 bg-slate-50 rounded-lg border border-slate-100 hover:border-slate-300 transition-colors">
          <input type="checkbox" x-model="askToIntroduce" class="w-5 h-5 text-teal-500 rounded border-slate-300 focus:ring-teal-500 transition-all">
          <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Pedir para o membro se apresentar (Nome/Idade)</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer group p-3 bg-slate-50 rounded-lg border border-slate-100 hover:border-slate-300 transition-colors mt-3">
          <input type="checkbox" x-model="mentionAdmins" class="w-5 h-5 text-teal-500 rounded border-slate-300 focus:ring-teal-500 transition-all">
          <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Avisar que pode chamar os Administradores</span>
        </label>
      </div>

    </div>

    <!-- Lado Direito: Preview -->
    <div class="lg:col-span-6">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-1 bg-[url('/images/whatsapp-bg.png')] bg-repeat relative overflow-hidden sticky top-24">
        
        <div class="bg-slate-100/90 backdrop-blur-sm absolute inset-0 z-0"></div>

        <div class="relative z-10 flex flex-col h-full min-h-[500px]">
          <div class="bg-[#075E54] text-white p-4 flex items-center gap-3 rounded-t-xl">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
              <x-heroicon-s-hand-raised class="w-6 h-6" />
            </div>
            <div>
              <h3 class="font-bold leading-tight" x-text="groupName ? groupName : 'Seu Grupo'"></h3>
              <p class="text-xs text-white/70">Mensagem gerada ao vivo</p>
            </div>
          </div>

          <div class="flex-1 p-4 flex flex-col justify-center">
            
            <div class="bg-white rounded-xl p-4 shadow-sm relative group my-auto">
              <!-- Triangulo do balaozinho de zap -->
              <div class="absolute top-0 left-0 -ml-2 mt-4 w-0 h-0 border-t-[10px] border-t-white border-l-[15px] border-l-transparent border-b-[10px] border-b-transparent"></div>
              
              <p class="whitespace-pre-wrap text-slate-700 text-[15px] font-medium font-sans leading-relaxed" x-text="generatedMessage"></p>
              
              <div class="text-[10px] text-right text-slate-400 mt-2">12:00</div>
            </div>

          </div>

          <div class="p-4 bg-white/80 backdrop-blur-md border-t border-slate-200/50 rounded-b-xl flex gap-3 flex-col sm:flex-row">
            <button @click="copyMessage()" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-teal-500/30 transition-all flex items-center justify-center gap-2">
              <x-heroicon-s-clipboard-document-check class="w-5 h-5" />
              <span x-text="copied ? 'Copiado!' : 'Copiar Mensagem'"></span>
            </button>
          </div>
        </div>

      </div>
    </div>

  </div>
  <div class="mt-12 max-w-4xl mx-auto">
    <x-publish-invite />
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('welcomeGenerator', () => ({
    groupName: '',
    rulesLink: '',
    selectedCategory: 'amigavel',
    askToIntroduce: true,
    mentionAdmins: false,
    copied: false,

    categories: [
      { id: 'amigavel', name: 'Amigável', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" /></svg>' },
      { id: 'profissional', name: 'Negócios', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>' },
      { id: 'jogos', name: 'Jogos / Clã', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a1.5 1.5 0 0 1-1.5 1.5H6a1.5 1.5 0 0 0-1.5 1.5v4.5A1.5 1.5 0 0 0 6 15h1.5v1.5A1.5 1.5 0 0 0 9 18h6a1.5 1.5 0 0 0 1.5-1.5V15H18a1.5 1.5 0 0 0 1.5-1.5v-4.5A1.5 1.5 0 0 0 18 7.5h-2.25a1.5 1.5 0 0 1-1.5-1.5v0Z" /></svg>' },
      { id: 'estudos', name: 'Estudos', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>' },
    ],

    get generatedMessage() {
      let gName = this.groupName ? this.groupName.toUpperCase() : "NOSSO GRUPO";
      
      let title = "";
      let body = "";

      if(this.selectedCategory === 'amigavel') {
        title = `👋 Olá! Seja muito bem-vindo(a) ao ${gName}! 🎉\n\n`;
        body = "É um prazer ter você aqui com a gente. Sinta-se em casa para conversar, fazer amizades e interagir com o pessoal.\n\n";
      } else if (this.selectedCategory === 'profissional') {
        title = `🤝 Bem-vindo(a) ao ${gName}\n\n`;
        body = "Agradecemos a sua entrada no grupo. Nosso objetivo aqui é manter um ambiente de respeito, networking e troca de conhecimentos profissionais.\n\n";
      } else if (this.selectedCategory === 'jogos') {
        title = `🎮 BEM-VINDO(A) AO ${gName}! 🔥\n\n`;
        body = "A tropa aumentou! Bora focar no jogo, respeitar os membros e garantir aquela vitória. Tamo junto!\n\n";
      } else if (this.selectedCategory === 'estudos') {
        title = `📚 Bem-vindo(a) ao ${gName}! 📝\n\n`;
        body = "Que bom ter você aqui! Vamos compartilhar muito conhecimento, tirar dúvidas e focar juntos nos nossos objetivos.\n\n";
      }

      let extra = "";
      if(this.askToIntroduce) {
        extra += "📌 Para começarmos, por favor se apresente dizendo seu Nome e de onde você é!\n\n";
      }

      if(this.rulesLink) {
        extra += `📜 Leia nossas regras clicando aqui:\n${this.rulesLink}\n\n`;
      } else {
        extra += `📜 Não esqueça de ler as regras na descrição do grupo para evitarmos qualquer problema.\n\n`;
      }

      if(this.mentionAdmins) {
        extra += "👨‍💻 Qualquer dúvida, pode chamar um dos Administradores.\n\n";
      }

      let footer = "Aproveite o grupo! ✨";

      return title + body + extra + footer;
    },

    copyMessage() {
      navigator.clipboard.writeText(this.generatedMessage).then(() => {
        this.copied = true;
        setTimeout(() => this.copied = false, 2000);
      });
    }
  }));
});
</script>
@endpush
@endsection



