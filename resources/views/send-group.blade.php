@extends('layouts.app')
@section('title', 'Enviar Grupo de WhatsApp — WhatsGrupos')
@section('description', 'Envie e divulgue seu grupo ou canal de WhatsApp gratuitamente no maior diretório do Brasil.')

@section('content')

<div class="max-w-xl mx-auto px-4 py-8">
  
  <div class="mb-8 text-center">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">
      Adicionar Grupo
    </h1>
    <p class="text-slate-600 text-sm">
      Compartilhe seu link de convite. É rápido e gratuito!
    </p>
  </div>

  <x-adsense class="mb-8" />

  <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-sm">
    
    <div x-data="{
      link: '',
      loading: false,
      detected: false,
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

      <form action="/enviar-grupo" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

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
              class="w-full bg-slate-50 border rounded-lg px-4 py-3 text-slate-900 outline-none transition-colors"
              :class="detected ? 'border-green-500 bg-green-50' : (error ? 'border-red-500' : 'border-slate-300 focus:border-green-500')"
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
          <div x-show="detected" class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-3 mt-3" style="display: none;">
            <template x-if="detectedImage">
              <img :src="detectedImage" class="w-10 h-10 rounded-full object-cover shadow-sm border border-green-100">
            </template>
            <template x-if="!detectedImage">
               <div class="w-10 h-10 rounded-full bg-green-200 flex items-center justify-center text-green-700 font-bold">
                 <x-heroicon-o-check class="w-6 h-6" />
               </div>
            </template>
            <div>
              <p class="text-xs font-bold text-green-700 uppercase tracking-wide flex items-center gap-1">
                <x-heroicon-s-check-circle class="w-4 h-4 text-green-600" /> Link Detectado com Sucesso!
              </p>
              <p class="text-sm text-green-900 font-medium" x-text="detectedName"></p>
            </div>
          </div>
        </div>

        <!-- Categoria -->
        <div class="space-y-1.5">
          <label for="category_input" class="block text-sm font-semibold text-slate-700">Categoria *</label>
          <select id="category_input" name="category_id" required
                  class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-900 outline-none focus:border-green-500 transition-colors">
            <option value="">Selecione uma categoria...</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
            value="{{ old('name') }}"
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
          >{{ old('description') }}</textarea>
        </div>

        <!-- Imagem -->
        <div class="space-y-1.5">
          <label for="image_input" class="block text-sm font-semibold text-slate-700">Imagem (Opcional)</label>
          <input 
            id="image_input"
            type="file" 
            name="image" 
            accept="image/*"
            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2.5 text-sm text-slate-600 outline-none file:bg-slate-200 file:border-none file:text-slate-700 file:rounded file:px-3 file:py-1 file:mr-3 file:font-semibold"
          />
        </div>

        <!-- Regras do Grupo (Pelo menos uma obrigatória) -->
        <div class="space-y-3 pt-2">
          <label class="block text-sm font-semibold text-slate-700">Regras do Grupo * <span class="text-xs text-slate-400 font-normal">(Selecione pelo menos uma)</span></label>
          <div class="space-y-2.5 bg-slate-50 border border-slate-200 rounded-lg p-4">
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input type="checkbox" name="selected_rules[]" value="Proibido conteúdo adulto, agressivo ou ilegal" class="mt-1 rounded text-[#25D366] focus:ring-[#25D366] border-slate-300" {{ is_array(old('selected_rules')) && in_array('Proibido conteúdo adulto, agressivo ou ilegal', old('selected_rules')) ? 'checked' : '' }} />
              <span class="text-sm text-slate-700 font-medium select-none">Proibido conteúdo adulto, agressivo ou ilegal</span>
            </label>
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input type="checkbox" name="selected_rules[]" value="Proibido spam ou envio excessivo de links sem autorização" class="mt-1 rounded text-[#25D366] focus:ring-[#25D366] border-slate-300" {{ is_array(old('selected_rules')) && in_array('Proibido spam ou envio excessivo de links sem autorização', old('selected_rules')) ? 'checked' : '' }} />
              <span class="text-sm text-slate-700 font-medium select-none">Proibido spam ou envio excessivo de links sem autorização</span>
            </label>
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input type="checkbox" name="selected_rules[]" value="Respeitar todos os membros e administradores" class="mt-1 rounded text-[#25D366] focus:ring-[#25D366] border-slate-300" {{ is_array(old('selected_rules')) && in_array('Respeitar todos os membros e administradores', old('selected_rules')) ? 'checked' : '' }} />
              <span class="text-sm text-slate-700 font-medium select-none">Respeitar todos os membros e administradores</span>
            </label>
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input type="checkbox" name="selected_rules[]" value="Proibido chamar outros participantes no privado (inbox)" class="mt-1 rounded text-[#25D366] focus:ring-[#25D366] border-slate-300" {{ is_array(old('selected_rules')) && in_array('Proibido chamar outros participantes no privado (inbox)', old('selected_rules')) ? 'checked' : '' }} />
              <span class="text-sm text-slate-700 font-medium select-none">Proibido chamar outros participantes no privado (inbox)</span>
            </label>
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input type="checkbox" name="selected_rules[]" value="Proibido debates políticos ofensivos ou fake news" class="mt-1 rounded text-[#25D366] focus:ring-[#25D366] border-slate-300" {{ is_array(old('selected_rules')) && in_array('Proibido debates políticos ofensivos ou fake news', old('selected_rules')) ? 'checked' : '' }} />
              <span class="text-sm text-slate-700 font-medium select-none">Proibido debates políticos ofensivos ou fake news</span>
            </label>
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input type="checkbox" name="selected_rules[]" value="Manter o foco exclusivamente no tema proposto do grupo" class="mt-1 rounded text-[#25D366] focus:ring-[#25D366] border-slate-300" {{ is_array(old('selected_rules')) && in_array('Manter o foco exclusivamente no tema proposto do grupo', old('selected_rules')) ? 'checked' : '' }} />
              <span class="text-sm text-slate-700 font-medium select-none">Manter o foco exclusivamente no tema proposto do grupo</span>
            </label>
          </div>
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
          >{{ old('rules') }}</textarea>
        </div>

        <!-- Alerta por Email -->
        <div class="pt-4 border-t border-slate-100" x-data="{ show: false }">
          <label class="flex items-center gap-2 cursor-pointer text-sm font-semibold text-slate-700">
            <input type="checkbox" name="notify_email" x-on:change="show = $el.checked"
                   class="rounded text-green-500 focus:ring-green-500 border-slate-300" />
            Receber alerta por e-mail quando for aprovado
          </label>
          <div x-show="show" class="mt-3" style="display: none;">
            <input
              type="email"
              name="submitter_email"
              placeholder="seu@email.com"
              class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-900 outline-none focus:border-green-500 transition-colors"
            />
          </div>
        </div>

        <!-- Termos de Uso -->
        <div class="pt-2">
          <label class="flex items-start gap-2 cursor-pointer text-sm font-semibold text-slate-700">
            <input type="checkbox" name="terms" required class="mt-1 rounded text-green-500 focus:ring-green-500 border-slate-300" />
            <span>Eu li e concordo com os <a href="/termos" target="_blank" class="text-blue-600 hover:underline">Termos de Uso</a> e afirmo que o grupo obedece às regras.</span>
          </label>
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white py-4 rounded-xl font-bold text-lg transition-colors mt-4">
          Enviar Grupo
        </button>

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
