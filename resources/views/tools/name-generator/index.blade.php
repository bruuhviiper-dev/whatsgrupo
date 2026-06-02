@extends('layouts.tools')

@section('navbar_color', 'bg-[#10b981]')





@section('title', 'Gerador de Nomes para Grupos de WhatsApp | WhatsGrupos')
@section('description', 'Ficou sem criatividade? Gere milhares de nomes criativos, engraçados e exclusivos para o seu grupo de Família, Amigos ou Negócios.')

@section('tool_icon')
<x-heroicon-s-sparkles class="w-7 h-7 sm:w-8 sm:h-8" />
@endsection

@section('tool_logo_html')
GERADOR<span class="text-green-100 font-bold">NOMES</span>
@endsection

@section('tool_action_btn')
<a href="{{ route('tools.name-generator') }}" class="bg-white text-[#25D366] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
  <x-heroicon-s-sparkles class="w-4 h-4" />
  Novo Nome
</a>
@endsection

@section('tool_mobile_home')
<a href="{{ route('tools.name-generator') }}" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('ferramentas/gerador-de-nomes*') ? 'text-[#25D366] bg-green-50' : '' }}">
  <x-heroicon-o-sparkles class="w-6 h-6" />
  <span class="text-[10px] font-bold mt-0.5">Gerador</span>
</a>
@endsection

@section('tool_mobile_fab')
<a href="{{ route('tools.name-generator') }}" class="flex flex-col items-center justify-center -translate-y-4 relative">
  <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
    <x-heroicon-s-sparkles class="w-8 h-8 text-white" />
  </div>
  <span class="text-[10px] font-bold mt-1 text-slate-600">Nomes</span>
</a>
@endsection

@section('canonical', route('tools.name-generator'))

@section('content')

<x-seo.tool name="Gerador de Nomes para Grupos de WhatsApp"
            description="Gere nomes criativos, engraçados e exclusivos para o seu grupo de WhatsApp, de graça e sem cadastro." />

<div class="py-8 md:py-12" x-data="nameGenerator()">
  <div class="text-center max-w-2xl mx-auto mb-10">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-purple-50 border-2 border-purple-100 text-purple-500 mb-6 shadow-[0_0_25px_rgba(168,85,247,0.2)] rotate-3">
        <x-heroicon-s-sparkles class="w-10 h-10 -rotate-3" />
    </div>
    <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">
      Gerador de Nomes <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-500">Criativos</span>
    </h1>
    <p class="text-slate-500 text-lg">Sem ideias para o nome do grupo? Escolha a categoria e clique no botão para gerar combinações fantásticas instantaneamente.</p>
  </div>

  <div class="max-w-[1000px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Esquerda: Controles -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
          <x-heroicon-s-tag class="w-5 h-5 text-purple-500"/> Escolha o Tipo de Grupo
        </h2>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <template x-for="cat in categories" :key="cat.id">
            <button 
              @click="selectCategory(cat.id)"
              :class="selectedCategory === cat.id ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-slate-200 bg-white text-slate-600 hover:border-purple-300 hover:bg-slate-50'"
              class="border-2 rounded-xl p-3 flex flex-col items-center justify-center gap-2 transition-all font-bold text-sm text-center">
              <span x-html="cat.icon" class="w-6 h-6"></span>
              <span x-text="cat.name"></span>
            </button>
          </template>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
          <x-heroicon-s-plus-circle class="w-5 h-5 text-purple-500"/> Sufixos (Opcional)
        </h2>
        
        <label class="flex items-center gap-3 cursor-pointer group p-3 bg-slate-50 rounded-lg border border-slate-100 hover:border-slate-300 transition-colors">
          <input type="checkbox" x-model="useYear" class="w-5 h-5 text-purple-600 rounded border-slate-300 focus:ring-purple-500 transition-all">
          <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Adicionar Ano Atual (ex: 2026)</span>
        </label>
        
        <label class="flex items-center gap-3 cursor-pointer group p-3 bg-slate-50 rounded-lg border border-slate-100 hover:border-slate-300 transition-colors mt-3">
          <input type="checkbox" x-model="useCity" class="w-5 h-5 text-purple-600 rounded border-slate-300 focus:ring-purple-500 transition-all">
          <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Nome da sua Cidade/Estado</span>
        </label>
        
        <div x-show="useCity" class="mt-3" x-transition>
            <input type="text" x-model="cityText" placeholder="Digite a cidade (ex: SP, Rio...)" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-purple-500 outline-none text-sm">
        </div>
      </div>

      <button @click="generateNames()" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-black text-lg py-4 px-6 rounded-2xl shadow-lg shadow-purple-500/30 transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
        <x-heroicon-s-arrow-path class="w-6 h-6" x-bind:class="isGenerating ? 'animate-spin' : ''" />
        <span x-text="generatedList.length > 0 ? 'Gerar Novas Ideias' : 'Gerar Nomes Mágicos'"></span>
      </button>
    </div>

    <!-- Direita: Resultados -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col h-full min-h-[500px]">
      <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
        <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
          <x-heroicon-s-star class="w-6 h-6 text-yellow-400"/> Resultados
        </h2>
        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full" x-text="generatedList.length + ' Nomes'"></span>
      </div>

      <!-- Sem Resultados -->
      <div x-show="generatedList.length === 0" class="flex-1 flex flex-col items-center justify-center text-slate-400">
        <x-heroicon-o-light-bulb class="w-20 h-20 opacity-20 mb-4" />
        <p class="font-medium text-sm">Clique em "Gerar Nomes" para começar</p>
      </div>

      <!-- Lista -->
      <div x-show="generatedList.length > 0" class="flex-1 space-y-3 overflow-y-auto pr-2 custom-scrollbar max-h-[500px]">
        <template x-for="(name, index) in generatedList" :key="index">
          <div class="group flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-purple-50 hover:border-purple-200 transition-colors">
            <span class="font-bold text-slate-700 text-lg group-hover:text-purple-800 transition-colors" x-text="name"></span>
            <button @click="copyName(name)" class="text-slate-400 hover:text-purple-600 transition-colors flex items-center gap-1 text-xs font-bold" :title="'Copiar ' + name">
              <x-heroicon-o-clipboard class="w-5 h-5" />
            </button>
          </div>
        </template>
      </div>

      <div x-show="copiedToast" x-transition class="mt-4 bg-green-100 text-green-800 font-bold px-4 py-3 rounded-lg text-center text-sm border border-green-200">
        Nome copiado com sucesso!
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
  Alpine.data('nameGenerator', () => ({
    selectedCategory: 'familia',
    useYear: false,
    useCity: false,
    cityText: '',
    isGenerating: false,
    generatedList: [],
    copiedToast: false,

    categories: [
      { id: 'familia', name: 'Família', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>' },
      { id: 'amigos', name: 'Amigos / Resenha', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>' },
      { id: 'jogos', name: 'Jogos / FF', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a1.5 1.5 0 0 1-1.5 1.5H6a1.5 1.5 0 0 0-1.5 1.5v4.5A1.5 1.5 0 0 0 6 15h1.5v1.5A1.5 1.5 0 0 0 9 18h6a1.5 1.5 0 0 0 1.5-1.5V15H18a1.5 1.5 0 0 0 1.5-1.5v-4.5A1.5 1.5 0 0 0 18 7.5h-2.25a1.5 1.5 0 0 1-1.5-1.5v0Z" /></svg>' },
      { id: 'estudos', name: 'Estudos', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>' },
      { id: 'negocios', name: 'Negócios', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>' },
      { id: 'engracados', name: 'Engraçados', icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" /></svg>' },
    ],

    wordBanks: {
        familia: ["Família", "Os", "A Grande Família", "Laços", "Sangue", "Turma", "Clã", "Casa", "Unidos", "Parentes", "Família Buscapé", "Nosso Lar", "DNA", "Os Silva", "Sobrenome", "Herança", "Família Real", "Tropa da", "Base"],
        familiaSuffix: ["Buscapé", "Unida", "Abençoada", "Blindada", "Raiz", "Fofoqueiros", "Feliz", "Loucos", "Zueira", "Amo Muito", "de Ouro", "Reunida", "Sem Fronteiras", "Especial", "Protegida", "da Bagunça", "Incansáveis"],
        
        amigos: ["Resenha", "Os Parças", "Turma", "Galera", "Os Crias", "Tropa", "Amigos", "Bonde", "Diretoria", "Elite", "Irmandade", "Os Mitos", "Só Loucos", "Panelinha", "Bebados", "Fofoca", "Sem Limites"],
        amigosSuffix: ["da Madrugada", "do Bar", "Sem Limites", "Fofoca", "Vip", "Sempre Juntos", "do Zé", "Reunida", "Só Elite", "Raiz", "Inimigos do Fim", "da Zueira", "do FDS", "Cancelados", "Tóxicos", "Raiz", "Milionários"],
        
        jogos: ["Clã", "Squad", "Tropa", "Guilda", "Team", "Pro Players", "Elite", "Lendários", "Insanos", "Bonde", "Nerdolas", "Só Capa", "Os Brabos", "Platina", "Mestres", "Assassinos", "Win", "Tryhard"],
        jogosSuffix: ["da Morte", "Rushadão", "Sem Capa", "Gamer", "E-sports", "Mobile", "Mestres", "Tryhard", "Tóxicos", "Win", "Lendários", "Invencíveis", "da Zoeira", "do Vício", "Headshot", "Ouro", "Tropa"],
        
        estudos: ["Foco", "Concurseiros", "Turma", "Estudos", "Aprovação", "Gênios", "Nerdola", "Futuros", "Medicina", "Direito", "Engenharia", "Estudantes", "Sem Dormir", "Gabaritando", "Os Cdf", "Bolsistas", "Tropa do"],
        estudosSuffix: ["Intensivo", "Focado", "Gabarito", "Unidos", "Positiva", "Sem Dormir", "Milionários", "Café", "Bora Estudar", "Vestibular", "Enem", "Aprovados", "da Madrugada", "Federal", "Resumos", "Foco 100%"],

        negocios: ["Negócios", "Empreendedores", "Networking", "Vendas", "Mercado", "Investidores", "Empresários", "Lucro", "Varejo", "Atacado", "Sucesso", "Bilionários", "Visão", "Mindset", "Startups", "Líderes", "Mestres"],
        negociosSuffix: ["Milionário", "Sem Crise", "Ativo", "Brasil", "B2B", "Digital", "Online", "Prosperidade", "Crescimento", "Oportunidades", "do Futuro", "Bilionário", "de Sucesso", "Global", "de Elite", "Pro"],

        engracados: ["Hospício", "Asilos", "Os Últimos", "Tropa", "Clube", "Karens", "Gados", "Trouxas", "Decepção", "Desastre", "Só Decepção", "Faliu", "Buracos", "Lixo", "Sem Rumo", "Sem Freio", "Os Excluídos"],
        engracadosSuffix: ["Solteiros", "Bebados", "Sem Rumo", "Cancelados", "Fudidos", "da Depressão", "Falidos", "Sem Salvação", "Ninguém Lê", "SOS", "na UTI", "Sem Teto", "Desempregados", "Caloteiros", "Chifrudos", "Lascardos"],
    },

    selectCategory(id) {
      this.selectedCategory = id;
      this.generatedList = [];
    },

    getRandom(arr) {
        return arr[Math.floor(Math.random() * arr.length)];
    },

    generateNames() {
      this.isGenerating = true;
      this.generatedList = [];
      
      setTimeout(() => {
        let prefixArr = this.wordBanks[this.selectedCategory];
        let suffixArr = this.wordBanks[this.selectedCategory + 'Suffix'];
        let newNames = new Set();
        let maxAttempts = 0;
        
        // Gerar 50 nomes únicos ou tentar até 1000 vezes (previne loop infinito)
        while(newNames.size < 50 && maxAttempts < 1000) {
            maxAttempts++;
            let p = this.getRandom(prefixArr);
            let s = this.getRandom(suffixArr);
            let name = p + " " + s;
            
            // Emoji aleatório
            const emojis = ['🚀','🔥','⭐','💥','🍺','🎮','📚','💰','🤣','😎','❤️','👑'];
            const emoji = this.getRandom(emojis);

            if(this.useCity && this.cityText.trim() !== '') {
                name += " " + this.cityText.trim();
            }
            if(this.useYear) {
                name += " 2026";
            }

            // Alternar posições do emoji (inicio, fim, ambos)
            let roll = Math.random();
            if(roll < 0.3) name = emoji + " " + name;
            else if(roll < 0.6) name = name + " " + emoji;
            else name = emoji + " " + name + " " + emoji;

            newNames.add(name);
        }

        this.generatedList = Array.from(newNames);
        this.isGenerating = false;
      }, 400); // pequeno delay para efeito visual de "pensando"
    },

    copyName(name) {
      navigator.clipboard.writeText(name).then(() => {
        this.copiedToast = true;
        setTimeout(() => this.copiedToast = false, 2000);
      });
    }
  }));
});
</script>
@endpush
@endsection



