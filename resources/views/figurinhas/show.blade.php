@extends('layouts.figurinhas')

@section('navbar_color', 'bg-[#15803d]')

@section('title', "Figurinha: {$figurinha->titulo} para WhatsApp | WhatsGrupos")
@section('description', "Baixe a figurinha {$figurinha->titulo} na categoria {$figurinha->categoria->label()} e envie no seu WhatsApp.")

@section('content')

<div class="mb-6">
    <a href="{{ route('figurinhas.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
        <x-heroicon-s-arrow-left class="w-4 h-4" /> Voltar para figurinhas
    </a>
</div>

<x-adsense class="mb-8" />

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 mb-12">
    <!-- Lado Esquerdo: Imagem e Editor -->
    <div class="bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-3xl p-8 shadow-sm flex flex-col items-center justify-center relative overflow-hidden" x-data="stickerEditor()">
        <!-- Container Principal de Preview -->
        <div id="meme-preview-container" class="relative inline-flex items-center justify-center w-full max-w-[450px] rounded-xl overflow-hidden transition-all duration-300">
            <img src="{{ $figurinha->url_arquivo }}" alt="{{ $figurinha->titulo }}" id="sticker-image" crossorigin="anonymous" class="w-full h-auto object-contain drop-shadow-2xl z-10 relative">
            
            <!-- Overlay de Texto Topo Nativo -->
            <div x-show="isEditing && textTop" x-transition.opacity class="absolute top-0 inset-x-0 flex flex-col items-center p-4 z-20 pointer-events-none">
                <p x-text="textTop" class="text-center w-full whitespace-pre-wrap leading-none tracking-wide"
                   :style="'font-family: Impact, Arial, sans-serif; font-size: clamp(24px, 8vw, 40px); font-weight: bold; color: ' + textColor + '; -webkit-text-stroke: 1.5px black; text-transform: uppercase;'">
                </p>
            </div>

            <!-- Overlay de Texto Fundo Nativo -->
            <div x-show="isEditing && textBottom" x-transition.opacity class="absolute bottom-0 inset-x-0 flex flex-col items-center p-4 z-20 pointer-events-none">
                <p x-text="textBottom" class="text-center w-full whitespace-pre-wrap leading-none tracking-wide"
                   :style="'font-family: Impact, Arial, sans-serif; font-size: clamp(24px, 8vw, 40px); font-weight: bold; color: ' + textColor + '; -webkit-text-stroke: 1.5px black; text-transform: uppercase;'">
                </p>
            </div>
        </div>
        
        <!-- Botão para abrir o editor -->
        <button x-show="!isEditing" @click="isEditing = true" class="mt-6 px-5 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-bold shadow-md hover:bg-slate-800 transition-all flex items-center gap-2 active:scale-95">
            <x-heroicon-s-pencil class="w-4 h-4" /> Personalizar com Texto
        </button>

        <!-- Controles do Editor -->
        <div x-show="isEditing" x-cloak class="mt-6 w-full flex flex-col gap-4 relative z-10 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Texto Superior</label>
                    <textarea x-model="textTop" placeholder="Topo..." rows="2" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm font-medium outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Texto Inferior</label>
                    <textarea x-model="textBottom" placeholder="Fundo..." rows="2" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm font-medium outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all resize-none"></textarea>
                </div>
            </div>
            
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500">Cor do Texto:</label>
                    <input type="color" x-model="textColor" class="w-8 h-8 p-0 border-0 rounded cursor-pointer ring-1 ring-slate-200 shadow-sm">
                </div>
            </div>

            <div class="border-t border-slate-100 my-1"></div>

            <div class="grid grid-cols-2 gap-3">
                <button @click="downloadMeme()" :disabled="isDownloading" class="flex items-center justify-center gap-1.5 bg-slate-900 text-white text-sm font-bold rounded-xl py-3 hover:bg-slate-800 transition-all shadow-sm active:scale-95 disabled:opacity-50">
                    <x-heroicon-s-arrow-down-tray class="w-4 h-4" x-show="!isDownloading" />
                    <svg x-show="isDownloading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="isDownloading ? 'Gerando...' : 'Salvar Meme'"></span>
                </button>
                <button @click="isEditing = false" class="flex items-center justify-center gap-1.5 bg-slate-100 text-slate-600 border border-slate-200 text-sm font-bold rounded-xl py-3 hover:bg-slate-200 transition-all active:scale-95">
                    <x-heroicon-s-x-mark class="w-4 h-4" /> Fechar
                </button>
            </div>
        </div>
    </div>
    
    <!-- Lado Direito: Informações e Ações -->
    <div class="flex flex-col justify-center">
        <div class="mb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-full text-xs font-bold text-slate-700 uppercase tracking-wider mb-4">
                {{ $figurinha->categoria->emoji() }} {{ $figurinha->categoria->label() }}
            </span>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 mb-2 leading-tight">
                {{ $figurinha->titulo }}
            </h1>
            <div class="flex items-center gap-4 text-sm font-bold text-slate-500">
                <span class="flex items-center gap-1.5"><x-heroicon-s-eye class="w-5 h-5 text-slate-300" /> {{ number_format($figurinha->visualizacoes, 0, '', '.') }} views</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="flex items-center gap-1.5"><x-heroicon-s-arrow-down-tray class="w-5 h-5 text-slate-300" /> {{ number_format($figurinha->downloads, 0, '', '.') }} downloads</span>
            </div>
        </div>
        
        @if(!empty($figurinha->tags))
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach($figurinha->tags as $tag)
                    <span class="px-2.5 py-1 bg-slate-50 border border-slate-100 rounded-lg text-xs font-bold text-slate-500">#{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col gap-3">
            <a href="{{ route('figurinhas.download', $figurinha->slug) }}" class="flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-black px-8 py-4 rounded-2xl transition-all shadow-md text-lg">
                <x-heroicon-s-arrow-down-tray class="w-6 h-6" /> Baixar Figurinha
            </a>
            
            <div class="grid grid-cols-2 gap-3">
                <a href="https://wa.me/?text={{ urlencode("Baixe a figurinha {$figurinha->titulo} grátis no WhatsGrupos: " . url()->current()) }}" target="_blank" class="flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1DA851] text-white font-bold px-4 py-3.5 rounded-2xl transition-all shadow-sm">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                    WhatsApp
                </a>
                
                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 2000)" class="flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-bold px-4 py-3.5 rounded-2xl border border-slate-200 transition-all shadow-sm">
                    <span x-show="!copied" class="flex items-center gap-2"><x-heroicon-o-link class="w-5 h-5" /> Copiar Link</span>
                    <span x-show="copied" x-cloak class="flex items-center gap-2 text-green-600"><x-heroicon-s-check class="w-5 h-5" /> Copiado!</span>
                </button>
            </div>
        </div>
    </div>
</div>

<x-adsense class="mb-12" />

<!-- Figurinhas Relacionadas -->
@if($relacionadas->isNotEmpty())
    <div class="mb-12">
        <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
            <x-heroicon-s-sparkles class="w-6 h-6 text-amber-400" /> Mais figurinhas de {{ $figurinha->categoria->label() }}
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            @foreach($relacionadas as $rel)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col">
                <a href="{{ route('figurinhas.show', $rel->slug) }}" class="block relative w-full aspect-square bg-gradient-to-br from-slate-50 to-slate-100 overflow-hidden group-hover:from-slate-100 group-hover:to-slate-200 transition-colors">
                    <img src="{{ $rel->url_arquivo }}" alt="{{ $rel->titulo }}" class="absolute inset-0 w-full h-full object-contain p-4 drop-shadow-md group-hover:scale-110 transition-transform duration-300">
                </a>
                <div class="p-3 border-t border-slate-100 flex-1 flex flex-col justify-center">
                    <a href="{{ route('figurinhas.show', $rel->slug) }}" class="text-xs font-bold text-slate-900 truncate hover:text-green-600 transition-colors" title="{{ $rel->titulo }}">
                        {{ $rel->titulo }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('stickerEditor', () => ({
        isEditing: false,
        textTop: '',
        textBottom: '',
        textColor: '#FFFFFF',
        isDownloading: false,

        drawText(ctx, text, img, isTop) {
            if (!text.trim()) return;
            const fontSize = Math.max(20, Math.floor(img.height * 0.1));
            ctx.font = 'bold ' + fontSize + 'px Impact, Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = isTop ? 'top' : 'bottom';
            ctx.fillStyle = this.textColor;
            ctx.strokeStyle = 'black';
            ctx.lineWidth = Math.max(1, Math.floor(fontSize / 15));
            
            const textLines = text.toUpperCase().split('\n');
            const lineHeight = fontSize * 1.1;
            
            const linesToDraw = [];
            textLines.forEach(line => {
                const words = line.split(' ');
                let currentLine = '';
                for (let n = 0; n < words.length; n++) {
                    const testLine = currentLine + words[n] + ' ';
                    if (ctx.measureText(testLine).width > (img.width * 0.9) && n > 0) {
                        linesToDraw.push(currentLine.trim());
                        currentLine = words[n] + ' ';
                    } else {
                        currentLine = testLine;
                    }
                }
                linesToDraw.push(currentLine.trim());
            });
            
            const totalTextHeight = linesToDraw.length * lineHeight;
            let startY = isTop ? img.height * 0.05 : img.height * 0.95;
            
            // Adjust startY for bottom text so it draws upwards
            if (!isTop) {
                // Because textBaseline is bottom, each line draws upwards from its y.
                // To stack them properly from top to bottom, we calculate the highest y:
                startY = img.height * 0.95 - totalTextHeight + lineHeight;
            }
            
            const x = img.width / 2;
            
            linesToDraw.forEach((line, i) => {
                const y = isTop ? startY + (i * lineHeight) : startY + (i * lineHeight);
                ctx.strokeText(line, x, y);
                ctx.fillText(line, x, y);
            });
        },

        downloadMeme() {
            this.isDownloading = true;
            
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            const img = new Image();
            img.crossOrigin = "anonymous";
            
            img.onload = () => {
                canvas.width = img.width;
                canvas.height = img.height;
                
                ctx.drawImage(img, 0, 0);
                
                this.drawText(ctx, this.textTop, img, true);
                this.drawText(ctx, this.textBottom, img, false);
                
                // Exporta e baixa
                const dataURL = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = '{{ $figurinha->slug }}-meme.png';
                link.href = dataURL;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                this.isDownloading = false;
            };
            
            // Se falhar ao carregar (por ex. erro de CORS inesperado), fallback elegante
            img.onerror = () => {
                alert('Ocorreu um erro ao gerar a figurinha de alta resolução. Tente novamente.');
                this.isDownloading = false;
            };
            
            // Inicia o carregamento
            img.src = '{{ $figurinha->url_arquivo }}';
        }
    }));
});
</script>
@endpush

