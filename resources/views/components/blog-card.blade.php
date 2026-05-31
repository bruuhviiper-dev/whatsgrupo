@props(['post'])

<article class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col sm:flex-row hover:shadow-md hover:-translate-y-1 transition-all duration-200 h-full relative group">
    <!-- Link invisível sobre todo o card -->
    <a href="/blog/{{ $post->slug }}" class="absolute inset-0 z-20" aria-label="Ler artigo: {{ $post->title }}"></a>
    <!-- Image/Cover Area (Left Side) -->
    <div class="sm:w-2/5 sm:min-h-[160px] h-32 bg-gradient-to-br from-green-400 to-emerald-600 p-5 flex flex-col justify-between relative overflow-hidden flex-shrink-0">
        <div class="absolute right-0 bottom-0 opacity-10 translate-x-4 translate-y-4">
            <x-heroicon-o-chat-bubble-left-right class="w-24 h-24" />
        </div>
        <div class="flex justify-between items-start z-10">
            <span class="bg-white/95 text-[#1da851] text-[9px] font-extrabold px-2 py-1 rounded-full uppercase tracking-wider">
                {{ $post->blogCategory->name ?? 'Geral' }}
            </span>
            <span class="text-white/80 text-[10px] font-semibold flex items-center gap-1">
                <x-heroicon-o-eye class="w-3.5 h-3.5" /> {{ number_format($post->views) }} views
            </span>
        </div>
        <div class="z-10 mt-2 sm:mt-0">
            <span class="text-[10px] text-white/90 font-bold block">
                {{ $post->created_at->translatedFormat('d \d\e F') }}
            </span>
        </div>
    </div>

    <!-- Content Area (Right Side) -->
    <div class="p-5 flex-1 flex flex-col justify-between">
        <div>
            <h2 class="text-slate-900 font-extrabold text-sm sm:text-base line-clamp-2 leading-tight mb-2 group-hover:text-[#25D366] transition-colors relative z-10">
                {{ $post->title }}
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed line-clamp-2 mb-4">
                {{ $post->meta_description }}
            </p>
        </div>
        
        <div class="flex items-center justify-between pt-4 border-t border-slate-50 mt-auto relative z-30">
            <!-- Share buttons here -->
            <div class="flex items-center gap-3">
                <a href="https://api.whatsapp.com/send?text={{ rawurlencode($post->title . ' ' . url('/blog/'.$post->slug)) }}" target="_blank" class="text-slate-400 hover:text-[#25D366] transition-colors" title="WhatsApp"><svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97-1.863-1.868-4.343-2.898-6.978-2.9-5.437 0-9.864 4.37-9.869 9.8-.001 1.76.476 3.476 1.385 4.982L1.8 22.282l5.05-1.326l-.203-.122zm10.743-6.611c-.301-.15-1.78-.879-2.056-.979-.275-.1-.475-.15-.675.15-.2.3-.775 1.01-1.038 1.3-.263.29-.525.32-.825.17-1.4-.7-2.312-1.28-3.138-2.7-.22-.38.22-.35.63-.78.18-.19.3-.3.45-.45.15-.15.2-.25.3-.45.1-.2.05-.375-.025-.525-.075-.15-.675-1.625-.925-2.225-.244-.589-.48-.51-.662-.519c-.171-.009-.367-.01-.563-.01c-.196 0-.516.07-.786.37-.27.3-1.03 1.01-1.03 2.46c0 1.45 1.05 2.85 1.2 3.05.15.2 2.07 3.15 5.007 4.42c.699.302 1.243.483 1.668.619.702.223 1.342.192 1.847.116.563-.085 1.78-.729 2.03-1.43c.25-.7.25-1.3.175-1.43-.075-.13-.275-.205-.575-.355z"/></svg></a>
                <a href="https://t.me/share/url?url={{ urlencode(url('/blog/'.$post->slug)) }}&text={{ rawurlencode($post->title) }}" target="_blank" class="text-slate-400 hover:text-[#0088cc] transition-colors" title="Telegram"><svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.892-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/blog/'.$post->slug)) }}" target="_blank" class="text-slate-400 hover:text-[#1877F2] transition-colors" title="Facebook"><svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                <div x-data="{ copied: false }" class="relative flex">
                    <button @click="navigator.clipboard.writeText('{{ url('/blog/'.$post->slug) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="text-slate-400 hover:text-slate-700 transition-colors" title="Copiar link">
                        <x-heroicon-o-link class="w-4 h-4" />
                    </button>
                    <span x-show="copied" x-cloak style="display: none;" class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-slate-800 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap">Copiado!</span>
                </div>
            </div>
            
            <span class="text-[10px] font-bold text-[#25D366] group-hover:text-[#1da851] transition-colors inline-flex items-center gap-0.5 uppercase tracking-wider relative z-10">
                Ler Artigo <x-heroicon-m-chevron-right class="w-3.5 h-3.5" />
            </span>
        </div>
    </div>
</article>
