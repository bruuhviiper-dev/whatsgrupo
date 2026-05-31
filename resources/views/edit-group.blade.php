@extends('layouts.app')
@section('title', 'Editar Grupo — WhatsGrupos')
@section('description', 'Edite as informações do seu grupo enquanto ele está pendente de aprovação.')

@section('content')

<div class="max-w-xl mx-auto px-4 py-8">
  
  <div class="mb-8 text-center flex flex-col items-center">
    <a href="{{ route('my-groups') }}" class="text-sm font-bold text-slate-500 hover:text-slate-900 mb-4 inline-flex items-center gap-1 transition-colors">
      <x-heroicon-m-arrow-left class="w-4 h-4" /> Voltar para Meus Grupos
    </a>
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">
      Editar Grupo
    </h1>
    <p class="text-slate-600 text-sm">
      Atualize as informações do seu grupo antes da nossa equipe analisá-lo.
    </p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-sm">
    
    <div x-data="{
      link: '{{ old('whatsapp_link', $group->whatsapp_link) }}',
      loading: false,
      detected: true,
      detectedName: '',
      detectedImage: '',
      error: '',
      
      async validateLink() {
        if (!this.link.includes('chat.whatsapp.com') && !this.link.includes('whatsapp.com/channel')) {
          this.error = 'Por favor, insira um link oficial (chat.whatsapp.com ou whatsapp.com/channel).'
          this.detected = false
          return
        }
        this.loading = true
        this.error = ''
        try {
          const res = await fetch('/api/validate-link', {
            method: 'POST',
            headers: {
              'Content-Type':'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({link: this.link})
          })
          const data = await res.json()
          if (data.valid) {
            this.detected = true
            this.detectedName = data.name || ''
            this.detectedImage = data.image || ''
            if (data.name) document.getElementById('nameInput').value = data.name
          } else {
            this.error = data.error || 'Este link é inválido ou foi revogado.'
            this.detected = false
          }
        } catch(e) {
          this.error = 'Erro ao validar o link. Verifique a conexão.'
        } finally {
          this.loading = false
        }
      }
    }">

      <form action="{{ route('my-groups.update', $group) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Link -->
        <div class="space-y-1.5">
          <label for="link_input" class="block text-sm font-semibold text-slate-700">Link de Convite *</label>
          <div class="flex gap-2">
            <input
              id="link_input"
              type="url"
              name="whatsapp_link"
              x-model="link"
              placeholder="https://chat.whatsapp.com/..."
              required
              class="w-full bg-slate-50 border rounded-lg px-4 py-3 text-slate-900 outline-none transition-colors border-slate-300 focus:border-green-500"
            />
            <button
              type="button"
              x-on:click="validateLink()"
              :disabled="loading"
              class="bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg px-6 transition-colors disabled:opacity-50">
              <span x-show="!loading">Verificar</span>
              <span x-show="loading">...</span>
            </button>
          </div>
          <p x-show="error" x-text="error" class="text-sm text-red-500 mt-1" style="display: none;"></p>
        </div>

        <!-- Categoria -->
        <div class="space-y-1.5">
          <label for="category_input" class="block text-sm font-semibold text-slate-700">Categoria *</label>
          <select id="category_input" name="category_id" required
                  class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-900 outline-none focus:border-green-500 transition-colors">
            <option value="">Selecione uma categoria...</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id', $group->category_id) == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Nome -->
        <div class="space-y-1.5">
          <label for="nameInput" class="block text-sm font-semibold text-slate-700">Nome do Grupo *</label>
          <input
            id="nameInput"
            type="text"
            name="name"
            maxlength="100"
            required
            value="{{ old('name', $group->name) }}"
            placeholder="Nome do grupo"
            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-900 outline-none focus:border-green-500 transition-colors"
          />
        </div>

        <!-- Descrição -->
        <div class="space-y-1.5">
          <label for="desc_input" class="block text-sm font-semibold text-slate-700">Descrição *</label>
          <textarea
            id="desc_input"
            name="description"
            required
            minlength="20"
            rows="3"
            placeholder="Do que se trata o grupo? (Mínimo de 20 caracteres)"
            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-900 outline-none focus:border-green-500 transition-colors resize-y"
          >{{ old('description', $group->description) }}</textarea>
        </div>

        <!-- Imagem Atual / Nova Imagem -->
        <div class="space-y-1.5">
          <label for="image_input" class="block text-sm font-semibold text-slate-700">Imagem (Opcional)</label>
          @if ($group->image_path)
            <div class="flex items-center gap-4 mb-3 p-3 border border-slate-200 rounded-lg bg-slate-50">
              <img src="{{ Storage::url($group->image_path) }}" class="w-12 h-12 rounded-lg object-cover border border-slate-300 shadow-sm">
              <span class="text-xs font-semibold text-slate-500">Esta é a imagem atual. Se enviar uma nova abaixo, ela será substituída.</span>
            </div>
          @endif
          <input 
            id="image_input"
            type="file" 
            name="image" 
            accept="image/*"
            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2.5 text-sm text-slate-600 outline-none file:bg-slate-200 file:border-none file:text-slate-700 file:rounded file:px-3 file:py-1 file:mr-3 file:font-semibold"
          />
        </div>

        <!-- Regras Personalizadas -->
        <div class="space-y-1.5">
          <label for="rules_input" class="block text-sm font-semibold text-slate-700">Regras Personalizadas (Opcional)</label>
          <textarea
            id="rules_input"
            name="rules"
            rows="2"
            placeholder="Adicione outras regras (uma por linha)"
            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-900 outline-none focus:border-green-500 transition-colors resize-y"
          >{{ old('rules', $group->rules) }}</textarea>
        </div>

        <!-- Submit -->
        <div class="flex gap-3 pt-4">
          <a href="{{ route('my-groups') }}"
             class="flex-1 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-center py-4 rounded-xl font-bold transition-colors">
            Cancelar
          </a>
          <button type="submit"
                  class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl font-bold transition-colors shadow-sm">
            Salvar Alterações
          </button>
        </div>

        @if($errors->any())
          <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-4 mt-4">
            @foreach($errors->all() as $e)
              <p class="text-sm font-medium">• {{ $e }}</p>
            @endforeach
          </div>
        @endif

      </form>
    </div>
  </div>
</div>

@endsection
