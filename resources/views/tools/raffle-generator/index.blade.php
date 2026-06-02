@extends('layouts.tools')

@section('navbar_color', 'bg-[#065f46]')

@section('title', 'Gerador de Sorteios para Grupos de WhatsApp | WhatsGrupos')
@section('description', 'Faça sorteios justos para grupos de WhatsApp com centenas de membros! Cole a lista de participantes, escolha os vencedores e sorteie com roleta animada ou aleatorizador instantâneo.')

@section('tool_icon')
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 sm:w-8 sm:h-8">
    <path fill-rule="evenodd"
      d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2Zm0 5a1 1 0 0 1 1 1v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 0 1 1-1Z"
      clip-rule="evenodd" />
  </svg>
@endsection

@section('tool_logo_html')
  GERADOR<span class="text-emerald-100 font-bold">SORTEIO</span>
@endsection

@section('tool_action_btn')
  <a href="{{ route('tools.raffle-generator') }}"
    class="bg-white text-[#25D366] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
      <path
        d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2Zm0 3a7 7 0 1 1 0 14A7 7 0 0 1 12 5Zm-1 3v3H8a1 1 0 1 0 0 2h3v3a1 1 0 1 0 2 0v-3h3a1 1 0 1 0 0-2h-3V8a1 1 0 1 0-2 0Z" />
    </svg>
    Novo Sorteio
  </a>
@endsection

@section('tool_mobile_home')
  <a href="{{ route('tools.raffle-generator') }}"
    class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('ferramentas/gerador-de-sorteios*') ? 'text-[#25D366] bg-green-50' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
      class="w-6 h-6">
      <circle cx="12" cy="12" r="10" />
      <path d="M12 8v4l3 3" />
    </svg>
    <span class="text-[10px] font-bold mt-0.5">Sorteio</span>
  </a>
@endsection

@section('tool_mobile_fab')
  <a href="{{ route('tools.raffle-generator') }}"
    class="flex flex-col items-center justify-center -translate-y-4 relative">
    <div
      class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-white">
        <path
          d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2Zm0 3a7 7 0 1 1 0 14A7 7 0 0 1 12 5Zm-1 3v3H8a1 1 0 1 0 0 2h3v3a1 1 0 1 0 2 0v-3h3a1 1 0 1 0 0-2h-3V8a1 1 0 1 0-2 0Z" />
      </svg>
    </div>
    <span class="text-[10px] font-bold mt-1 text-slate-600">Sortear</span>
  </a>
@endsection

@section('canonical', route('tools.raffle-generator'))

@section('content')

<x-seo.tool name="Gerador de Sorteios para WhatsApp"
            description="Realize sorteios justos e transparentes para o seu grupo de WhatsApp, com resultado verificável." />

  <div class="py-8 md:py-12" x-data="raffleGenerator()">

    {{-- Hero --}}
    <div class="text-center max-w-2xl mx-auto mb-10">
      <div
        class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-orange-50 border-2 border-orange-100 text-orange-600 mb-6 shadow-[0_0_25px_rgba(234,88,12,0.2)] rotate-3">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 -rotate-3">
          <path fill-rule="evenodd"
            d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2Zm-1 6a1 1 0 0 1 2 0v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8Z"
            clip-rule="evenodd" />
        </svg>
      </div>
      <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">
        Gerador de <span
          class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-orange-600">Sorteios</span>
      </h1>
      <p class="text-slate-500 text-lg">Cole a lista de membros do grupo, escolha a quantidade de vencedores e sorteie em
        segundos — funciona com <strong>200+ participantes</strong>.</p>
    </div>

    <div class="max-w-[1100px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">

      {{-- ===================== ESQUERDA: Configuração ===================== --}}
      <div class="lg:col-span-5 space-y-5">

        {{-- STEP 1: Importar Lista --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
          <h2 class="text-lg font-bold text-slate-800 mb-1 flex items-center gap-2">
            <span
              class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center flex-shrink-0">1</span>
            Importar Participantes
          </h2>
          <p class="text-xs text-slate-400 font-medium mb-4 ml-8">Cole um nome por linha ou separe por vírgula. Funciona
            com 200+ nomes.</p>

          {{-- Título do Sorteio --}}
          <div class="mb-5">
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Nome do Sorteio
              (Opcional)</label>
            <input type="text" x-model="drawTitle" placeholder="Ex: Sorteio Camisa Oficial"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-medium text-slate-700 text-sm outline-none transition-colors">
          </div>

          {{-- Tabs de entrada --}}
          <div class="flex flex-wrap gap-1.5 mb-3 bg-slate-100 rounded-xl p-1">
            <button @click="inputMode = 'paste'"
              :class="inputMode === 'paste' ? 'bg-white shadow text-slate-800' : 'text-slate-500 hover:text-slate-700'"
              class="flex-1 min-w-[90px] py-1.5 px-2 rounded-lg text-xs font-bold transition-all">
              📋 Colar
            </button>
            <button @click="inputMode = 'manual'"
              :class="inputMode === 'manual' ? 'bg-white shadow text-slate-800' : 'text-slate-500 hover:text-slate-700'"
              class="flex-1 min-w-[90px] py-1.5 px-2 rounded-lg text-xs font-bold transition-all">
              ✏️ Digitar
            </button>
            <label
              :class="inputMode === 'csv' ? 'bg-emerald-600 shadow text-white' : 'text-slate-500 hover:text-slate-700 bg-slate-200/50 cursor-pointer'"
              class="flex-1 min-w-[90px] py-1.5 px-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1">
              <span>📁 CSV</span>
              <input type="file" accept=".csv" class="hidden" @change="importCSV($event)">
            </label>
          </div>

          {{-- Modo Colar --}}
          <div x-show="inputMode === 'paste'" x-transition>
            <textarea x-model="pasteText" @input="parsePaste()" rows="7"
              placeholder="Cole aqui a lista de membros:&#10;&#10;Ana Silva&#10;Carlos Mendes&#10;João Pedro&#10;Maria Lima&#10;Pedro Rocha&#10;...&#10;&#10;💡 Pode separar por vírgula, ponto e vírgula, ou uma por linha."
              class="w-full p-3 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-0 transition-all font-medium text-slate-700 text-sm outline-none resize-none"></textarea>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs text-slate-400">
                <span class="font-black text-emerald-700" x-text="validItems.length"></span> participante(s) detectado(s)
              </span>
              <button @click="pasteText = ''; items = []" x-show="pasteText.length > 0"
                class="text-xs text-red-400 hover:text-red-600 font-bold transition-colors">
                Limpar
              </button>
            </div>
          </div>

          {{-- Modo Manual --}}
          <div x-show="inputMode === 'manual'" x-transition>
            <div class="space-y-2 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
              <template x-for="(item, index) in items" :key="index">
                <div class="flex items-center gap-2">
                  <span
                    class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black flex items-center justify-center"
                    x-text="index + 1"></span>
                  <input type="text" x-model="items[index]" :placeholder="'Participante ' + (index + 1)"
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-2 font-medium transition-colors outline-none">
                  <button @click="items.splice(index, 1)" :disabled="items.length <= 2"
                    class="p-1.5 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                      <path fill-rule="evenodd"
                        d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                        clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </template>
            </div>
            <button @click="items.push('')"
              class="mt-3 flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-3 py-2 rounded-xl hover:bg-emerald-100 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path
                  d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
              </svg>
              Adicionar linha
            </button>
          </div>
        </div>

        {{-- STEP 2: Configurar Sorteio --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
          <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span
              class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center flex-shrink-0">2</span>
            Configurar Sorteio
          </h2>

          {{-- Modo --}}
          <div class="mb-4">
            <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Modo</p>
            <div class="grid grid-cols-2 gap-2">
              <button @click="mode = 'roulette'"
                :class="mode === 'roulette' ? 'border-emerald-600 bg-emerald-50 text-emerald-800' : 'border-slate-200 text-slate-600 hover:border-emerald-300'"
                class="border-2 rounded-xl p-3 flex flex-col items-center gap-1.5 transition-all font-bold text-xs">
                <span class="text-2xl">🎡</span> Roleta Animada
              </button>
              <button @click="mode = 'random'"
                :class="mode === 'random' ? 'border-emerald-600 bg-emerald-50 text-emerald-800' : 'border-slate-200 text-slate-600 hover:border-emerald-300'"
                class="border-2 rounded-xl p-3 flex flex-col items-center gap-1.5 transition-all font-bold text-xs">
                <span class="text-2xl">⚡</span> Aleatorizador Rápido
              </button>
            </div>
          </div>

          {{-- Número de vencedores --}}
          <div class="mb-4">
            <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Número de Vencedores</p>
            <div class="flex items-center gap-3">
              <button @click="winners = Math.max(1, winners - 1)"
                class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-emerald-100 text-slate-700 font-bold text-lg flex items-center justify-center transition-colors">−</button>
              <span class="text-3xl font-black text-emerald-700 w-10 text-center" x-text="winners"></span>
              <button @click="winners = Math.min(Math.max(1, validItems.length - 1), winners + 1)"
                class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-emerald-100 text-slate-700 font-bold text-lg flex items-center justify-center transition-colors">+</button>
              <span class="text-sm text-slate-400 font-medium">vencedor(es)</span>
            </div>
          </div>

          {{-- Configurações Extras --}}
          <div class="mt-4 flex flex-col gap-3">
            <label
              class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
              <input type="checkbox" x-model="allowRepeats"
                class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
              <span class="text-sm font-medium text-slate-700">Permitir repetição de vencedor</span>
            </label>
            <label
              class="flex items-center gap-3 p-3 rounded-xl border border-orange-200 bg-orange-50/50 cursor-pointer hover:bg-orange-50 transition-colors">
              <input type="checkbox" x-model="saveDraw"
                class="w-4 h-4 text-orange-600 rounded border-orange-300 focus:ring-orange-500">
              <div class="flex flex-col">
                <span class="text-sm font-bold text-slate-800">Salvar Sorteio Oficialmente</span>
                <span class="text-[10px] text-slate-500 font-medium">Gera URL de Comprovação e Histórico Auditável.</span>
              </div>
            </label>
            <label
              class="flex items-center gap-3 cursor-pointer group p-2.5 bg-slate-50 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors">
              <input type="checkbox" x-model="ignoreDuplicates"
                class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
              <span class="text-sm font-medium text-slate-600 group-hover:text-slate-800">Ignorar nomes duplicados</span>
            </label>
          </div>
        </div>

        {{-- STEP 3: Sortear --}}
        <button @click="startDraw()" :disabled="spinning || validItems.length < 2"
          class="w-full bg-gradient-to-b from-emerald-600 to-emerald-800 hover:from-emerald-500 hover:to-emerald-700 text-white font-black text-xl py-5 px-6 rounded-2xl shadow-xl shadow-emerald-800/30 transition-all flex items-center justify-center gap-3 hover:-translate-y-0.5 active:translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7"
            :class="spinning ? 'animate-spin' : ''">
            <path fill-rule="evenodd"
              d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2Zm-1 6a1 1 0 0 1 2 0v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8Z"
              clip-rule="evenodd" />
          </svg>
          <span
            x-text="spinning ? 'Sorteando...' : (validItems.length < 2 ? 'Adicione participantes ↑' : '🎲 Iniciar Sorteio')"></span>
        </button>

        {{-- Info --}}
        <div x-show="validItems.length >= 2"
          class="flex items-center gap-2 justify-center text-xs text-slate-400 font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
            class="w-4 h-4 text-emerald-500">
            <path fill-rule="evenodd"
              d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z"
              clip-rule="evenodd" />
          </svg>
          <span x-text="validItems.length + ' participante(s) prontos para o sorteio'"></span>
        </div>

      </div>

      {{-- ===================== DIREITA: Resultado ===================== --}}
      <div class="lg:col-span-7 space-y-5">

        {{-- ROLETA --}}
        <div x-show="mode === 'roulette'" class="bg-slate-900 rounded-2xl shadow-xl p-6 relative overflow-hidden">
          <div
            class="absolute top-0 right-0 w-48 h-48 bg-emerald-700/20 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
          </div>
          <div
            class="absolute bottom-0 left-0 w-48 h-48 bg-teal-800/20 rounded-full blur-3xl -ml-16 -mb-16 pointer-events-none">
          </div>

          <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2 relative z-10">
            <span class="text-emerald-400">🎡</span> Roleta Animada
            <span class="ml-auto text-xs bg-emerald-900/60 text-emerald-300 font-bold px-2 py-1 rounded-full"
              x-text="validItems.length + ' nomes'"></span>
          </h2>

          <div class="flex items-center justify-center relative z-10">
            <div class="relative">
              <div class="absolute top-1/2 -right-2 -translate-y-1/2 z-20 drop-shadow-lg">
                <div
                  class="w-0 h-0 border-y-[14px] border-y-transparent border-l-[22px] border-l-white filter drop-shadow-md">
                </div>
              </div>
              <canvas id="rouletteCanvas" width="300" height="300"
                class="rounded-full border-4 border-emerald-900/60 shadow-2xl shadow-emerald-950/50"></canvas>
            </div>
          </div>

          {{-- Resultado Roleta --}}
          <div x-show="result.length > 0 && !spinning" class="mt-5 relative z-10"
            x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100">
            <div
              class="bg-gradient-to-r from-emerald-900/60 to-teal-900/60 border border-emerald-700/50 rounded-2xl p-4 text-center shadow-lg">
              <div class="flex items-center justify-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <p class="text-emerald-300 text-[10px] font-black uppercase tracking-widest">Sorteio Oficializado</p>
              </div>
              <template x-for="(winner, i) in result" :key="i">
                <div class="flex items-center justify-center gap-2 py-1">
                  <span class="text-yellow-400 text-lg" x-text="i === 0 ? '🥇' : (i === 1 ? '🥈' : '🥉')"></span>
                  <p class="text-white text-xl font-black" x-text="winner"></p>
                </div>
              </template>
              <div x-show="currentDrawUrl" class="mt-3 pt-3 border-t border-emerald-800/50">
                <p class="text-[10px] text-slate-400 font-medium mb-1">🔗 Link Oficial do Sorteio (Copiado
                  automaticamente):</p>
                <a :href="currentDrawUrl" target="_blank"
                  class="text-xs font-bold text-emerald-400 hover:text-emerald-300 transition-colors break-all"
                  x-text="currentDrawUrl"></a>
              </div>
            </div>
          </div>

          {{-- Placeholder roleta vazia --}}
          <div x-show="validItems.length < 2 && !spinning" class="mt-4 text-center relative z-10">
            <p class="text-slate-500 text-sm font-medium">↑ Adicione ao menos 2 participantes</p>
          </div>
        </div>

        {{-- ALEATORIZADOR --}}
        <div x-show="mode === 'random'" class="bg-slate-900 rounded-2xl shadow-xl p-6 relative overflow-hidden">
          <div
            class="absolute top-0 right-0 w-48 h-48 bg-emerald-700/20 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
          </div>
          <div
            class="absolute bottom-0 left-0 w-48 h-48 bg-teal-800/20 rounded-full blur-3xl -ml-16 -mb-16 pointer-events-none">
          </div>

          <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2 relative z-10">
            <span class="text-emerald-400">⚡</span> Resultado do Sorteio
            <span class="ml-auto text-xs bg-emerald-900/60 text-emerald-300 font-bold px-2 py-1 rounded-full"
              x-text="validItems.length + ' nomes'"></span>
          </h2>

          <div class="relative z-10">
            {{-- Placeholder --}}
            <template x-if="!spinning && shuffledAll.length === 0 && validItems.length < 2">
              <div class="flex flex-col items-center justify-center h-48 text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                  stroke="currentColor" class="w-14 h-14 mb-3 opacity-30">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <p class="text-sm font-bold">Adicione participantes e sorteie</p>
              </div>
            </template>

            {{-- Aguardando --}}
            <template x-if="!spinning && shuffledAll.length === 0 && validItems.length >= 2">
              <div class="flex flex-col items-center justify-center h-48 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="w-14 h-14 mb-3 opacity-30">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112Z" />
                </svg>
                <p class="text-sm font-bold" x-text="validItems.length + ' participantes prontos. Clique em Sortear!'">
                </p>
              </div>
            </template>

            {{-- Loading embaralhando --}}
            <template x-if="spinning && mode === 'random'">
              <div class="space-y-2">
                <template x-for="n in Math.min(6, validItems.length)" :key="n">
                  <div class="h-10 bg-emerald-900/30 rounded-xl animate-pulse"
                    :style="'animation-delay:' + (n * 80) + 'ms'"></div>
                </template>
              </div>
            </template>

            {{-- Resultado lista --}}
            <template x-if="!spinning && shuffledAll.length > 0">
              <div>
                {{-- Vencedores destaque --}}
                <div class="mb-4 space-y-2">
                  <div x-show="currentDrawUrl"
                    class="flex items-center justify-center gap-2 mb-3 px-3 py-2 bg-emerald-900/40 border border-emerald-800/50 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                      class="w-4 h-4 text-emerald-400">
                      <path fill-rule="evenodd"
                        d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                        clip-rule="evenodd" />
                    </svg>
                    <span class="text-[10px] font-black text-emerald-300 uppercase tracking-widest">Verificado e
                      Registrado</span>
                  </div>
                  <template x-for="(item, idx) in shuffledAll.slice(0, winners)" :key="'w' + idx">
                    <div
                      class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-700/60 to-teal-700/60 border border-emerald-600/50"
                      x-transition:enter="transition ease-out duration-300"
                      :style="'transition-delay:' + (idx * 60) + 'ms'">
                      <span class="text-xl"
                        x-text="idx === 0 ? '🥇' : (idx === 1 ? '🥈' : (idx === 2 ? '🥉' : '🏅'))"></span>
                      <span class="font-black text-white flex-1 text-base" x-text="item"></span>
                      <span class="text-[10px] font-black text-emerald-300 bg-emerald-900/40 px-2 py-0.5 rounded-full"
                        x-text="(idx + 1) + 'º lugar'"></span>
                    </div>
                  </template>
                </div>

                {{-- Demais (colapsável) --}}
                <template x-if="shuffledAll.length > winners">
                  <div>
                    <button @click="showAll = !showAll"
                      class="w-full text-xs text-slate-500 hover:text-slate-300 font-bold py-2 flex items-center justify-center gap-1 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-4 h-4 transition-transform" :class="showAll ? 'rotate-180' : ''">
                        <path fill-rule="evenodd"
                          d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z"
                          clip-rule="evenodd" />
                      </svg>
                      <span
                        x-text="showAll ? 'Ocultar demais' : 'Ver todos os ' + shuffledAll.length + ' participantes na ordem'"></span>
                    </button>
                    <div x-show="showAll" x-transition class="space-y-1 max-h-64 overflow-y-auto custom-scrollbar mt-2">
                      <template x-for="(item, idx) in shuffledAll.slice(winners)" :key="'o' + idx">
                        <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-800/60">
                          <span
                            class="w-6 h-6 rounded-full bg-slate-700 text-slate-400 text-[10px] font-black flex items-center justify-center flex-shrink-0"
                            x-text="winners + idx + 1"></span>
                          <span class="text-sm text-slate-400 font-medium" x-text="item"></span>
                        </div>
                      </template>
                    </div>
                  </div>
                </template>
              </div>
            </template>
          </div>
        </div>

        {{-- Ações pós-sorteio --}}
        <div x-show="result.length > 0 && !spinning" class="flex flex-col sm:flex-row gap-3"
          x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
          x-transition:enter-end="opacity-100 translate-y-0">
          <button @click="copyResult()"
            class="flex-1 bg-white border-2 border-emerald-200 hover:border-emerald-400 text-emerald-800 font-bold text-sm py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
              <path
                d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z" />
              <path
                d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z" />
            </svg>
            <span x-text="copied ? '✅ Copiado!' : 'Copiar Resultado'"></span>
          </button>
          <button @click="startDraw()" :disabled="spinning"
            class="flex-1 bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-sm py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
              <path fill-rule="evenodd"
                d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                clip-rule="evenodd" />
            </svg>
            Sortear Novamente
          </button>
          <button @click="resetAll()"
            class="sm:w-auto bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
              <path fill-rule="evenodd"
                d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                clip-rule="evenodd" />
            </svg>
            Zerar
          </button>
        </div>

        {{-- Histórico Auditoria --}}
        <div class="space-y-4">

          {{-- Buscar Sorteio por Código --}}
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <h3 class="text-sm font-black text-slate-700 mb-2 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                class="w-5 h-5 text-slate-400">
                <path fill-rule="evenodd"
                  d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                  clip-rule="evenodd" />
              </svg>
              Verificar Certificado
            </h3>
            <p class="text-xs text-slate-500 mb-3">Possui o código de um sorteio? Digite-o para ver o resultado oficial.
            </p>
            <form action="{{ route('tools.raffle.search') }}" method="GET" class="flex gap-2">
              <input type="text" name="code" placeholder="Ex: 9E8F8B2C" required
                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 focus:ring-orange-500 focus:border-orange-500 font-mono text-sm outline-none transition-colors uppercase">
              <button type="submit"
                class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition-colors shadow-sm">Buscar</button>
            </form>
            @if(session('error'))
              <p class="text-[10px] text-red-500 mt-2 font-bold">{{ session('error') }}</p>
            @endif
          </div>

          <div x-show="history.length > 0" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <h3 class="text-sm font-black text-slate-700 mb-4 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                  class="w-5 h-5 text-emerald-600">
                  <path fill-rule="evenodd"
                    d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z"
                    clip-rule="evenodd" />
                </svg>
                Auditoria e Registros Oficiais
              </div>
              <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md"
                x-text="history.length + ' salvo(s)'"></span>
            </h3>
            <div class="space-y-3 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
              <template x-for="(entry, idx) in history.slice().reverse()" :key="idx">
                <div
                  class="border border-slate-100 bg-slate-50/50 rounded-xl p-3 flex flex-col gap-2 transition-colors hover:bg-slate-50">
                  <div class="flex justify-between items-start gap-2">
                    <div class="flex-1">
                      <p class="text-xs font-black text-slate-800 mb-0.5" x-text="entry.title"></p>
                      <p class="text-[10px] text-slate-500 font-medium font-mono">ID: <span x-text="entry.hash"></span>
                      </p>
                    </div>
                    <span
                      class="text-[10px] font-bold text-slate-400 bg-white border border-slate-200 px-1.5 py-0.5 rounded shadow-sm shrink-0"
                      x-text="entry.time"></span>
                  </div>

                  <div
                    class="flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50/50 rounded-lg p-2 border border-emerald-100/50">
                    <span class="text-base" x-text="entry.mode === 'roulette' ? '🎡' : '⚡'"></span>
                    <span class="truncate" x-text="entry.winners"></span>
                  </div>

                  <div class="flex items-center justify-between mt-1">
                    <span class="text-[10px] font-bold text-slate-400" x-text="entry.total + ' participantes'"></span>
                    <a x-show="entry.url" :href="entry.url" target="_blank"
                      class="text-[10px] font-black text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                      Ver Oficial <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-3 h-3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                      </svg>
                    </a>
                  </div>
                </div>
              </template>
            </div>
          </div>

        </div>
      </div>


    </div>
    {{-- AdSense Slot Intermediário --}}
      <div class="max-w-[1100px] mx-auto mt-10">
        <x-adsense class="my-4" />
      </div>
      <div class="mt-12 max-w-4xl mx-auto">
        <x-publish-invite />
      </div>
  </div>
  @push('head')
    <style>
      .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
      }

      .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
      }

      .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
      }
    </style>
  @endpush

  @push('scripts')
    <script>
      document.addEventListener('alpine:init', () => {
        Alpine.data('raffleGenerator', () => ({
          // Estado principal
          drawTitle: '',
          inputMode: 'paste',
          mode: 'roulette',
          pasteText: '',
          items: ['Ana Silva', 'Carlos Mendes', 'João Pedro', 'Maria Lima', 'Pedro Rocha'],
          winners: 1,
          allowRepeats: false,
          ignoreDuplicates: true,
          saveDraw: true,

          // Controle UI
          spinning: false,
          result: [],
          shuffledAll: [],
          showAll: false,
          copied: false,
          history: [],
          currentDrawUrl: null,

          // Roleta Canvas
          canvas: null,
          ctx: null,
          currentAngle: 0,
          animationId: null,

          // Paleta verde/teal para as fatias
          sliceColors: [
            '#065f46', '#047857', '#059669', '#0f766e',
            '#0d9488', '#0e7490', '#0369a1', '#1d4ed8',
            '#4338ca', '#7c3aed', '#6d28d9', '#be185d',
          ],

          // Computed: participantes válidos
          get validItems() {
            let list = this.inputMode === 'paste' ? this.items : this.items.filter(i => i.trim() !== '');
            if (this.ignoreDuplicates) {
              const seen = new Set();
              list = list.filter(i => {
                const k = i.trim().toLowerCase();
                if (!k || seen.has(k)) return false;
                seen.add(k);
                return true;
              });
            } else {
              list = list.filter(i => i.trim() !== '');
            }
            return list;
          },

          init() {
            this.$nextTick(() => {
              this.canvas = document.getElementById('rouletteCanvas');
              if (this.canvas) {
                this.ctx = this.canvas.getContext('2d');
                this.drawRoulette(this.currentAngle);
              }
            });
          },

          // ─── IMPORTAÇÃO ────────────────────────────────────────
          parsePaste() {
            const raw = this.pasteText;
            if (!raw.trim()) { this.items = []; return; }

            let names;
            // Detecta separador: vírgula, ponto-e-vírgula, ou quebra de linha
            if (raw.includes('\n')) {
              names = raw.split('\n');
            } else if (raw.includes(';')) {
              names = raw.split(';');
            } else {
              names = raw.split(',');
            }

            names = names
              .map(n => n.replace(/^[\d\.\-\*\#\s]+/, '').trim()) // Remove numeração (1. 2. etc)
              .filter(n => n.length > 0);

            this.items = names;
            this.$nextTick(() => this.drawRoulette(this.currentAngle));
          },

          importCSV(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
              const text = e.target.result;
              this.inputMode = 'paste';
              this.pasteText = text;
              this.parsePaste();
              event.target.value = ''; // reseta input file
            };
            reader.readAsText(file);
          },

          // ─── ROLETA CANVAS ─────────────────────────────────────
          drawRoulette(rotation, highlightIndex = -1) {
            if (!this.ctx) return;
            const items = this.validItems;
            if (items.length < 2) {
              // Desenha roleta vazia/placeholder
              const cx = 150, cy = 150, r = 146;
              this.ctx.clearRect(0, 0, 300, 300);
              this.ctx.beginPath();
              this.ctx.arc(cx, cy, r, 0, 2 * Math.PI);
              this.ctx.fillStyle = '#1e293b';
              this.ctx.fill();
              this.ctx.fillStyle = '#334155';
              this.ctx.font = 'bold 13px Inter, sans-serif';
              this.ctx.textAlign = 'center';
              this.ctx.textBaseline = 'middle';
              this.ctx.fillText('Adicione participantes', cx, cy - 10);
              this.ctx.fillText('para ver a roleta', cx, cy + 12);
              return;
            }

            const canvas = this.canvas;
            const ctx = this.ctx;
            const cx = canvas.width / 2;
            const cy = canvas.height / 2;
            const r = Math.min(cx, cy) - 4;
            const slice = (2 * Math.PI) / items.length;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Sombra central
            ctx.beginPath();
            ctx.arc(cx, cy, r, 0, 2 * Math.PI);
            ctx.shadowColor = 'rgba(0,0,0,0.5)';
            ctx.shadowBlur = 20;
            ctx.fillStyle = '#0f172a';
            ctx.fill();
            ctx.shadowBlur = 0;

            items.forEach((item, i) => {
              const startAngle = rotation + i * slice;
              const endAngle = startAngle + slice;

              // Fatia
              ctx.beginPath();
              ctx.moveTo(cx, cy);
              ctx.arc(cx, cy, r, startAngle, endAngle);
              ctx.closePath();
              ctx.fillStyle = this.sliceColors[i % this.sliceColors.length];

              // Destaque vencedor
              if (highlightIndex === i) {
                ctx.fillStyle = '#f0fdf4';
              }
              ctx.fill();

              // Borda separadora
              ctx.strokeStyle = 'rgba(255,255,255,0.15)';
              ctx.lineWidth = 1.5;
              ctx.stroke();

              // Texto
              ctx.save();
              ctx.translate(cx, cy);
              ctx.rotate(startAngle + slice / 2);
              ctx.textAlign = 'right';
              ctx.fillStyle = highlightIndex === i ? '#065f46' : '#ffffff';
              const fontSize = Math.max(8, Math.min(13, 200 / items.length));
              ctx.font = `bold ${fontSize}px Inter, system-ui, sans-serif`;
              ctx.shadowColor = 'rgba(0,0,0,0.6)';
              ctx.shadowBlur = 3;
              const maxLen = Math.max(6, Math.floor(18 - items.length * 0.05));
              const label = item.length > maxLen ? item.substring(0, maxLen - 1) + '…' : item;
              ctx.fillText(label, r - 10, fontSize / 3);
              ctx.restore();
            });

            // Círculo central
            const gradient = ctx.createRadialGradient(cx, cy, 0, cx, cy, 22);
            gradient.addColorStop(0, '#065f46');
            gradient.addColorStop(1, '#022c22');
            ctx.beginPath();
            ctx.arc(cx, cy, 22, 0, 2 * Math.PI);
            ctx.fillStyle = gradient;
            ctx.fill();
            ctx.strokeStyle = 'rgba(255,255,255,0.3)';
            ctx.lineWidth = 2;
            ctx.stroke();

            ctx.fillStyle = '#6ee7b7';
            ctx.font = 'bold 14px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.shadowBlur = 0;
            ctx.fillText('🎲', cx, cy);
          },

          // ─── SORTEIO ───────────────────────────────────────────
          startDraw() {
            const items = this.validItems;
            if (items.length < 2 || this.spinning) return;

            this.spinning = true;
            this.result = [];
            this.shuffledAll = [];
            this.showAll = false;
            this.currentDrawUrl = null;

            if (this.mode === 'roulette') {
              this.spinRoulette(items);
            } else {
              this.randomizeList(items);
            }
          },

          spinRoulette(items) {
            if (!this.ctx) {
              this.canvas = document.getElementById('rouletteCanvas');
              this.ctx = this.canvas?.getContext('2d');
            }

            // Sorteia o índice do vencedor principal
            const winnerIndex = Math.floor(Math.random() * items.length);
            const slice = (2 * Math.PI) / items.length;

            const totalSpins = (6 + Math.random() * 4) * 2 * Math.PI;
            // Posiciona o vencedor na ponta do ponteiro (ângulo 0 = direita)
            const targetAngle = totalSpins - (winnerIndex * slice) - (slice / 2);

            const startAngle = this.currentAngle;
            const startTime = performance.now();
            const duration = 4000 + Math.random() * 2000;

            // Ease out quártico
            const easeOut = t => 1 - Math.pow(1 - t, 4);

            const animate = (now) => {
              const t = Math.min((now - startTime) / duration, 1);
              this.currentAngle = startAngle + targetAngle * easeOut(t);
              this.drawRoulette(this.currentAngle);

              if (t < 1) {
                this.animationId = requestAnimationFrame(animate);
              } else {
                this.currentAngle = startAngle + targetAngle;
                this.drawRoulette(this.currentAngle, winnerIndex);
                this.finishRouletteDrawn(items, winnerIndex);
              }
            };
            this.animationId = requestAnimationFrame(animate);
          },

          finishRouletteDrawn(items, winnerIndex) {
            const winners = [];
            const pool = [...items];
            const rouletteWinner = items[winnerIndex];

            // 1º: sempre o da roleta
            winners.push(rouletteWinner);
            pool.splice(pool.indexOf(rouletteWinner), 1);

            // Demais: sorteio sem repetição
            for (let w = 1; w < Math.min(this.winners, items.length); w++) {
              if (pool.length === 0) break;
              const idx = Math.floor(Math.random() * pool.length);
              winners.push(pool[idx]);
              if (!this.allowRepeats) pool.splice(idx, 1);
            }

            this.result = winners;
            this.spinning = false;
            this.addHistory(items.length);
          },

          randomizeList(items) {
            let frame = 0;
            const totalFrames = 25;

            const tick = () => {
              // Embaralha visual temporário
              const temp = [...items];
              for (let i = temp.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [temp[i], temp[j]] = [temp[j], temp[i]];
              }
              this.shuffledAll = [...temp];
              frame++;

              if (frame < totalFrames) {
                setTimeout(tick, 50 + frame * 2);
              } else {
                // Resultado final
                const final = this.fisherYates([...items]);
                this.shuffledAll = final;
                this.result = final.slice(0, Math.min(this.winners, final.length));
                this.addHistory(items.length);
              }
            };
            setTimeout(tick, 30);
          },

          fisherYates(arr) {
            for (let i = arr.length - 1; i > 0; i--) {
              const j = Math.floor(Math.random() * (i + 1));
              [arr[i], arr[j]] = [arr[j], arr[i]];
            }
            return arr;
          },

          async addHistory(total) {
            const time = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const winnersList = this.result.join(', ');
            const title = this.drawTitle.trim() || 'Sorteio WhatsApp';

            let uuid = '...';
            let url = '';

            // Se usuário optou por não salvar, apenas mostra local
            if (!this.saveDraw) {
              this.history.push({ title, time, winners: winnersList, total, mode: this.mode, hash: 'Não Salvo', url: '' });
              this.spinning = false;
              if (this.history.length > 20) this.history.shift();
              return;
            }

            // UI update otimista
            const historyItem = { title, time, winners: winnersList, total, mode: this.mode, hash: 'Salvando...', url: '' };
            this.history.push(historyItem);

            try {
              const response = await fetch('{{ route('tools.raffle-generator.store') }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                  title: title,
                  participants: this.validItems,
                  winners: this.result,
                  winner_count: this.winners,
                  mode: this.mode,
                  total_participants: total
                })
              });

              const data = await response.json();

              if (data.uuid) {
                uuid = data.uuid.split('-')[0].toUpperCase();
                url = data.url;
                this.currentDrawUrl = url;
                historyItem.hash = uuid;
                historyItem.url = url;
                // Copia pro clipboard automaticamente a URL pro usuário ver
                navigator.clipboard.writeText(url).catch(e => { });
              }
            } catch (err) {
              historyItem.hash = 'ERRO-OFFLINE';
            }

            this.spinning = false;
            if (this.history.length > 20) this.history.shift();
          },

          copyResult() {
            const items = this.validItems;
            let text = `🎲 *RESULTADO DO SORTEIO: ${this.drawTitle || 'Grupo'}*\n`;
            text += `👥 Participantes: ${items.length}\n`;
            text += `📅 Data: ${new Date().toLocaleDateString('pt-BR')} às ${new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}\n\n`;
            this.result.forEach((w, i) => {
              const medal = i === 0 ? '🥇' : (i === 1 ? '🥈' : (i === 2 ? '🥉' : '🏅'));
              text += `${medal} ${i + 1}º Lugar: *${w}*\n`;
            });
            if (this.currentDrawUrl) {
              text += `\n🔗 *Link de Auditoria Oficial:*\n${this.currentDrawUrl}\n`;
            }
            text += `\n_Sorteio certificado via WhatsGrupos_ 🎉`;

            navigator.clipboard.writeText(text).then(() => {
              this.copied = true;
              setTimeout(() => this.copied = false, 2500);
            });
          },

          resetAll() {
            this.result = [];
            this.shuffledAll = [];
            this.showAll = false;
            this.$nextTick(() => this.drawRoulette(this.currentAngle));
          },
        }));
      });
    </script>
  @endpush
@endsection