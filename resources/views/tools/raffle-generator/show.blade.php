@extends('layouts.tools')

@section('navbar_color', 'bg-[#065f46]')

@section('title', "Resultado Oficial: " . ($raffle->title ?? 'Sorteio') . " | WhatsGrupos")
@section('tool_logo_html')
GERADOR<span class="text-emerald-100 font-bold">SORTEIO</span>
@endsection

@section('tool_action_btn')
<a href="{{ route('tools.raffle-generator') }}" class="bg-white text-[#25D366] px-4 py-2 rounded-md font-bold text-xs uppercase tracking-wider shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-1.5">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
    <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2Zm0 3a7 7 0 1 1 0 14A7 7 0 0 1 12 5Zm-1 3v3H8a1 1 0 1 0 0 2h3v3a1 1 0 1 0 2 0v-3h3a1 1 0 1 0 0-2h-3V8a1 1 0 1 0-2 0Z"/>
  </svg>
  Novo Sorteio
</a>
@endsection

@section('content')
<div class="py-8 md:py-12 relative overflow-hidden" x-data="showRaffle()">

  <div class="max-w-3xl mx-auto px-4 relative z-10">
    
    {{-- Header / Certificado --}}
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 mb-6 shadow-lg shadow-emerald-500/20 ring-4 ring-emerald-50">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10">
          <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/>
        </svg>
      </div>
      <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight mb-2">
        Sorteio Oficializado
      </h1>
      <p class="text-slate-500 font-medium text-lg">
        {{ $raffle->title ?? 'Sorteio de Grupo' }}
      </p>
    </div>

    {{-- Main Card --}}
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl relative overflow-hidden border border-slate-700">
      
      <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>

      {{-- Metadata do Sorteio --}}
      <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50 mb-8 relative z-10">
        <div>
          <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">ID de Verificação</p>
          <p class="text-emerald-400 font-mono text-sm sm:text-base font-bold bg-emerald-950/30 px-2 py-0.5 rounded border border-emerald-900/50">
            {{ strtoupper(explode('-', $raffle->uuid)[0]) }}
          </p>
        </div>
        <div class="grid grid-cols-2 gap-4 text-left sm:text-right w-full sm:w-auto">
          <div>
            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Data / Hora</p>
            <p class="text-white text-sm font-bold">{{ $raffle->created_at->format('d/m/Y H:i') }}</p>
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Participantes</p>
            <p class="text-white text-sm font-bold">{{ $raffle->total_participants }} concorrentes</p>
          </div>
        </div>
      </div>

      {{-- Vencedores --}}
      <div class="relative z-10 mb-8">
        <h3 class="text-center text-sm font-black uppercase tracking-widest text-emerald-400 mb-6">
          🏆 Vencedor{{ $raffle->winner_count > 1 ? 'es' : '' }} Oficia{{ $raffle->winner_count > 1 ? 'is' : 'l' }}
        </h3>
        
        <div class="space-y-3">
          @foreach($raffle->winners as $index => $winner)
            <div class="flex items-center gap-4 bg-gradient-to-r from-emerald-900/80 to-teal-900/80 p-4 rounded-2xl border border-emerald-700/50 shadow-inner">
              <div class="w-12 h-12 rounded-full bg-slate-900/50 flex items-center justify-center text-2xl shadow-lg border border-emerald-800/50 shrink-0">
                {{ $index === 0 ? '🥇' : ($index === 1 ? '🥈' : ($index === 2 ? '🥉' : '🏅')) }}
              </div>
              <div class="flex-1">
                <p class="text-white font-black text-xl md:text-2xl tracking-tight leading-none mb-1">{{ $winner }}</p>
                <p class="text-emerald-300 text-xs font-bold uppercase">{{ $index + 1 }}º Lugar</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Compartilhar / Ações --}}
      <div class="relative z-10 border-t border-slate-700 pt-6 mt-6 flex flex-col gap-3">
        @php
          $shareText = "🏆 *RESULTADO DO SORTEIO*\n" 
            . ($raffle->title ? "_{$raffle->title}_\n\n" : "\n")
            . "Vencedores:\n";
          foreach($raffle->winners as $i => $w) {
            $shareText .= ($i + 1) . "º: *$w*\n";
          }
          $shareText .= "\n🔗 *Comprovação Oficial:*\n" . url()->current();
        @endphp

        <div class="flex flex-col sm:flex-row gap-3">
          <a href="https://api.whatsapp.com/send?text={{ urlencode($shareText) }}" target="_blank"
             class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-black py-4 px-6 rounded-xl text-center transition-all shadow-lg shadow-emerald-900/50 flex items-center justify-center gap-2">
            Compartilhar no WhatsApp 📲
          </a>
          
          <button @click="copyLink"
                  class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-4 px-6 rounded-xl text-center transition-all border border-slate-600 shadow-sm flex items-center justify-center gap-2">
            <span x-show="!copied">Copiar Link do Sorteio 🔗</span>
            <span x-show="copied" x-cloak class="text-emerald-400">✅ Link Copiado!</span>
          </button>
        </div>

        {{-- Envio por Email --}}
        <div class="mt-4 bg-slate-800/50 rounded-xl p-4 border border-slate-700">
          <p class="text-sm text-slate-300 font-bold mb-3">Deseja receber uma cópia no seu e-mail?</p>
          <form @submit.prevent="sendEmail" class="flex gap-2 relative">
            <input type="email" x-model="email" placeholder="Seu e-mail..." required class="flex-1 px-4 py-3 rounded-lg bg-slate-900 border border-slate-600 text-white placeholder-slate-500 focus:ring-emerald-500 focus:border-emerald-500 outline-none text-sm transition-colors">
            <button type="submit" :disabled="sendingEmail" class="bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-white font-bold px-6 rounded-lg text-sm transition-all shadow-sm flex items-center justify-center min-w-[120px]">
              <span x-show="!sendingEmail && !emailSent">Enviar</span>
              <span x-show="sendingEmail" x-cloak>
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              </span>
              <span x-show="emailSent" x-cloak class="text-white">Enviado!</span>
            </button>
          </form>
          <p x-show="emailError" x-text="emailError" class="text-red-400 text-xs mt-2" x-cloak></p>
        </div>

      </div>

    </div>

    {{-- CTA para nova ferramenta --}}
    <div class="mt-8 text-center bg-emerald-50 border border-emerald-100 rounded-3xl p-6 sm:p-8">
      <div class="text-3xl mb-3">🎲</div>
      <h3 class="text-lg font-black text-slate-800 mb-2">Quer fazer seu próprio sorteio?</h3>
      <p class="text-slate-500 text-sm mb-6 max-w-sm mx-auto">Use a ferramenta de Sorteios do WhatsGrupos para grupos grandes, exportação por CSV e roleta animada totalmente grátis.</p>
      <a href="{{ route('tools.raffle-generator') }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold px-8 py-3 rounded-xl transition-all shadow-md hover:-translate-y-0.5">
        Criar Sorteio Grátis
      </a>
    </div>

  </div>

  <div class="max-w-[1100px] mx-auto mt-10">
    <x-adsense class="my-4" />
  </div>
  <div class="mt-6 max-w-4xl mx-auto">
    <x-publish-invite />
  </div>

</div>
@endsection

@push('head')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('showRaffle', () => ({
    copied: false,
    email: '',
    sendingEmail: false,
    emailSent: false,
    emailError: '',
    
    copyLink() {
      navigator.clipboard.writeText('{{ url()->current() }}');
      this.copied = true;
      setTimeout(() => this.copied = false, 2500);
    },

    async sendEmail() {
      if (!this.email || this.sendingEmail) return;
      this.sendingEmail = true;
      this.emailError = '';
      this.emailSent = false;

      try {
        const response = await fetch('{{ route('tools.raffle.email', $raffle->uuid) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ email: this.email })
        });
        
        const data = await response.json();
        if (data.success) {
          this.emailSent = true;
          this.email = '';
          setTimeout(() => this.emailSent = false, 5000);
        } else {
          this.emailError = data.error || 'Erro ao enviar e-mail.';
        }
      } catch (err) {
        this.emailError = 'Erro de conexão.';
      }
      this.sendingEmail = false;
    }
  }));
});
</script>
@endpush
