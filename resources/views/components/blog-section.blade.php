@props([
    'posts',
    'bare' => false,
])

@if(isset($posts) && $posts->isNotEmpty())
<section>
    {{-- Cabeçalho: sem fundo branco em ambos os contextos --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
        <div>
            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-widest mb-2.5 inline-block">
                Blog WhatsGrupos
            </span>
            <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                Últimas Notícias e Tutoriais
            </h2>
            @if(!$bare)
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Dicas, guias e as últimas novidades sobre o universo do WhatsApp.
            </p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($posts as $post)
            <x-blog-card :post="$post" />
        @endforeach
    </div>

    {{-- Único botão, branco, centralizado abaixo dos cards --}}
    <div class="mt-8 text-center">
        <a href="{{ route('blog.index') }}"
           class="inline-flex items-center justify-center gap-2 px-8 py-3
                  bg-white border border-slate-200 hover:border-slate-300
                  text-slate-600 hover:text-slate-900
                  font-bold text-xs rounded-xl
                  shadow-sm hover:shadow-md
                  transition-all duration-200 whitespace-nowrap">
            <x-heroicon-o-book-open class="w-4 h-4 text-[#25D366]" />
            Ver todas as publicações
            <x-heroicon-m-arrow-right class="w-3.5 h-3.5 opacity-50" />
        </a>
    </div>
</section>
@endif
