@extends('layouts.figurinhas')

@section('navbar_color', 'bg-[#15803d]')

@section('title', 'Enviar Figurinha para WhatsApp | WhatsGrupos')
@section('description', 'Envie sua figurinha para o nosso banco de figurinhas do WhatsApp. Aprovação rápida e gratuita!')

@section('content')
<div class="mb-6">
    <a href="{{ route('figurinhas.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
        <x-heroicon-s-arrow-left class="w-4 h-4" /> Voltar para figurinhas
    </a>
</div>

<div class="max-w-xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6">
            <h1 class="text-2xl font-black text-white mb-1">Enviar Figurinha</h1>
            <p class="text-green-100 text-sm font-medium">Contribua com a comunidade! Após envio, a figurinha passa por moderação.</p>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
                    <ul class="text-red-700 text-sm font-medium space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2"><x-heroicon-s-x-circle class="w-4 h-4 flex-shrink-0" /> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('figurinhas.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Título -->
                <div>
                    <label for="titulo" class="block text-sm font-bold text-slate-700 mb-1.5">Título da figurinha <span class="text-red-500">*</span></label>
                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}" maxlength="60"
                           placeholder="Ex: Cachorro animado dançando"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 font-medium outline-none focus:border-green-400 focus:ring-4 focus:ring-green-50 transition-all @error('titulo') border-red-300 bg-red-50 @enderror">
                    <p class="text-xs text-slate-400 mt-1">Mínimo 3, máximo 60 caracteres.</p>
                </div>

                <!-- Categoria -->
                <div>
                    <label for="categoria" class="block text-sm font-bold text-slate-700 mb-1.5">Categoria <span class="text-red-500">*</span></label>
                    <select id="categoria" name="categoria"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 font-medium outline-none focus:border-green-400 focus:ring-4 focus:ring-green-50 transition-all bg-white @error('categoria') border-red-300 bg-red-50 @enderror">
                        <option value="">Selecione uma categoria...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->value }}" {{ old('categoria') === $cat->value ? 'selected' : '' }}>
                                {{ $cat->emoji() }} {{ $cat->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-bold text-slate-700 mb-1.5">Tags <span class="text-slate-400 font-normal">(opcional)</span></label>
                    <input type="text" id="tags" name="tags" value="{{ old('tags') }}" maxlength="255"
                           placeholder="Ex: engraçado, meme, cachorro, dançando"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 font-medium outline-none focus:border-green-400 focus:ring-4 focus:ring-green-50 transition-all @error('tags') border-red-300 bg-red-50 @enderror">
                    <p class="text-xs text-slate-400 mt-1">Separe as palavras-chave com vírgula para ajudar na busca.</p>
                </div>

                <!-- Arquivo -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Arquivo da figurinha <span class="text-red-500">*</span></label>
                    <label for="arquivo" class="flex flex-col items-center justify-center gap-3 w-full border-2 border-dashed border-slate-200 hover:border-green-400 rounded-2xl p-8 cursor-pointer transition-all group bg-slate-50 hover:bg-green-50 @error('arquivo') border-red-300 bg-red-50 @enderror" id="upload-label">
                        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center group-hover:border-green-300 transition-all shadow-sm" id="preview-container">
                            <x-heroicon-o-photo class="w-7 h-7 text-slate-300 group-hover:text-green-400 transition-colors" id="upload-icon" />
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-bold text-slate-700 group-hover:text-green-700 transition-colors">Clique para selecionar a imagem</p>
                            <p class="text-xs text-slate-400 mt-0.5">PNG, JPG, GIF ou WebP — Máx. 5 MB</p>
                        </div>
                        <input type="file" id="arquivo" name="arquivo" accept="image/png,image/jpeg,image/gif,image/webp" class="hidden">
                    </label>
                    <p class="text-xs text-slate-400 mt-1.5">A imagem será processada e convertida para WebP 512×512px automaticamente.</p>
                </div>

                <!-- Aviso -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex gap-3">
                    <x-heroicon-s-information-circle class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="text-sm font-bold text-amber-800">Antes de enviar</p>
                        <p class="text-xs text-amber-700 mt-0.5">Ao enviar, você confirma que tem os direitos sobre esta imagem ou que ela está sob licença livre. Conteúdo impróprio ou com direitos autorais será removido.</p>
                    </div>
                </div>

                <!-- Botão -->
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-2xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-base">
                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                    Enviar Figurinha
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('arquivo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const label = document.getElementById('upload-label');
    const container = document.getElementById('preview-container');

    const reader = new FileReader();
    reader.onload = function(ev) {
        container.innerHTML = `<img src="${ev.target.result}" class="w-14 h-14 object-contain rounded-xl">`;
        label.querySelector('p').textContent = file.name;
    };
    reader.readAsDataURL(file);
});
</script>
@endsection

