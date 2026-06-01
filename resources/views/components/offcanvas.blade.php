<style>
  .minimal-scrollbar::-webkit-scrollbar {
    width: 4px;
  }
  .minimal-scrollbar::-webkit-scrollbar-track {
    background: transparent;
  }
  .minimal-scrollbar::-webkit-scrollbar-thumb {
    background-color: #94a3b8;
    border-radius: 10px;
  }
  .minimal-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #64748b;
  }
</style>
<!-- Off-Canvas Menu Sidebar (Mobile & Desktop Sandwich) -->
  <div x-show="mobileMenuOpen" class="relative z-[100]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
    <!-- Overlay Escuro -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"
         @click="mobileMenuOpen = false"></div>

    <div class="fixed inset-0 overflow-hidden">
      <div class="absolute inset-0 overflow-hidden">
        <div class="pointer-events-none fixed inset-y-0 left-0 flex max-w-full pr-10">
          <!-- Painel Lateral -->
          <div x-show="mobileMenuOpen"
               x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="pointer-events-auto relative w-[85vw] max-w-[340px]">

            <!-- Conteúdo do Menu Lateral -->
            <div class="flex h-full flex-col overflow-hidden bg-white shadow-xl">

              <!-- Cabeçalho -->
              <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-slate-100 flex-shrink-0">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400" id="slide-over-title">Menu</span>
                <button @click="mobileMenuOpen = false" type="button" class="text-slate-400 hover:text-slate-700 transition-colors p-1.5 rounded-lg hover:bg-slate-100">
                  <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
              </div>

              <!-- Corpo com scroll -->
              <div class="flex-1 overflow-y-auto px-4 py-5 space-y-5 minimal-scrollbar">
              
                @if(request()->is('blog*'))
                <!-- Buscador do Blog -->
                <div class="mb-4">
                  <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-2 mb-1.5">Pesquisar Blog</p>
                  <form action="{{ route('blog.index') }}" method="GET" class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Ex: como criar grupo" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-[#25D366] focus:ring-1 focus:ring-[#25D366] text-sm text-slate-700 outline-none transition-all">
                    <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 hover:text-[#25D366] transition-colors">
                      <x-heroicon-o-magnifying-glass class="w-5 h-5"/>
                    </button>
                  </form>
                </div>
                @endif

                <!-- Principal -->
                <div>
                  <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-2 mb-1.5">Principal</p>
                  <nav class="flex flex-col gap-0.5">
                    <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('/') ? 'text-[#25D366] bg-green-50' : 'text-slate-700 hover:text-[#25D366] hover:bg-green-50' }}">
                      <x-heroicon-o-home class="w-5 h-5 flex-shrink-0"/> Grupos de WhatsApp
                    </a>
                    <a href="/enviar-grupo" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('enviar-grupo') ? 'text-[#25D366] bg-green-50' : 'text-slate-700 hover:text-[#25D366] hover:bg-green-50' }}">
                      <x-heroicon-o-plus-circle class="w-5 h-5 flex-shrink-0"/> Enviar Grupo
                    </a>
                    <a href="/meus-grupos" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('meus-grupos*') ? 'text-[#25D366] bg-green-50' : 'text-slate-700 hover:text-[#25D366] hover:bg-green-50' }}">
                      <x-heroicon-o-users class="w-5 h-5 flex-shrink-0"/> Meus Grupos
                    </a>
                    <a href="/pacotes-vip" class="mt-1 mb-1 flex items-center justify-between px-3 py-2 rounded-xl font-black text-sm transition-all duration-150 bg-gradient-to-b from-amber-400 to-amber-500 border border-amber-300/50 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.3),0_4px_0_#d97706,0_5px_10px_rgba(245,158,11,0.3)] hover:-translate-y-0.5 hover:shadow-[inset_0_1px_0_rgba(255,255,255,0.4),0_4px_0_#d97706,0_8px_15px_rgba(245,158,11,0.4)] active:translate-y-1 active:shadow-[inset_0_1px_0_rgba(255,255,255,0.1),0_0px_0_#d97706,0_0px_0_rgba(245,158,11,0)] group">
                      <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" class="w-5 h-5 flex-shrink-0 text-amber-100 drop-shadow-[0_1px_2px_rgba(0,0,0,0.5)] group-hover:scale-110 transition-transform">
                          <path d="M239.54,98.11l-36.88,86.07a16,16,0,0,1-14.66,9.82H68a16,16,0,0,1-14.66-9.82L16.46,98.11A8,8,0,0,1,24.63,86.3l57,21.36,39.11-65.18a8,8,0,0,1,13.72,0l39.11,65.18,57-21.36a8,8,0,0,1,8.17,11.81Z"></path>
                        </svg>
                        <span class="tracking-wide drop-shadow-[0_1px_2px_rgba(0,0,0,0.6)]">Impulsionar</span>
                      </div>
                      <div class="bg-white rounded-full p-1 shadow-md flex items-center justify-center group-hover:translate-x-1 transition-transform">
                        <x-heroicon-s-chevron-right class="w-3 h-3 text-amber-600 drop-shadow-sm" />
                      </div>
                    </a>
                  </nav>
                </div>

                <!-- Conteúdo -->
                <div>
                  <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-2 mb-1.5">Conteúdo</p>
                  <nav class="flex flex-col gap-0.5">
                    <a href="/blog" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('blog*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <x-heroicon-o-document-text class="w-5 h-5 flex-shrink-0 text-slate-400"/> Blog GruposWhats
                    </a>
                    <a href="/frases" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('frases*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5 flex-shrink-0 text-slate-400"/> 
                      Frases para Status
                    </a>
                    <a href="/minhas-frases" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('minhas-frases') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <x-heroicon-o-bookmark class="w-5 h-5 flex-shrink-0 text-slate-400"/> Minhas Frases
                    </a>
                  </nav>
                </div>

                <!-- Ferramentas -->
                <div>
                  <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-2 mb-1.5">Ferramentas</p>
                  <nav class="flex flex-col gap-0.5">
                    <a href="/figurinhas" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('figurinhas*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-sm">
                        <x-heroicon-o-face-smile class="w-4 h-4"/>
                      </div>
                      Figurinhas de Grupos
                    </a>
                    <a href="/ferramentas/analise-de-engajamento" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/analise*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-green-400 to-green-600 text-white shadow-sm">
                        <x-heroicon-o-sparkles class="w-4 h-4"/>
                      </div>
                      Análise de Engajamento
                    </a>
                    <a href="{{ route('tools.spam-detector') }}" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/detector-de-spam*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-red-400 to-red-600 text-white shadow-sm">
                        <x-heroicon-o-shield-exclamation class="w-4 h-4"/>
                      </div>
                      Detector de Spam
                    </a>
                    <a href="/ferramentas/gerador-de-regras" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/gerador-de-regras*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-sm">
                        <x-heroicon-o-document-check class="w-4 h-4"/>
                      </div>
                      Gerador de Regras
                    </a>
                    <a href="{{ route('tools.name-generator') }}" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/gerador-de-nomes*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 text-white shadow-sm">
                        <x-heroicon-o-sparkles class="w-4 h-4"/>
                      </div>
                      Gerador de Nomes
                    </a>
                    <a href="{{ route('tools.welcome-message') }}" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/mensagem-de-boas-vindas*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-teal-400 to-teal-600 text-white shadow-sm">
                        <x-heroicon-o-hand-raised class="w-4 h-4"/>
                      </div>
                      Msg de Boas-Vindas
                    </a>
                    <a href="{{ route('tools.link-validator') }}" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/verificador*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 text-white shadow-sm">
                        <x-heroicon-o-link class="w-4 h-4"/>
                      </div>
                      Verificador de Link
                    </a>
                    <a href="{{ route('tools.poll-generator') }}" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/gerador-de-enquete*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-pink-400 to-pink-600 text-white shadow-sm">
                        <x-heroicon-o-chart-bar class="w-4 h-4"/>
                      </div>
                      Gerador de Enquete
                    </a>
                    <a href="{{ route('tools.raffle-generator') }}" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/gerador-de-sorteios*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                          <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2Zm0 2a8 8 0 1 1 0 16A8 8 0 0 1 12 4Zm-1 4v3H8a1 1 0 1 0 0 2h3v3a1 1 0 1 0 2 0v-3h3a1 1 0 1 0 0-2h-3V8a1 1 0 1 0-2 0Z"/>
                        </svg>
                      </div>
                      Gerador de Sorteios
                    </a>
                    <a href="{{ route('tools.fonts-generator') }}" class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ request()->is('ferramentas/gerador-de-letras*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                      <div class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                          <path d="M12.553 4.293a1 1 0 0 0-1.106 0l-7.5 5A1 1 0 0 0 4.5 11h2.361l-1.39 6.257A1 1 0 0 0 6.447 18.5l7.5-5a1 1 0 0 0 .553-.894V11h-2.36l1.39-6.257a1 1 0 0 0-.977-1.45Z" />
                        </svg>
                      </div>
                      Gerador de Letras
                    </a>
                  </nav>
                </div>

                <!-- Institucional -->
                <div class="border-t border-slate-100 pt-4">
                  <nav class="flex flex-col gap-0.5">
                    <a href="/faq" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors">FAQ</a>
                    <a href="/termos-de-uso" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors">Termos de Uso</a>
                    <a href="/politica-de-privacidade" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors">Política de Privacidade</a>
                  </nav>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


