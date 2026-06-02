@extends('layouts.app')
@section('title', 'Grupo ' . $group->name)
@section('description', Str::limit($group->description, 155))

@section('content')

@php
    $referral = app(App\Services\ReferralService::class)->generateForGroup($group);
    $referralUrl = url('/r/' . $referral->code);

    // Verifica se o visitante é o dono do grupo:
    // 1) Por e-mail na sessão (cadastro com e-mail)
    // 2) Por ID nos cookies (cadastro sem e-mail)
    // Nunca mostra se submitter_email for null E o cookie não tiver o ID
    $sessionEmail = session('my_groups_email');
    $isOwnerByEmail = $sessionEmail && $group->submitter_email && $sessionEmail === $group->submitter_email;

    $cookieIds = json_decode(request()->cookie('submitted_groups', '[]'), true);
    $cookieIds = is_array($cookieIds) ? $cookieIds : [];
    $isOwnerByCookie = in_array($group->id, $cookieIds);

    $isOwner = $isOwnerByEmail || $isOwnerByCookie;
@endphp

<div class="max-w-3xl mx-auto px-4 py-8">

  <!-- Breadcrumb Moderno -->
  <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8">
    <a href="/" class="hover:text-slate-900 transition-colors">Início</a>
    <span class="text-slate-300">/</span>
    <a href="/categoria/{{ $group->category->slug }}" class="hover:text-slate-900 transition-colors">
      {{ $group->category->name }}
    </a>
  </nav>

  <x-adsense class="mb-8" />

  <!-- Container Principal do Detalhe -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-12 shadow-sm">
    
    <!-- Topo: Capa (Banner) -->
<div class="relative w-full h-[250px] sm:h-[300px] bg-slate-100">
    @if($group->image_path)
        <img src="{{ Storage::url($group->image_path) }}" alt="{{ $group->name }}" loading="lazy" class="w-full h-full object-cover">
    @else
        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#075E54] via-[#128C7E] to-[#25D366] relative overflow-hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute w-64 h-64 text-white opacity-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
            <span class="text-white text-7xl font-black uppercase drop-shadow-lg z-10">{{ Str::upper(Str::substr($group->name, 0, 1)) }}</span>
        </div>
    @endif

    <!-- Gradient Overlay for contrast -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

    <!-- VIP Badge no banner -->
    @if($group->is_currently_vip)
        <div class="absolute top-4 right-4 bg-gradient-to-br from-amber-400 to-amber-500 px-4 py-1.5 rounded-full flex items-center gap-1.5 shadow-lg border-2 border-white/20">
            <x-heroicon-s-star class="w-4 h-4 text-white drop-shadow-sm" />
            <span class="text-white text-xs font-bold uppercase tracking-wider">VIP</span>
        </div>
    @endif
</div>

<div class="p-6 md:p-8 text-center md:text-left relative">
    <!-- Categoria (Flutuando entre o banner e o conteudo) -->
    <div class="absolute -top-5 left-1/2 md:left-8 -translate-x-1/2 md:translate-x-0 bg-slate-900 text-white text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-lg shadow-md border-2 border-white">
        {{ $group->category->name }}
    </div>

    <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-6 pt-4">
        <div class="flex-1">
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 leading-tight mb-2 flex flex-col md:flex-row items-center gap-2">
                {{ $group->name }}
                @if($group->is_verified)
                <x-heroicon-s-check-badge class="w-6 h-6 text-blue-500 shrink-0" title="Grupo Verificado" />
                @endif
            </h1>
        </div>

        <!-- Bot�o de Acesso Direto -->
        <a href="/g/{{ $group->id }}/entrar" target="_blank" rel="nofollow noopener"
            class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white px-8 py-3.5 rounded-xl font-black text-sm uppercase tracking-wide transition-all shadow-md hover:shadow-lg text-decoration-none">
            <svg style="width: 20px; height: 20px; fill: currentColor;" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97-1.863-1.868-4.343-2.898-6.978-2.9-5.437 0-9.864 4.37-9.869 9.8-.001 1.76.476 3.476 1.385 4.982L1.8 22.282l5.05-1.326l-.203-.122zm10.743-6.611c-.301-.15-1.78-.879-2.056-.979-.275-.1-.475-.15-.675.15-.2.3-.775 1.01-1.038 1.3-.263.29-.525.32-.825.17-1.4-.7-2.312-1.28-3.138-2.7-.22-.38.22-.35.63-.78.18-.19.3-.3.45-.45.15-.15.2-.25.3-.45.1-.2.05-.375-.025-.525-.075-.15-.675-1.625-.925-2.225-.244-.589-.48-.51-.662-.519c-.171-.009-.367-.01-.563-.01c-.196 0-.516.07-.786.37-.27.3-1.03 1.01-1.03 2.46c0 1.45 1.05 2.85 1.2 3.05.15.2 2.07 3.15 5.007 4.42c.699.302 1.243.483 1.668.619.702.223 1.342.192 1.847.116.563-.085 1.78-.729 2.03-1.43c.25-.7.25-1.3.175-1.43-.075-.13-.275-.205-.575-.355z"/>
            </svg>
            Entrar no Grupo
        </a>
    </div>
</div>

    <!-- Sobre o Grupo -->
    <div class="px-6 md:px-8 py-6 md:py-8 border-t border-slate-100">
        <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
            Sobre este grupo
        </h2>
        <div class="text-slate-600 text-[15px] leading-relaxed whitespace-pre-wrap break-words overflow-hidden">
            {{ $group->description }}
        </div>
    </div>

    <!-- Regras -->
    @if($group->rules || true)
    <div class="px-6 md:px-8 py-6 md:py-8 border-t border-slate-100 bg-slate-50">
      <h2 class="text-sm font-bold text-slate-900 mb-4">Regras do grupo</h2>
      <ul class="space-y-2 text-slate-600 text-sm">
        <li class="flex items-start gap-2">
           <span class="text-green-500 font-bold mt-0.5">✓</span> Proibido conteúdo adulto ou agressivo
        </li>
        <li class="flex items-start gap-2">
           <span class="text-green-500 font-bold mt-0.5">✓</span> Proibido spam ou envio excessivo de links
        </li>
        <li class="flex items-start gap-2">
           <span class="text-green-500 font-bold mt-0.5">✓</span> Respeitar todos os membros
        </li>
        @if($group->rules)
          @foreach(explode("\n", $group->rules) as $customRule)
            @if(trim($customRule))
            <li class="flex items-start gap-2 pt-2 border-t border-slate-200 mt-2">
              <span class="text-slate-400 font-bold mt-0.5">•</span> {{ trim($customRule) }}
            </li>
            @endif
          @endforeach
        @endif
      </ul>
    </div>
    @endif

    <!-- Compartilhar com Ícones -->
    <div class="px-6 md:px-8 py-6 border-t border-slate-100 flex flex-wrap items-center gap-3">
        <span class="text-sm text-slate-600 font-bold mr-2">Compartilhar:</span>
        
        <!-- WhatsApp -->
        <a href="https://api.whatsapp.com/send?text={{ rawurlencode('Entre no grupo ' . $group->name . ': ' . url('/grupo/'.$group->id)) }}"
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#25D366] text-white hover:bg-[#1da851] transition-colors shadow-sm" title="WhatsApp">
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97-1.863-1.868-4.343-2.898-6.978-2.9-5.437 0-9.864 4.37-9.869 9.8-.001 1.76.476 3.476 1.385 4.982L1.8 22.282l5.05-1.326l-.203-.122zm10.743-6.611c-.301-.15-1.78-.879-2.056-.979-.275-.1-.475-.15-.675.15-.2.3-.775 1.01-1.038 1.3-.263.29-.525.32-.825.17-1.4-.7-2.312-1.28-3.138-2.7-.22-.38.22-.35.63-.78.18-.19.3-.3.45-.45.15-.15.2-.25.3-.45.1-.2.05-.375-.025-.525-.075-.15-.675-1.625-.925-2.225-.244-.589-.48-.51-.662-.519c-.171-.009-.367-.01-.563-.01c-.196 0-.516.07-.786.37-.27.3-1.03 1.01-1.03 2.46c0 1.45 1.05 2.85 1.2 3.05.15.2 2.07 3.15 5.007 4.42c.699.302 1.243.483 1.668.619.702.223 1.342.192 1.847.116.563-.085 1.78-.729 2.03-1.43c.25-.7.25-1.3.175-1.43-.075-.13-.275-.205-.575-.355z"/></svg>
        </a>

        <!-- Telegram -->
        <a href="https://t.me/share/url?url={{ urlencode(url('/grupo/'.$group->id)) }}&text={{ rawurlencode('Entre no grupo ' . $group->name) }}"
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#0088cc] text-white hover:bg-[#0077b3] transition-colors shadow-sm" title="Telegram">
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.892-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
        </a>

        <!-- Facebook -->
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/grupo/'.$group->id)) }}"
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#1877F2] text-white hover:bg-[#166fe5] transition-colors shadow-sm" title="Facebook">
           <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>

        <!-- Instagram (Copy Link wrapper since IG doesn't have link intent) -->
        <div x-data="{ copiedIg: false }">
            <button @click="navigator.clipboard.writeText('{{ url('/grupo/'.$group->id) }}'); copiedIg = true; setTimeout(() => copiedIg = false, 2000)"
               class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gradient-to-tr from-[#f09433] via-[#e6683c] to-[#bc1888] text-white hover:opacity-90 transition-opacity shadow-sm" title="Copiar link para Instagram">
               <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.07M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
            </button>
        </div>

        <!-- Copiar Link -->
        <div x-data="{ copied: false }">
            <button @click="navigator.clipboard.writeText('{{ url('/grupo/'.$group->id) }}'); copied = true; setTimeout(() => copied = false, 2000)"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors shadow-sm text-xs font-bold" title="Copiar link">
               <x-heroicon-o-link class="w-4 h-4" />
               <span x-show="!copied">Copiar Link</span>
               <span x-show="copied" class="text-green-600">Copiado!</span>
            </button>
        </div>
    </div>

    <!-- Estatísticas Privadas para o Dono -->
    @if ($isOwner)
      <div class="border-t border-blue-100 bg-blue-50/50 p-6 md:p-8">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            Área do Proprietário
          </h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center mb-6">
          <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs text-slate-500 block mb-1">Visualizações (Privado)</span>
            <span class="text-lg font-bold text-slate-900">{{ number_format($group->views) }}</span>
          </div>
          <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs text-slate-500 block mb-1">Cliques (Privado)</span>
            <span class="text-lg font-bold text-blue-600">{{ number_format($group->clicks) }}</span>
          </div>
          <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col items-center justify-center">
             <span class="text-xs text-slate-500 block mb-1">Indicações para VIP</span>
             <span class="text-lg font-bold text-amber-600">{{ $referral->conversions }} / 5</span>
          </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm" x-data="{ copied: false }">
            <h4 class="text-sm font-bold text-slate-900 mb-2">Seu Link de Indicação para ganhar VIP</h4>
            <p class="text-xs text-slate-600 mb-3">Compartilhe este link. A cada 5 membros que entrarem, seu grupo ganha VIP grátis!</p>
            <div class="flex gap-2">
                <input type="text" readonly value="{{ $referralUrl }}" class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-600 outline-none" />
                <button @click="navigator.clipboard.writeText('{{ $referralUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg px-4 py-2 transition-colors">
                  <span x-show="!copied">Copiar</span>
                  <span x-show="copied">Copiado!</span>
                </button>
            </div>
        </div>
      </div>
    @endif
  </div>

  <!-- Buscas Relacionadas (SEO internal link building) -->
  @if(isset($relatedSeoPages) && $relatedSeoPages->isNotEmpty())
  <div class="mb-12 p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-1.5">
      <x-heroicon-s-link class="w-4 h-4 text-slate-400" /> Tópicos Relacionados Recomendados
    </h3>
    <div class="flex flex-wrap gap-2">
      @foreach($relatedSeoPages as $relatedPage)
        @php
          $cleanName = str_ireplace(
              ['grupos de whatsapp de', 'grupos de whatsapp', 'grupo de whatsapp', 'grupos whatsapp', 'grupo whatsapp', 'no whatsapp', 'do whatsapp', 'de whatsapp', 'whatsapp', 'grupos de ', 'grupo de ', 'grupos ', 'grupo '],
              '', 
              $relatedPage->keyword
          );
          $cleanName = trim($cleanName);
        @endphp
        <a href="/grupos-whatsapp/{{ $relatedPage->slug }}" 
           class="px-3.5 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold text-slate-600 hover:bg-green-50 hover:text-primary hover:border-green-200 transition-all">
          Grupos de <span class="capitalize">{{ $cleanName }}</span>
        </a>
      @endforeach
    </div>
  </div>
  @endif

  <x-adsense class="mb-12" />

</div> <!-- End max-w-3xl container -->

<!-- Related Groups & Blog (Full Width max-w-6xl) -->
<div class="max-w-6xl mx-auto px-4 mb-16">
  <!-- Grupos relacionados -->
  @if($related->count())
  <div class="mb-12">
    <h2 class="text-xl font-bold text-slate-900 mb-6">Grupos relacionados de {{ $group->category->name }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      @foreach($related as $rel)
        @include('components.group-card', ['group' => $rel])
      @endforeach
    </div>
  </div>
  @endif

  <x-adsense class="mb-12" />

  <!-- LATEST BLOG POSTS SECTION -->
  <x-blog-section :posts="$latestBlogPosts ?? collect()" :bare="true" />
</div>
@endsection


