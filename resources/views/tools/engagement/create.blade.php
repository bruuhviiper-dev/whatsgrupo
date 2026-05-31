@extends('layouts.analyzer')

@section('navbar_color', 'bg-[#16a34a]')

@section('title', 'Analisador de Engajamento de Grupo | WhatsGrupos')
@section('description', 'Descubra o engajamento e a saúde do seu grupo de WhatsApp com nossa Inteligência Artificial.')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-[24px] bg-green-50 border-2 border-green-100 text-green-600 mb-6 shadow-[0_0_25px_rgba(34,197,94,0.2)] rotate-3">
            <x-heroicon-s-chart-bar class="w-10 h-10 -rotate-3" />
        </div>
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">
            Analisador de <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-500 to-emerald-600">Engajamento</span>
        </h1>
        <p class="text-slate-500 text-sm sm:text-base max-w-2xl mx-auto">
            Gere um relatório instantâneo sobre a saúde e o engajamento do seu grupo. Descubra os pontos fortes, pontos de atenção e receba uma nota de 0 a 10 para compartilhar com os seus membros!
        </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-sm">
        <form action="{{ route('tools.engagement.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="group_name" class="block text-sm font-bold text-slate-700 mb-2">Nome ou Link de Convite do Grupo</label>
                <input type="text" name="group_name" id="group_name" required maxlength="200"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all shadow-sm"
                    placeholder="Ex: Jogadores de Blox Fruits BR ou https://chat.whatsapp.com/..."
                    value="{{ old('group_name') }}">
                @error('group_name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="mb-8">
                <label for="category_id" class="block text-sm font-bold text-slate-700 mb-2">Categoria do Grupo</label>
                <select name="category_id" id="category_id" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all shadow-sm">
                    <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Selecione a categoria principal...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-8 flex gap-3 text-sm text-blue-800">
                <x-heroicon-s-information-circle class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                <p>Nossa IA analisará os padrões estatísticos da categoria escolhida cruzando com o nome do seu grupo para gerar uma projeção realista de engajamento.</p>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-black text-lg py-4 rounded-xl shadow-lg shadow-green-500/30 flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                <x-heroicon-s-sparkles class="w-6 h-6" />
                Gerar Relatório Grátis
            </button>
            <p class="text-center text-xs text-slate-400 font-medium mt-4 flex items-center justify-center gap-1">
                <x-heroicon-s-lock-closed class="w-3 h-3" /> Seguro, instantâneo e 100% gratuito
            </p>
        </form>
    </div>
</div>

<div class="mt-12 max-w-4xl mx-auto">
    <x-publish-invite />
</div>
@endsection



