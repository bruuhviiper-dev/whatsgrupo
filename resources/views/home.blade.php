@extends('layouts.app')

@section('title', 'Grupos de WhatsApp — WhatsGrupos')
@section('description', 'Encontre os melhores grupos de WhatsApp do Brasil. Mais de 10.000 grupos ativos organizados por categorias.')

@section('content')

  <!-- CABEÇALHO DA HOME -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight mb-1">
        Grupos de Whatsapp
      </h1>
    </div>

    <!-- TABS DE FILTRO EM ESTILO PILL SELECIONÁVEL -->
    <div class="flex flex-wrap gap-2">
      <a href="/" class="px-5 py-2 rounded-full text-xs font-semibold transition-all border
           {{ !request('tab')
    ? 'bg-slate-900 border-slate-900 text-white shadow-sm'
    : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
        Todos
      </a>
      <a href="/?tab=vip" class="px-5 py-2 rounded-full text-xs font-semibold transition-all border
           {{ request('tab') == 'vip'
    ? 'bg-amber-100 border-amber-200 text-amber-900 shadow-sm'
    : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
        VIPs
      </a>
      <a href="/?tab=popular" class="px-5 py-2 rounded-full text-xs font-semibold transition-all border
           {{ request('tab') == 'popular'
    ? 'bg-blue-100 border-blue-200 text-blue-900 shadow-sm'
    : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
        Populares
      </a>
      <a href="/?tab=novos" class="px-5 py-2 rounded-full text-xs font-semibold transition-all border
           {{ request('tab') == 'novos'
    ? 'bg-green-100 border-green-200 text-green-900 shadow-sm'
    : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
        Novos
      </a>
    </div>
  </div>

  <x-adsense class="mb-8" />

  <!-- SEÇÃO DE DESTAQUES VIP NO TOPO (Apenas na aba Todos ou VIP) -->
  @if(!request('tab') || request('tab') == 'vip')
    @php
      $vips = $groups->filter(fn($g) => $g->is_currently_vip);
    @endphp
    @if($vips->isNotEmpty())
      <div class="mb-10">
        <div class="flex items-center gap-2 mb-4">
          <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wider">Grupos VIP em Destaque</h2>
        </div>
        <!-- Grid especial para VIPs (cards maiores com destaque premium) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          @foreach($vips as $group)
            @include('components.group-card', ['group' => $group])
          @endforeach
        </div>
        <hr class="border-slate-100 mt-8">
      </div>
    @endif
  @endif

  <!-- LISTAGEM GERAL -->
  <div>
    @if(!request('tab') && isset($vips) && $vips->isNotEmpty())
      <div class="flex items-center gap-2 mb-4">
        <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wider">Todos os Grupos</h2>
      </div>
    @endif

    <!-- GRID DE CARDS RESPONSIVO -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      @forelse($groups as $group)
        <!-- Se já foi listado no topo dos VIPs, pula para não duplicar -->
        @if(isset($vips) && $vips->contains($group))
          @continue
        @endif
        @include('components.group-card', ['group' => $group])
      @empty
        <div class="col-span-full text-center py-20 border border-slate-100 rounded-2xl bg-white shadow-sm">
          <h3 class="text-slate-900 font-bold text-lg mb-1">Nenhum grupo encontrado</h3>
          <p class="text-slate-500 text-sm">Tente buscar outros termos ou navegue pelas categorias acima.</p>
        </div>
      @endforelse
    </div>
  </div>

  <!-- PAGINAÇÃO -->
  <div class="flex justify-center mt-12">
    {{ $groups->onEachSide(2)->links('components.pagination') }}
  </div>

  <x-adsense class="mt-12" />

  <!-- LATEST BLOG POSTS SECTION -->
  <div class="mt-16">
    <x-blog-section :posts="$latestBlogPosts ?? collect()" />
  </div>

  <!-- POPULAR TOPICS SECTION (SEO internal link building) -->
  @if(isset($seoPages) && $seoPages->isNotEmpty())
    <section class="mt-16 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm">
      <div class="max-w-4xl mx-auto">
        <div class="mb-8">
          <span
            class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-2.5 inline-block">
            Buscas Populares
          </span>
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
            Tópicos e Assuntos mais Buscados
          </h2>
          <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Encontre comunidades e grupos focados em temas brasileiros específicos com alto engajamento.
          </p>
        </div>

        <div class="flex flex-wrap gap-2.5">
          @foreach($seoPages as $page)
            @php
              $cleanName = str_ireplace(['no WhatsApp', 'do WhatsApp', 'no whatsapp', 'do whatsapp', 'WhatsApp de', 'WhatsApp'], '', $page->keyword);
              $cleanName = trim($cleanName);
            @endphp
            <a href="/grupos-whatsapp/{{ $page->slug }}"
              class="px-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-bold text-slate-600 hover:bg-green-50 hover:text-primary hover:border-green-200 transition-all flex items-center gap-1.5 shadow-sm">
              <x-heroicon-o-hashtag class="w-3.5 h-3.5 text-slate-400" /> <span class="capitalize">{{ $cleanName }}</span>
            </a>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- Bloco Din�mico e Rico para SEO T�cnico de Rodap� (Light Clean Style) --}}
  <section class="mt-16 pt-12 border-t border-slate-200/50 text-left">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div>
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-3 inline-flex items-center gap-2">
          <x-heroicon-s-chat-bubble-left-right class="w-5 h-5 text-slate-500" /> Encontre Grupos Ativos
        </h2>
        <p class="text-xs text-slate-500 leading-relaxed">
          O <strong>WhatsGrupos</strong> O maior e mais atualizado diretório pblico para encontrar e entrar em links de
          grupos de WhatsApp reais do Brasil. Nossa plataforma conta com milhares de comunidades organizadas por
          categorias como amizades, negcios, figurinhas, jogos e muito mais. Todos os envios passam por moderao humana
          rigorosa de segurana para garantir apenas links ativos.
        </p>
      </div>
      <div>
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-3 inline-flex items-center gap-2">
          <x-heroicon-s-rocket-launch class="w-5 h-5 text-slate-500" /> Como Divulgar seu Grupo
        </h2>
        <p class="text-xs text-slate-500 leading-relaxed">
          Deseja atrair centenas de novos membros de forma rapida e qualificada? Clique em <strong>Enviar Grupo</strong>
          no topo, cole o link oficial de convite do WhatsApp e selecione a categoria ideal. O nosso sistema realiza a
          auto-detecção da imagem e do ttulo do grupo automaticamente. Voc tambm pode impulsionar sua comunidade assinando
          nossos pacotes VIPs para fixar seu grupo no topo!
        </p>
      </div>
      <div>
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-3 inline-flex items-center gap-2">
          <x-heroicon-s-shield-check class="w-5 h-5 text-slate-500" /> Regras e Seguran�a
        </h2>
        <p class="text-xs text-slate-500 leading-relaxed">
          Prezamos pela integridade dos nossos usuarios. Nossa plataforma estritamente proibido o cadastro de links de
          grupos contendo spam, virus, conteudo de violência, pirataria ou menor de idade. Grupos inativos ou com links
          expirados so removidos automaticamente pelo nosso validador inteligente. Respeite as regras de cada
          administrador ao participar das discusses.
        </p>
      </div>
    </div>
  </section>

  @php
    // Fonte única das perguntas: alimenta o accordion visível E o FAQPage JSON-LD,
    // garantindo correspondência exata (exigência do Google para rich results).
    $homeFaqs = [
        ['q' => 'O que é o WhatsGrupos e como ele funciona?',
         'a' => 'O WhatsGrupos é o maior catálogo independente e motor de busca para links de convites de grupos e canais de WhatsApp no Brasil. Conectamos administradores de comunidades com usuários reais que desejam interagir sobre assuntos de interesse comum, de forma 100% gratuita. Basta escolher uma categoria, encontrar um grupo ativo e entrar com um clique.'],
        ['q' => 'Como entrar em um grupo de WhatsApp usando o link?',
         'a' => 'É muito simples: navegue pelas categorias ou use a barra de pesquisa, escolha o grupo que deseja participar e clique no botão "Entrar no Grupo". Você será direcionado ao site oficial do WhatsApp, que abrirá o aplicativo no seu celular ou computador de forma segura e instantânea.'],
        ['q' => 'Como divulgar e cadastrar o meu grupo gratuitamente?',
         'a' => 'Clique em "Enviar Grupo" no menu superior, cole o link oficial de convite do WhatsApp, escolha a categoria ideal, digite o nome e a descrição do grupo e marque pelo menos uma regra. Nosso robô tenta obter automaticamente a imagem de perfil e o nome do grupo. O cadastro é gratuito e o grupo passa por moderação antes de ser publicado.'],
        ['q' => 'Como criar um grupo de WhatsApp?',
         'a' => 'No aplicativo do WhatsApp, toque em Nova conversa e depois em Novo grupo, selecione os participantes, defina um nome e uma foto e confirme. Depois, abra o grupo, toque em Convidar via link e copie o link de convite — é esse link que você cadastra aqui no WhatsGrupos para atrair novos membros.'],
        ['q' => 'Quantas pessoas cabem em um grupo de WhatsApp?',
         'a' => 'Atualmente um grupo de WhatsApp comporta até 1.024 participantes. O limite começou em 100, passou para 256 e foi ampliado para 512 e depois 1.024. Para audiências maiores, o WhatsApp oferece os Canais, que permitem alcançar um número ilimitado de seguidores em modo de transmissão.'],
        ['q' => 'Como sair de um grupo de WhatsApp?',
         'a' => 'Abra o grupo, toque no nome do grupo no topo para ver os detalhes, role até o final e toque em "Sair do grupo" e confirme. Se você for administrador, vale designar outro administrador antes de sair para que a comunidade continue moderada.'],
        ['q' => 'Qual a diferença entre grupo e canal de WhatsApp?',
         'a' => 'No grupo todos os participantes podem conversar entre si (até 1.024 pessoas). Já o canal é uma ferramenta de transmissão de mão única: apenas o administrador publica e os seguidores recebem as atualizações, sem limite de seguidores. No WhatsGrupos você encontra e cadastra tanto grupos quanto canais.'],
        ['q' => 'O WhatsGrupos é seguro para os usuários?',
         'a' => 'Sim. Fazemos varreduras constantes com um validador automático de links para desativar convites expirados e moderamos os envios para impedir spam e conteúdo ilegal. Ainda assim, mantenha boas práticas: configure sua privacidade para ocultar sua foto de desconhecidos e nunca compartilhe senhas ou códigos recebidos no celular.'],
        ['q' => 'O que são os grupos VIP e as categorias especiais?',
         'a' => 'Os grupos VIP são destaques que aparecem no topo das listagens por tempo determinado. As categorias especiais são páginas otimizadas para os termos mais buscados do Brasil (como Figurinhas, Palmeiras, Roblox e muitos outros). Nosso sistema faz buscas inteligentes contínuas para recomendar apenas as conversas mais ativas de cada tema.'],
    ];
  @endphp

  <!-- DYNAMIC FAQ & ABOUT WHATSRUPOS SECTION (Alpine.js Accordion) -->
  <section class="mt-20 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm" x-data="{ faqOpen: null }">
    <div class="max-w-3xl mx-auto">
      <div class="text-center mb-10">
        <span
          class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-3 inline-block">
          Dúvidas Frequentes
        </span>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">
          Perguntas Frequentes sobre Grupos de WhatsApp
        </h2>
        <p class="text-slate-500 text-xs sm:text-sm mt-2">
          Tudo o que você precisa saber sobre o uso de grupos, canais e a divulgação gratuita no nosso portal.
        </p>
      </div>

      <div class="space-y-4">
        @foreach($homeFaqs as $i => $faq)
        <div class="border-b border-slate-100 pb-4 last:border-0">
          <button @click="faqOpen === {{ $i }} ? faqOpen = null : faqOpen = {{ $i }}"
            class="w-full flex justify-between items-center text-left font-extrabold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
            <span>{{ $faq['q'] }}</span>
            <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform shrink-0 ml-3"
              x-bind:class="faqOpen === {{ $i }} ? 'rotate-180 text-primary' : ''" />
          </button>
          <div x-show="faqOpen === {{ $i }}" x-collapse class="mt-3 text-xs sm:text-sm text-slate-500 leading-relaxed"
            style="display: none;">
            {{ $faq['a'] }}
          </div>
        </div>
        @endforeach
      </div>

    </div>
  </section>

  <!-- BLOCO DE TEXTO SEO (conteúdo longo otimizado para buscadores) -->
  <section class="mt-12 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm">
    <div class="max-w-3xl mx-auto prose prose-slate prose-sm sm:prose-base max-w-none">
      <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-4">
        O melhor site de links de Grupos de WhatsApp do Brasil
      </h2>
      <p class="text-slate-600 text-sm leading-relaxed mb-4">
        O <strong>WhatsGrupos</strong> reúne os melhores <strong>grupos de WhatsApp ativos</strong> em um só lugar.
        Aqui você encontra links de convite organizados por categoria — de <a href="/categoria/amizade" class="text-primary font-semibold hover:underline">amizade</a>,
        <a href="/categoria/namoro" class="text-primary font-semibold hover:underline">namoro</a> e
        <a href="/categoria/futebol" class="text-primary font-semibold hover:underline">futebol</a> a
        <a href="/categoria/games-e-jogos" class="text-primary font-semibold hover:underline">games</a>,
        <a href="/categoria/ganhar-dinheiro" class="text-primary font-semibold hover:underline">renda extra</a> e
        <a href="/categoria/vagas-de-emprego" class="text-primary font-semibold hover:underline">vagas de emprego</a> — todos
        verificados pelo nosso validador automático para garantir que os links estejam funcionando.
      </p>
      <p class="text-slate-600 text-sm leading-relaxed mb-4">
        Quer divulgar a sua comunidade? <a href="/enviar-grupo" class="text-primary font-semibold hover:underline">Cadastre seu grupo gratuitamente</a>
        em segundos e alcance milhares de pessoas interessadas no seu tema. Você também pode
        <a href="/pacotes-vip" class="text-primary font-semibold hover:underline">impulsionar seu grupo</a> para aparecer
        em destaque no topo das listagens. Explore ainda os
        <a href="/grupos-novos" class="text-primary font-semibold hover:underline">grupos novos</a> e os
        <a href="/grupos-mais-populares" class="text-primary font-semibold hover:underline">mais populares</a> do dia.
      </p>
      <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2 mt-6">Grupos e canais de WhatsApp para todos os interesses</h3>
      <p class="text-slate-600 text-sm leading-relaxed">
        Um grupo de WhatsApp comporta até <strong>1.024 participantes</strong> e é perfeito para conversas e troca de
        experiências. Já os <strong>canais de WhatsApp</strong> permitem transmitir conteúdo para um número ilimitado de
        seguidores. No WhatsGrupos você descobre e participa dos dois formatos, com novos grupos sendo adicionados todos
        os dias por administradores e pelo nosso coletor inteligente — sempre priorizando comunidades reais e ativas.
      </p>
    </div>
  </section>

  {{-- Structured data: FAQPage (perguntas acima) + ItemList (grupos listados) --}}
  <x-seo.faq :faqs="$homeFaqs" />
  @if(isset($groups) && $groups->count())
    <x-schema-list title="Grupos de WhatsApp" :groups="$groups->getCollection()" />
  @endif
@endsection