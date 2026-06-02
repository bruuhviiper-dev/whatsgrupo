@extends('layouts.tools')

@section('navbar_color', 'bg-[#059669]')





@section('title', 'Gerador de Regras para Grupo de WhatsApp | WhatsGrupos')
@section('description', 'Gere regras automáticas para o seu grupo de WhatsApp grátis! Personalize permissões de links, áudios e proibições com templates prontos.')

@section('tool_icon')
<x-heroicon-s-document-text class="w-7 h-7 sm:w-8 sm:h-8" />
@endsection

@section('tool_logo_html')
GERADOR<span class="text-green-100 font-bold">REGRAS</span>
@endsection

@section('tool_action_btn')
<a href="{{ route('tools.rules.index') }}" class="bg-white text-[#25D366] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
  <x-heroicon-s-document-text class="w-4 h-4" />
  Gerar Regras
</a>
@endsection

@section('tool_mobile_home')
<a href="{{ route('tools.rules.index') }}" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('ferramentas/gerador-de-regras*') ? 'text-[#25D366] bg-green-50' : '' }}">
  <x-heroicon-o-document-text class="w-6 h-6" />
  <span class="text-[10px] font-bold mt-0.5">Gerador</span>
</a>
@endsection

@section('tool_mobile_fab')
<a href="{{ route('tools.rules.index') }}" class="flex flex-col items-center justify-center -translate-y-4 relative">
  <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
    <x-heroicon-s-document-text class="w-8 h-8 text-white" />
  </div>
  <span class="text-[10px] font-bold mt-1 text-slate-600">Gerar</span>
</a>
@endsection

@section('canonical', route('tools.rules.index'))

@section('content')

<x-seo.tool name="Gerador de Regras para Grupos de WhatsApp"
            description="Crie regras claras e completas para administrar o seu grupo de WhatsApp, prontas para copiar e colar." />

<div class="py-8 md:py-12" x-data="rulesGenerator()">
  <div class="text-center max-w-2xl mx-auto mb-10">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-blue-50 border-2 border-blue-100 text-blue-600 mb-6 shadow-[0_0_25px_rgba(37,99,235,0.2)] rotate-3">
        <x-heroicon-s-document-text class="w-10 h-10 -rotate-3" />
    </div>
    <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">
      Gerador de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-600">Regras</span> para Grupos
    </h1>
    <p class="text-slate-500 text-lg">Mantenha a ordem no seu grupo! Personalize tudo: de envio de links a proibições rigorosas, e nós criamos o texto pronto para copiar.</p>
  </div>

  <div class="max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Lado Esquerdo: Opções -->
    <div class="lg:col-span-6 space-y-6">
      
      <!-- 1. Nome do Grupo -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
          <x-heroicon-s-pencil-square class="w-5 h-5 text-blue-500"/> 1. Nome do Grupo <span class="text-xs font-normal text-slate-400">(Opcional)</span>
        </h2>
        <input type="text" x-model="groupName" placeholder="Ex: Resenha Futebol Clube" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-slate-700 outline-none">
      </div>

      <!-- 2. Categoria -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
          <x-heroicon-s-tag class="w-5 h-5 text-blue-500"/> 2. Categoria do Grupo
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <template x-for="cat in categories" :key="cat.id">
            <button 
              @click="selectCategory(cat.id)"
              :class="selectedCategory === cat.id ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-300 hover:bg-slate-50'"
              class="border-2 rounded-xl p-3 flex flex-col items-center justify-center gap-2 transition-all font-bold text-sm text-center">
              <span x-html="cat.icon" class="w-6 h-6"></span>
              <span x-text="cat.name"></span>
            </button>
          </template>
        </div>
      </div>

      <!-- 3. Personalizar -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
         <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
          <x-heroicon-s-adjustments-horizontal class="w-5 h-5 text-blue-500"/> 3. Personalizar Regras
        </h2>
        
        <div class="space-y-4">
          <!-- O que é permitido -->
          <div class="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100">
            <h3 class="text-[11px] font-black uppercase text-emerald-600 tracking-wider mb-3">O que é Permitido?</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="allowLinks" class="w-5 h-5 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Enviar Links</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="allowAds" class="w-5 h-5 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Vendas / Spam</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="allowAudio" class="w-5 h-5 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Mandar Áudios</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="allowStickers" class="w-5 h-5 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Figurinhas</span>
              </label>
            </div>
          </div>

          <!-- O que é proibido -->
          <div class="p-4 bg-red-50/50 rounded-xl border border-red-100">
            <h3 class="text-[11px] font-black uppercase text-red-500 tracking-wider mb-3">O que dá BAN imediato?</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="noPv" class="w-5 h-5 text-red-500 rounded border-slate-300 focus:ring-red-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Chamar no PV</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="noPolitics" class="w-5 h-5 text-red-500 rounded border-slate-300 focus:ring-red-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Política / Religião</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="noFakeNews" class="w-5 h-5 text-red-500 rounded border-slate-300 focus:ring-red-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Fake News</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="noPorn" class="w-5 h-5 text-red-500 rounded border-slate-300 focus:ring-red-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Conteúdo +18</span>
              </label>
            </div>
          </div>

          <!-- Regras Extras -->
          <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">
            <h3 class="text-[11px] font-black uppercase text-blue-500 tracking-wider mb-3">Configurações Extras</h3>
            <div class="grid grid-cols-1 gap-3">
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="mustIntroduce" class="w-5 h-5 text-blue-500 rounded border-slate-300 focus:ring-blue-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Obrigatório se apresentar ao entrar (Nome e Idade)</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" x-model="strictTime" class="w-5 h-5 text-blue-500 rounded border-slate-300 focus:ring-blue-500 transition-all">
                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Grupo silenciado na madrugada (22h às 07h)</span>
              </label>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- Lado Direito: Preview -->
    <div class="lg:col-span-6">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-1 bg-[url('/images/whatsapp-bg.png')] bg-repeat relative overflow-hidden sticky top-24">
        
        <div class="bg-slate-100/90 backdrop-blur-sm absolute inset-0 z-0"></div>

        <div class="relative z-10 flex flex-col h-full min-h-[600px]">
          <div class="bg-[#075E54] text-white p-4 flex items-center gap-3 rounded-t-xl">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
              <x-heroicon-s-document-text class="w-6 h-6" />
            </div>
            <div>
              <h3 class="font-bold leading-tight" x-text="groupName ? groupName : 'Descrição do Grupo'"></h3>
              <p class="text-xs text-white/70">Pré-visualização Dinâmica</p>
            </div>
          </div>

          <div class="flex-1 p-4 flex flex-col justify-center max-h-[600px] overflow-y-auto custom-scrollbar">
            
            <div class="bg-white rounded-xl p-4 shadow-sm relative group my-auto">
              <p class="whitespace-pre-wrap text-slate-700 text-[15px] font-medium font-sans leading-relaxed" x-text="generatedRules"></p>
            </div>

          </div>

          <div class="p-4 bg-white/80 backdrop-blur-md border-t border-slate-200/50 rounded-b-xl flex gap-3 flex-col sm:flex-row">
            <button @click="copyRules()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
              <x-heroicon-s-clipboard-document-check class="w-5 h-5" />
              <span x-text="copied ? 'Texto Copiado!' : 'Copiar Regras'"></span>
            </button>
            <a href="/enviar-grupo" class="flex-1 bg-[#25D366] hover:bg-[#1da851] text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-green-500/30 transition-all flex items-center justify-center gap-2 text-center">
              <x-heroicon-s-plus-circle class="w-5 h-5" />
              Divulgar Grupo
            </a>
          </div>
        </div>

      </div>
    </div>

</div>
  <div class="mt-12 max-w-4xl mx-auto">
    <x-publish-invite />
  </div>
</div>

@push('head')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.1); border-radius: 10px; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('rulesGenerator', () => ({
    groupName: '',
    selectedCategory: 'geral',
    
    // Permitido
    allowLinks: false,
    allowAds: false,
    allowAudio: true,
    allowStickers: true,
    
    // Proibido
    noPv: true,
    noPolitics: true,
    noFakeNews: true,
    noPorn: true,

    // Extras
    strictTime: false,
    mustIntroduce: false,

    copied: false,

    categories: [
      { id: 'geral', name: 'Geral', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>' },
      { id: 'jogos', name: 'Jogos / FF', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a1.5 1.5 0 0 1-1.5 1.5H6a1.5 1.5 0 0 0-1.5 1.5v4.5A1.5 1.5 0 0 0 6 15h1.5v1.5A1.5 1.5 0 0 0 9 18h6a1.5 1.5 0 0 0 1.5-1.5V15H18a1.5 1.5 0 0 0 1.5-1.5v-4.5A1.5 1.5 0 0 0 18 7.5h-2.25a1.5 1.5 0 0 1-1.5-1.5v0Z" /></svg>' },
      { id: 'negocios', name: 'Negócios', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>' },
      { id: 'estudos', name: 'Estudos', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>' },
      { id: 'familia', name: 'Família', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>' },
      { id: '18mais', name: '+18', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>' },
    ],

    selectCategory(id) {
      this.selectedCategory = id;
    },

    get generatedRules() {
      let title = this.groupName 
        ? `🌟 BEM-VINDO AO GRUPO:\n${this.groupName.toUpperCase()} 🌟\n\n` 
        : `👋 BEM-VINDO AO GRUPO!\n\n`;
      
      let desc = "";
      switch(this.selectedCategory) {
        case 'geral':
          desc = "Nosso objetivo é fazer amizades, conversar e trocar ideias. Para manter o grupo organizado, leia atentamente as regras abaixo.\n\n"; break;
        case 'jogos':
          desc = "Foco total no jogo e nas partidas! Respeite o nível de cada jogador. Para manter a organização, siga as regras abaixo.\n\n"; break;
        case 'negocios':
          desc = "Grupo focado em networking, negócios e troca de contatos profissionais. Trate todos com o máximo de profissionalismo.\n\n"; break;
        case 'estudos':
          desc = "Foco absoluto nos estudos! Aqui compartilhamos materiais, dicas e resumos. Mantenha o foco e respeite os colegas.\n\n"; break;
        case 'familia':
          desc = "Grupo da família para nos mantermos próximos! Bom dia, boa tarde e boa noite são sempre muito bem-vindos.\n\n"; break;
        case '18mais':
          desc = "Grupo exclusivo para MAIORES de 18 anos. Conteúdo adulto sem censura, mas com limites e respeito à privacidade.\n\n"; break;
      }

      let rulesText = "📜 REGRAS DO GRUPO:\n\n";

      let permitted = [];
      if(this.allowLinks) permitted.push("✅ Enviar links (com moderação)");
      if(this.allowAds) permitted.push("✅ Divulgação de vendas/serviços");
      if(this.allowAudio) permitted.push("✅ Mandar áudios");
      if(this.allowStickers) permitted.push("✅ Enviar figurinhas e stickers");
      
      if(permitted.length > 0) {
        rulesText += permitted.join("\n") + "\n\n";
      }

      let prohibited = [];
      if(!this.allowLinks) prohibited.push("🚫 Enviar links de outros grupos");
      if(!this.allowAds) prohibited.push("🚫 Fazer SPAM ou propagandas");
      if(!this.allowAudio) prohibited.push("🚫 Enviar áudios (apenas texto/mídia)");
      if(!this.allowStickers) prohibited.push("🚫 Flodar com muitas figurinhas");
      
      if(this.noPv) prohibited.push("🚫 Chamar membros no privado sem permissão");
      if(this.noPolitics) prohibited.push("🚫 Discutir sobre política ou religião");
      if(this.noFakeNews) prohibited.push("🚫 Enviar correntes ou Fake News");
      
      if(this.noPorn && this.selectedCategory !== '18mais') {
        prohibited.push("🚫 Enviar pornografia, gore ou conteúdo +18");
      }
      
      prohibited.push("🚫 Falta de respeito ou ofensas (BAN direto)");

      if(prohibited.length > 0) {
        rulesText += prohibited.join("\n") + "\n\n";
      }

      let extra = [];
      if(this.mustIntroduce) extra.push("📌 Obrigatório: Apresente-se (Nome, Idade, Cidade) assim que entrar.");
      if(this.strictTime) extra.push("🌙 Horário de silêncio: Grupo fechado das 22h às 07h.");

      if(extra.length > 0) {
        rulesText += "📌 OUTROS AVISOS:\n" + extra.join("\n") + "\n\n";
      }

      let final = `${title}${desc}${rulesText}⚠️ O descumprimento resultará em remoção imediata.\n\n🌐 Encontre mais grupos em: whatsgrupos.com.br`;

      return final;
    },

    copyRules() {
      navigator.clipboard.writeText(this.generatedRules).then(() => {
        this.copied = true;
        setTimeout(() => this.copied = false, 2000);
      });
    }
  }));
});
</script>
@endpush
@endsection



