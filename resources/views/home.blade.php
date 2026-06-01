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
      <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-2.5 inline-block">
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
          <x-heroicon-s-fire class="w-3.5 h-3.5 text-orange-500" /> <span class="capitalize">{{ $cleanName }}</span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- DYNAMIC FAQ & ABOUT WHATSRUPOS SECTION (Alpine.js Accordion) -->
<section class="mt-20 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm" x-data="{ faqOpen: null }">
  <div class="max-w-3xl mx-auto">
    <div class="text-center mb-10">
      <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-3 inline-block">
        Dúvidas Frequentes
      </span>
      <h2 class="text-2xl font-black text-slate-900 tracking-tight">
        Perguntas Frequentes sobre WhatsApp & WhatsGrupos
      </h2>
      <p class="text-slate-500 text-xs sm:text-sm mt-2">
        Tudo o que você precisa saber sobre o uso de grupos, canais e a divulgação gratuita no nosso portal.
      </p>
    </div>

    <div class="space-y-4">
      <!-- FAQ 1 -->
      <div class="border-b border-slate-100 pb-4">
        <button @click="faqOpen === 1 ? faqOpen = null : faqOpen = 1" class="w-full flex justify-between items-center text-left font-extrabold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
          <span>O que é o WhatsGrupos e como ele funciona?</span>
          <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform" x-bind:class="faqOpen === 1 ? 'rotate-180 text-primary' : ''" />
        </button>
        <div x-show="faqOpen === 1" x-collapse class="mt-3 text-xs sm:text-sm text-slate-500 leading-relaxed" style="display: none;">
          O <strong>WhatsGrupos</strong> é o maior catálogo independente e motor de busca para links de convites de grupos e canais de WhatsApp no Brasil. Conectamos administradores de comunidades com usuários reais que desejam interagir sobre assuntos de seu interesse comum, de forma 100% gratuita.
        </div>
      </div>

      <!-- FAQ 2 -->
      <div class="border-b border-slate-100 pb-4">
        <button @click="faqOpen === 2 ? faqOpen = null : faqOpen = 2" class="w-full flex justify-between items-center text-left font-extrabold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
          <span>Como entrar em um grupo de WhatsApp usando o link?</span>
          <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform" x-bind:class="faqOpen === 2 ? 'rotate-180 text-primary' : ''" />
        </button>
        <div x-show="faqOpen === 2" x-collapse class="mt-3 text-xs sm:text-sm text-slate-500 leading-relaxed" style="display: none;">
          É muito simples! Basta navegar pelas nossas categorias ou utilizar a barra de pesquisa, escolher o grupo que deseja entrar e clicar no botão "Entrar no Grupo". Você será direcionado para o site oficial do WhatsApp que abrirá o aplicativo no seu celular ou computador de forma segura e instantânea.
        </div>
      </div>

      <!-- FAQ 3 -->
      <div class="border-b border-slate-100 pb-4">
        <button @click="faqOpen === 3 ? faqOpen = null : faqOpen = 3" class="w-full flex justify-between items-center text-left font-extrabold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
          <span>Como posso divulgar o meu grupo gratuitamente?</span>
          <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform" x-bind:class="faqOpen === 3 ? 'rotate-180 text-primary' : ''" />
        </button>
        <div x-show="faqOpen === 3" x-collapse class="mt-3 text-xs sm:text-sm text-slate-500 leading-relaxed" style="display: none;">
          Para divulgar sua comunidade, clique em "Enviar Grupo" no menu superior. Cole o link oficial de convite do WhatsApp, escolha a categoria ideal, digite o nome e descrição do grupo e marque pelo menos uma regra do grupo. Nosso robô inteligente tentará obter automaticamente a imagem de perfil e o nome do seu grupo do WhatsApp para você!
        </div>
      </div>

      <!-- FAQ 4 -->
      <div class="border-b border-slate-100 pb-4">
        <button @click="faqOpen === 4 ? faqOpen = null : faqOpen = 4" class="w-full flex justify-between items-center text-left font-extrabold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
          <span>O WhatsGrupos é seguro para os usuários?</span>
          <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform" x-bind:class="faqOpen === 4 ? 'rotate-180 text-primary' : ''" />
        </button>
        <div x-show="faqOpen === 4" x-collapse class="mt-3 text-xs sm:text-sm text-slate-500 leading-relaxed" style="display: none;">
          Sim! Nós fazemos varreduras constantes com nosso validador automático de links para desativar convites expirados e realizamos moderações constantes para impedir spam ou conteúdo ilegal. Lembre-se, porém, de manter as boas práticas de segurança, como configurar sua privacidade para ocultar sua foto a desconhecidos e nunca compartilhar senhas ou códigos recebidos no celular.
        </div>
      </div>

      <!-- FAQ 5 -->
      <div class="pb-2">
        <button @click="faqOpen === 5 ? faqOpen = null : faqOpen = 5" class="w-full flex justify-between items-center text-left font-extrabold text-slate-800 hover:text-primary transition-colors text-sm sm:text-base">
          <span>O que são os grupos recomendados e as categorias especiais?</span>
          <x-heroicon-m-chevron-down class="w-5 h-5 text-slate-400 transition-transform" x-bind:class="faqOpen === 5 ? 'rotate-180 text-primary' : ''" />
        </button>
        <div x-show="faqOpen === 5" x-collapse class="mt-3 text-xs sm:text-sm text-slate-500 leading-relaxed" style="display: none;">
          As categorias especiais são páginas otimizadas para os termos e assuntos mais buscados do Brasil (como Figurinhas, Palmeiras, Roblox, etc.). Nosso sistema realiza buscas inteligentes contínuas por correspondência de texto para agrupar e recomendar apenas as conversas mais ativas focadas exatamente em cada um desses temas!
        </div>
      </div>
    </div>

  </div>
</section>

      {{-- Bloco Din�mico e Rico para SEO T�cnico de Rodap� (Light Clean Style) --}}
      <section class="mt-16 pt-12 border-t border-slate-200/50 text-left">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-3 inline-flex items-center gap-2">
                <x-heroicon-s-chat-bubble-left-right class="w-5 h-5 text-slate-500" /> Encontre Grupos Ativos
            </h2>
            <p class="text-xs text-slate-500 leading-relaxed">
              O <strong>WhatsGrupos</strong> � o maior e mais atualizado diret�rio p�blico para encontrar e entrar em links de grupos de WhatsApp reais do Brasil. Nossa plataforma conta com milhares de comunidades organizadas por categorias como amizades, neg�cios, figurinhas, jogos e muito mais. Todos os envios passam por modera��o humana rigorosa de seguran�a para garantir apenas links ativos.
            </p>
          </div>
          <div>
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-3 inline-flex items-center gap-2">
                <x-heroicon-s-rocket-launch class="w-5 h-5 text-slate-500" /> Como Divulgar seu Grupo
            </h2>
            <p class="text-xs text-slate-500 leading-relaxed">
              Deseja atrair centenas de novos membros de forma r�pida e qualificada? Clique em <strong>Enviar Grupo</strong> no topo, cole o link oficial de convite do WhatsApp e selecione a categoria ideal. O nosso sistema realiza a auto-detec��o da imagem e do t�tulo do grupo automaticamente. Voc� tamb�m pode impulsionar sua comunidade assinando nossos pacotes VIPs para fixar seu grupo no topo!
            </p>
          </div>
          <div>
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-3 inline-flex items-center gap-2">
                <x-heroicon-s-shield-check class="w-5 h-5 text-slate-500" /> Regras e Seguran�a
            </h2>
            <p class="text-xs text-slate-500 leading-relaxed">
              Prezamos pela integridade dos nossos usu�rios. � estritamente proibido o cadastro de links de grupos contendo spam, v�rus, conte�do de viol�ncia, pirataria ou menor de idade. Grupos inativos ou com links expirados s�o removidos automaticamente pelo nosso validador inteligente. Respeite as regras de cada administrador ao participar das discuss�es.
            </p>
          </div>
        </div>
      </section>
@endsection

