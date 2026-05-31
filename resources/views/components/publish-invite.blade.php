<div {{ $attributes->merge(['class' => 'bg-gradient-to-r from-[#25D366] to-[#128C7E] rounded-2xl p-6 md:p-8 shadow-lg shadow-green-500/20 text-white relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6']) }}>
    <!-- Fundo decorativo -->
    <div class="absolute -right-10 -top-10 opacity-10 pointer-events-none">
        <x-heroicon-s-rocket-launch class="w-48 h-48" />
    </div>

    <!-- Conteúdo Textual -->
    <div class="relative z-10 flex-1 text-center md:text-left">
        <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
            <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-black uppercase tracking-widest px-2 py-1 rounded-md flex items-center gap-1">
                <x-heroicon-s-star class="w-3 h-3 text-yellow-300" /> Em Alta
            </span>
        </div>
        <h3 class="text-xl md:text-2xl font-black mb-2 leading-tight">Quer bombar o seu grupo?</h3>
        <p class="text-green-50 text-sm md:text-base font-medium max-w-xl">
            Junte-se a milhares de administradores e divulgue seu grupo ou canal gratuitamente no maior portal do Brasil.
        </p>
    </div>

    <!-- Botão CTA -->
    <div class="relative z-10 flex-shrink-0 w-full md:w-auto">
        <a href="/enviar-grupo" class="block w-full md:w-auto bg-white hover:bg-slate-50 text-green-700 font-black text-center px-6 py-4 rounded-xl shadow-xl transition-transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
            <x-heroicon-s-plus-circle class="w-6 h-6" />
            Publicar Meu Grupo
        </a>
    </div>
</div>
