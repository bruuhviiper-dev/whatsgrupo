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
                    <a href="/pacotes-vip" class="mt-1 mb-1 flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100 hover:border-amber-300 hover:text-amber-800 group">
                      <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 border border-amber-200 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-200 transition-colors">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" class="w-3.5 h-3.5 text-amber-600">
                            <path d="M239.54,98.11l-36.88,86.07a16,16,0,0,1-14.66,9.82H68a16,16,0,0,1-14.66-9.82L16.46,98.11A8,8,0,0,1,24.63,86.3l57,21.36,39.11-65.18a8,8,0,0,1,13.72,0l39.11,65.18,57-21.36a8,8,0,0,1,8.17,11.81Z"></path>
                          </svg>
                        </div>
                        <div class="flex flex-col leading-tight">
                          <span class="font-bold tracking-tight">Impulsionar Grupo</span>
                          <span class="text-[10px] text-amber-500 font-medium">Ver pacotes disponíveis</span>
                        </div>
                      </div>
                      <x-heroicon-o-arrow-right class="w-4 h-4 text-amber-400 group-hover:translate-x-0.5 transition-transform flex-shrink-0" />
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
                  <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-3 mb-1.5">Ferramentas</p>
                  @php
                    $toolList = [
                      ['/figurinhas',                            'figurinhas*',                    'heroicon-o-face-smile',           'Figurinhas de WhatsApp'],
                      ['/ferramentas/analise-de-engajamento',    'ferramentas/analise*',            'heroicon-o-chart-bar',            'Análise de Engajamento'],
                      [route('tools.spam-detector'),             'ferramentas/detector-de-spam*',   'heroicon-o-shield-exclamation',   'Detector de Spam'],
                      ['/ferramentas/gerador-de-regras',         'ferramentas/gerador-de-regras*',  'heroicon-o-document-check',       'Gerador de Regras'],
                      [route('tools.name-generator'),            'ferramentas/gerador-de-nomes*',   'heroicon-o-sparkles',             'Gerador de Nomes'],
                      [route('tools.welcome-message'),           'ferramentas/mensagem*',           'heroicon-o-hand-raised',          'Mensagem de Boas-Vindas'],
                      [route('tools.link-validator'),            'ferramentas/verificador*',        'heroicon-o-link',                 'Verificador de Link'],
                      [route('tools.poll-generator'),            'ferramentas/gerador-de-enquete*', 'heroicon-o-chart-bar-square',     'Gerador de Enquete'],
                      [route('tools.raffle-generator'),          'ferramentas/gerador-de-sorteios*','heroicon-o-gift',                 'Gerador de Sorteios'],
                      [route('tools.fonts-generator'),           'ferramentas/gerador-de-letras*',  'heroicon-o-language',             'Gerador de Letras Especiais'],
                    ];
                  @endphp
                  <nav class="flex flex-col gap-0.5">
                    @foreach($toolList as [$href, $pattern, $icon, $label])
                      @php $active = request()->is($pattern); @endphp
                      <a href="{{ $href }}"
                         class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors
                                {{ $active
                                    ? 'text-slate-900 bg-slate-100'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <x-dynamic-component :component="$icon"
                          class="w-4 h-4 flex-shrink-0 {{ $active ? 'text-slate-700' : 'text-slate-400' }}" />
                        {{ $label }}
                      </a>
                    @endforeach
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


