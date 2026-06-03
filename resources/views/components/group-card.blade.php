@props(['group'])

<a href="{{ route('group.show', $group->id) }}" class="group flex flex-col bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:border-[#25D366] transition-all duration-300 relative h-[380px] {{ $group->is_currently_vip ? 'ring-2 ring-amber-400' : '' }}">
  
  <!-- Topo: Imagem/Avatar -->
  <div class="relative w-full h-[170px] bg-slate-100 flex-shrink-0">
    @if ($group->image_url)
      <img src="{{ $group->image_url }}" alt="{{ $group->name }}" loading="lazy" class="w-full h-full object-cover">
    @else
      {{-- Imagem padrão do WhatsApp com inicial do grupo como fallback --}}
      <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#128C7E] to-[#25D366] relative overflow-hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-white opacity-30 absolute" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
        </svg>
        <span class="text-4xl font-black text-white z-10 drop-shadow-md">{{ Str::upper(Str::substr($group->name, 0, 1)) }}</span>
      </div>
    @endif
    
    <!-- VIP Badge -->
    @if ($group->is_currently_vip)
      <div class="absolute top-2 right-2 z-10" title="Grupo VIP">
        <div class="bg-gradient-to-br from-amber-300 via-amber-400 to-amber-600 w-12 h-12 rounded-full flex items-center justify-center shadow-[0_4px_15px_rgba(245,158,11,0.6)] border-[3px] border-white ring-2 ring-amber-200/50">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" class="w-7 h-7 text-white drop-shadow-md">
             <path d="M239.54,98.11l-36.88,86.07a16,16,0,0,1-14.66,9.82H68a16,16,0,0,1-14.66-9.82L16.46,98.11A8,8,0,0,1,24.63,86.3l57,21.36,39.11-65.18a8,8,0,0,1,13.72,0l39.11,65.18,57-21.36a8,8,0,0,1,8.17,11.81Z"></path>
           </svg>
        </div>
      </div>
    @endif

    <!-- Categoria Badge (Overlapping) -->
    <div class="absolute -bottom-3 left-4 bg-slate-800 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-lg shadow-md border border-slate-700">
      {{ $group->category->name ?? 'Geral' }}
    </div>
  </div>

  <!-- Body com Nome, Descrição e Botão -->
  <div class="p-4 flex flex-col flex-grow">
    <!-- Nome -->
    <h3 class="text-slate-900 font-bold text-[15px] leading-tight mb-2 line-clamp-1 group-hover:text-[#25D366] transition-colors flex items-center gap-1.5" title="{{ $group->name }}">
      {{ $group->name }}
      @if ($group->is_verified)
        <x-heroicon-s-check-badge class="w-4 h-4 text-blue-500 shrink-0" title="Grupo Verificado" />
      @endif
    </h3>
    
    <!-- Descrição com clamp e quebra de palavra para responsividade -->
    <div class="h-[60px] mb-3">
      <p class="text-slate-500 text-[13px] leading-relaxed line-clamp-3 break-words break-all sm:break-normal overflow-hidden">
        {{ $group->description }}
      </p>
    </div>

    <!-- Botão de Entrar (Fica preso no final) -->
    <div class="mt-auto">
      <div class="w-full bg-[#25D366] text-white py-2.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 group-hover:bg-[#20bd5a] transition-colors shadow-sm">
        Entrar no Grupo
      </div>
    </div>
  </div>
</a>
