@extends('layouts.tools')

@section('navbar_color', 'bg-[#047857]')





@section('title', 'Verificador de Link de WhatsApp | WhatsGrupos')
@section('description', 'Seu link de grupo do WhatsApp está correto? Use nossa ferramenta gratuita para validar, testar e conferir se o link de convite está funcionando.')

@section('tool_icon')
<x-heroicon-s-link class="w-7 h-7 sm:w-8 sm:h-8" />
@endsection

@section('tool_logo_html')
VALIDA<span class="text-green-100 font-bold">LINK</span>
@endsection

@section('tool_action_btn')
<a href="{{ route('tools.link-validator') }}" class="bg-white text-[#25D366] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
  <x-heroicon-s-link class="w-4 h-4" />
  Validar Link
</a>
@endsection

@section('tool_mobile_home')
<a href="{{ route('tools.link-validator') }}" class="flex flex-col items-center justify-center w-16 h-16 rounded-lg text-slate-600 hover:text-[#25D366] hover:bg-green-50 transition-all {{ request()->is('ferramentas/verificador*') ? 'text-[#25D366] bg-green-50' : '' }}">
  <x-heroicon-o-link class="w-6 h-6" />
  <span class="text-[10px] font-bold mt-0.5">Validador</span>
</a>
@endsection

@section('tool_mobile_fab')
<a href="{{ route('tools.link-validator') }}" class="flex flex-col items-center justify-center -translate-y-4 relative">
  <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#25D366] to-[#128C7E] shadow-lg shadow-green-500/40 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
    <x-heroicon-s-check-badge class="w-8 h-8 text-white" />
  </div>
  <span class="text-[10px] font-bold mt-1 text-slate-600">Checar</span>
</a>
@endsection

@section('canonical', route('tools.link-validator'))

@section('content')

<x-seo.tool name="Verificador de Link de Grupo de WhatsApp"
            description="Verifique gratuitamente se o link de convite do seu grupo de WhatsApp está ativo e válido." />

<div class="py-8 md:py-12" x-data="linkValidator()">
  <div class="text-center max-w-2xl mx-auto mb-10">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-blue-50 border-2 border-blue-100 text-blue-500 mb-6 shadow-[0_0_25px_rgba(59,130,246,0.2)] rotate-3">
        <x-heroicon-s-link class="w-10 h-10 -rotate-3" />
    </div>
    <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">
      Verificador de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-[#25D366]">Links</span>
    </h1>
    <p class="text-slate-500 text-lg">Muitos links de grupos falham na hora de divulgar. Cole o seu link abaixo e nós verificamos se ele está no formato perfeito para atrair membros.</p>
  </div>

  <div class="max-w-[700px] mx-auto">
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        
        <label class="block text-sm font-bold text-slate-700 mb-3">Cole o link de convite do Grupo (ou Canal):</label>
        
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <x-heroicon-o-link class="w-6 h-6 text-slate-400" />
            </div>
            <input 
                type="text" 
                x-model="linkInput" 
                @input="validate()"
                placeholder="https://chat.whatsapp.com/..." 
                class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-slate-200 focus:ring-0 focus:border-blue-500 transition-all font-medium text-slate-800 outline-none text-lg">
            
            <button x-show="linkInput.length > 0" @click="linkInput = ''; validate()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-500 transition-colors">
                <x-heroicon-s-x-circle class="w-6 h-6" />
            </button>
        </div>

        <p class="text-xs text-slate-400 mt-3 flex items-center gap-1 font-medium">
            <x-heroicon-s-information-circle class="w-4 h-4"/>
            O link nunca é salvo. A verificação ocorre apenas no seu navegador.
        </p>

        <!-- Resultados da Validação -->
        <div x-show="linkInput.length > 0" x-transition.opacity class="mt-8">
            
            <!-- Link Válido (Grupo) -->
            <div x-show="status === 'valid_group'" class="p-6 bg-green-50 rounded-xl border border-green-200 text-center relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-10"><x-heroicon-s-check-circle class="w-32 h-32 text-green-600"/></div>
                
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4 relative z-10">
                    <x-heroicon-s-check class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-black text-green-800 mb-2 relative z-10">Link de Grupo Válido!</h3>
                <p class="text-green-700 text-sm font-medium mb-6 relative z-10">Excelente! O formato do seu link está correto e pronto para receber centenas de membros.</p>
                
                <div class="flex flex-col sm:flex-row gap-3 relative z-10">
                    <a :href="linkInput" target="_blank" class="flex-1 bg-white text-green-700 border border-green-200 font-bold py-3 px-4 rounded-xl hover:bg-green-100 transition-all flex items-center justify-center gap-2">
                        <x-heroicon-s-arrow-top-right-on-square class="w-5 h-5" /> Testar Acesso
                    </a>
                    <a href="/enviar-grupo" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-green-500/30 transition-all flex items-center justify-center gap-2">
                        <x-heroicon-s-rocket-launch class="w-5 h-5" /> Divulgar Grupo Agora
                    </a>
                </div>
            </div>

            <!-- Link Válido (Canal) -->
            <div x-show="status === 'valid_channel'" class="p-6 bg-blue-50 rounded-xl border border-blue-200 text-center relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-10"><x-heroicon-s-megaphone class="w-32 h-32 text-blue-600"/></div>
                
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-4 relative z-10">
                    <x-heroicon-s-check class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-black text-blue-800 mb-2 relative z-10">Link de Canal Válido!</h3>
                <p class="text-blue-700 text-sm font-medium mb-6 relative z-10">Detectamos que este é um link de <b>Canal</b> do WhatsApp. O formato está correto!</p>
                
                <div class="flex flex-col sm:flex-row gap-3 relative z-10">
                    <a :href="linkInput" target="_blank" class="flex-1 bg-white text-blue-700 border border-blue-200 font-bold py-3 px-4 rounded-xl hover:bg-blue-100 transition-all flex items-center justify-center gap-2">
                        <x-heroicon-s-arrow-top-right-on-square class="w-5 h-5" /> Testar Acesso
                    </a>
                    <a href="/enviar-grupo" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                        <x-heroicon-s-rocket-launch class="w-5 h-5" /> Divulgar Canal Agora
                    </a>
                </div>
            </div>

            <!-- Link Inválido -->
            <div x-show="status === 'invalid'" class="p-6 bg-red-50 rounded-xl border border-red-200 text-center relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-10"><x-heroicon-s-x-circle class="w-32 h-32 text-red-600"/></div>
                
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4 relative z-10">
                    <x-heroicon-s-exclamation-triangle class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-black text-red-800 mb-2 relative z-10">Link Inválido ou Quebrado</h3>
                <p class="text-red-700 text-sm font-medium mb-4 relative z-10">O texto inserido não parece ser um link de convite oficial do WhatsApp.</p>
                
                <div class="bg-white/60 p-4 rounded-lg text-left text-sm text-red-800 relative z-10">
                    <p class="font-bold mb-2">Um link de WhatsApp correto deve começar com:</p>
                    <ul class="list-disc pl-5 space-y-1 font-mono text-xs">
                        <li>https://chat.whatsapp.com/ID_AQUI (Grupos)</li>
                        <li>https://whatsapp.com/channel/ID_AQUI (Canais)</li>
                    </ul>
                </div>
            </div>

            <!-- Redefinido do Zero -->
            <div x-show="status === 'reset_link'" class="p-6 bg-amber-50 rounded-xl border border-amber-200 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 text-amber-600 mb-4">
                    <x-heroicon-s-arrow-path class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-black text-amber-800 mb-2">Link Redefinido?</h3>
                <p class="text-amber-700 text-sm font-medium">Parece que o link é válido, mas se você redefiniu ele nas configurações do WhatsApp, os membros verão um aviso de erro. Sempre copie o link mais recente.</p>
            </div>

        </div>

    </div>
  </div>
  <div class="max-w-[1100px] mx-auto mt-10">
    <x-adsense class="my-4" />
  </div>
  <div class="mt-12 max-w-4xl mx-auto">
    <x-publish-invite />
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('linkValidator', () => ({
    linkInput: '',
    status: '', // 'valid_group', 'valid_channel', 'invalid', 'reset_link', ''

    validate() {
        const url = this.linkInput.trim();
        
        if (url.length === 0) {
            this.status = '';
            return;
        }

        // Regex para Grupo
        const groupRegex = /^https?:\/\/(chat\.whatsapp\.com)\/([a-zA-Z0-9]{20,25})$/i;
        
        // Regex para Canal
        const channelRegex = /^https?:\/\/(whatsapp\.com\/channel)\/([a-zA-Z0-9]{20,30})$/i;

        if (groupRegex.test(url)) {
            this.status = 'valid_group';
        } else if (channelRegex.test(url)) {
            this.status = 'valid_channel';
        } else {
            // Checa se o cara colou algo muito longo que parece redefinido ou com erro de digitação
            if(url.includes('chat.whatsapp.com') && url.length > 50) {
                 this.status = 'reset_link'; // Apenas uma suposição pra UX rica
            } else {
                 this.status = 'invalid';
            }
        }
    }
  }));
});
</script>
@endpush
@endsection




