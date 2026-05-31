<footer class="bg-slate-900 border-t border-slate-800 pt-16 pb-8 mt-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <!-- Brand & Description -->
            <div class="col-span-1 md:col-span-2">
                <a href="/" class="flex items-center gap-2 mb-4">
                    <x-heroicon-s-chat-bubble-oval-left-ellipsis class="w-8 h-8 text-[#25D366]" />
                    <span class="text-2xl font-black tracking-tighter text-white">Whats<span class="text-[#25D366]">Grupos</span></span>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed max-w-sm mb-6">
                    O maior e mais atualizado diretório público para encontrar e entrar em links de grupos de WhatsApp reais do Brasil.
                </p>
                <div class="flex gap-4">
                    <!-- Social placeholders -->
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-[#25D366] hover:text-white transition-all">
                        <x-heroicon-s-globe-alt class="w-5 h-5" />
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-[#25D366] hover:text-white transition-all">
                        <x-heroicon-s-envelope class="w-5 h-5" />
                    </a>
                </div>
            </div>

            <!-- Links Uteis -->
            <div>
                <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Links Úteis</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Grupos de WhatsApp</a></li>
                    <li><a href="/pacotes-vip" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium flex items-center gap-2">Pacotes VIP <x-heroicon-s-star class="w-3 h-3 text-amber-500" /></a></li>
                    <li><a href="/blog" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Blog e Tutoriais</a></li>
                    <li><a href="/faq" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Perguntas Frequentes</a></li>
                </ul>
            </div>

            <!-- Ferramentas -->
            <div>
                <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Ferramentas</h4>
                <ul class="space-y-3">
                    <li><a href="/figurinhas" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Figurinhas para WhatsApp</a></li>
                    <li><a href="/frases" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Frases para Status</a></li>
                    <li><a href="/ferramentas/analise-de-engajamento" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Análise de Engajamento</a></li>
                    <li><a href="/ferramentas/gerador-de-regras" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Gerador de Regras</a></li>
                    <li><a href="{{ route('tools.name-generator') }}" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Gerador de Nomes</a></li>
                    <li><a href="{{ route('tools.welcome-message') }}" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Msg de Boas-Vindas</a></li>
                    <li><a href="{{ route('tools.link-validator') }}" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Verificador de Link</a></li>
                    <li><a href="{{ route('tools.poll-generator') }}" class="text-slate-400 hover:text-[#25D366] transition-colors text-sm font-medium">Gerador de Enquete</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-xs text-slate-500 font-medium">
                &copy; {{ date('Y') }} WhatsGrupos — Todos os direitos reservados.
            </p>
            <div class="flex items-center gap-6">
                <a href="/termos-de-uso" class="text-xs font-medium text-slate-500 hover:text-slate-300 transition-colors">Termos de Uso</a>
                <a href="/politica-de-privacidade" class="text-xs font-medium text-slate-500 hover:text-slate-300 transition-colors">Política de Privacidade</a>
                <a href="/contato" class="text-xs font-medium text-slate-500 hover:text-slate-300 transition-colors">Contato</a>
            </div>
        </div>
    </div>
</footer>
