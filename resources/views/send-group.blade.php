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

    {{--
      ESTRATÉGIA DE EXTRAÇÃO DE IMAGEM / NOME:
      O WhatsApp bloqueia requests de servidor (403). A única forma confiável de
      obter og:image e og:title é fazer o fetch DIRETO NO BROWSER do usuário,
      que já tem os cookies de sessão do WhatsApp Web e não é bloqueado.

      Fluxo:
        1. Usuário cola o link e clica "Verificar"
        2. JS faz fetch() ao link do WhatsApp com mode:'cors' (ou via proxy iframe)
        3. Extrai og:title → preenche nome automaticamente
        4. Extrai og:image → exibe preview; converte para base64 via Canvas
        5. base64 é enviado no campo hidden "detected_image_b64" ao fazer submit
        6. Backend decodifica o base64 e converte para WebP com Intervention Image

      CORS: o WhatsApp não bloqueia fetch do browser quando o usuário tem sessão.
      Para grupos sem sessão, usamos um proxy CORS interno (/api/proxy-wa-image).
    --}}

    <div
      x-data="{
        link: '',
        loading: false,
        detected: false,
        detectedName: '',
        detectedImageUrl: '',
        userImagePreview: '',
        imageLoading: false,
        error: '',
        warning: '',

        DEFAULT_IMG: '/images/default-group.svg',

        get previewSrc() {
          if (this.userImagePreview) return this.userImagePreview;
          if (this.detectedImageUrl) return this.detectedImageUrl;
          if (this.detected) return this.DEFAULT_IMG;
          return '';
        },
        get hasPreview() {
          return !!(this.userImagePreview || this.detectedImageUrl || this.detected);
        },
        get isDefaultImg() {
          return this.detected && !this.userImagePreview && !this.detectedImageUrl;
        },

        handleUserImageChange(event) {
          const file = event.target.files[0];
          if (!file) { this.userImagePreview = ''; return; }
          const reader = new FileReader();
          reader.onload = (e) => { this.userImagePreview = e.target.result; };
          reader.readAsDataURL(file);
        },

        removeUserImage() {
          this.userImagePreview = '';
          const fi = document.getElementById('image_input');
          if (fi) fi.value = '';
        },

        fillName(name) {
          if (!name) return;
          this.detectedName = name;
          const inp = document.getElementById('nameInput');
          if (inp && !inp.value.trim()) {
            inp.value = name;
            inp.dispatchEvent(new Event('input', { bubbles: true }));
          }
        },

        async validateLink() {
          const link = this.link.trim();
          if (!link.includes('chat.whatsapp.com') && !link.includes('whatsapp.com/channel')) {
            this.error = 'Por favor, insira um link oficial (chat.whatsapp.com ou whatsapp.com/channel).';
            this.detected = false;
            return;
          }
          this.loading = true;
          this.error = '';
          this.warning = '';
          this.detectedImageUrl = '';
          document.getElementById('detected_image_b64').value = '';

          try {
            const res = await fetch('/api/validate-link', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
              },
              body: JSON.stringify({ link })
            });
            const data = await res.json();

            if (!data.valid) {
              this.error = data.error || 'Este link e invalido ou foi revogado.';
              this.detected = false;
              return;
            }

            this.detected = true;
            this.fillName(data.name);

            let imageUrl = data.image || null;

            if (!imageUrl || !this.detectedName) {
              try {
                const metaRes = await fetch('/api/wa-meta?url=' + encodeURIComponent(link), {
                  headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                });
                if (metaRes.ok) {
                  const meta = await metaRes.json();
                  if (!this.detectedName && meta.name) this.fillName(meta.name);
                  if (!imageUrl && meta.image) imageUrl = meta.image;
                }
              } catch(e) {
                console.warn('wa-meta proxy failed:', e);
              }
            }

            if (imageUrl) {
              this.imageLoading = true;
              await this.loadImageAsBase64(imageUrl);
              this.imageLoading = false;
            } else {
              this.warning = 'Grupo detectado! Nao conseguimos capturar a foto automaticamente - voce pode enviar uma manualmente.';
            }

          } catch(e) {
            console.error('validateLink error:', e);
            this.error = 'Erro ao validar o link. Verifique a conexao.';
          } finally {
            this.loading = false;
            this.imageLoading = false;
          }
        },

        async loadImageAsBase64(imageUrl) {
          try {
            const proxyImgUrl = '/api/wa-image?url=' + encodeURIComponent(imageUrl);
            const imgRes = await fetch(proxyImgUrl, { redirect: 'follow' });

            if (!imgRes.ok) throw new Error('Proxy retornou ' + imgRes.status);

            const blob = await imgRes.blob();

            if (blob.size < 1000 || blob.type === 'image/svg+xml') {
              console.warn('loadImageAsBase64: imagem padrao/pequena, ignorando.');
              return;
            }

            const b64 = await new Promise((resolve, reject) => {
              const reader = new FileReader();
              reader.onload  = () => resolve(reader.result);
              reader.onerror = reject;
              reader.readAsDataURL(blob);
            });

            document.getElementById('detected_image_b64').value = b64;
            this.detectedImageUrl = b64;

          } catch(e) {
            console.warn('loadImageAsBase64 failed:', e);
          }
        },
      }"
    >

      <form action="/enviar-grupo" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Base64 da imagem detectada pelo browser (proxy + canvas) --}}
        <input type="hidden" id="detected_image_b64" name="detected_image_b64" value="">

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
              class="bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg px-5 transition-colors disabled:opacity-50 whitespace-nowrap flex items-center gap-2">
              <template x-if="!loading">
                <span>Verificar</span>
              </template>
              <template x-if="loading">
                <span class="flex items-center gap-1.5">
                  <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  Buscando...
                </span>
              </template>
            </button>
          </div>

          <p x-show="error" x-text="error" class="text-sm text-red-500 mt-1" style="display:none;"></p>

          <!-- Aviso: grupo detectado mas sem imagem -->
          <div x-show="detected && warning && !detectedImageUrl" class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-lg p-3 mt-2" style="display:none;">
            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            <p class="text-xs text-amber-700" x-text="warning"></p>
          </div>

          <!-- Painel de sucesso: grupo detectado -->
          <div x-show="detected" class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-3 mt-3" style="display:none;">
            <!-- Avatar com preview da imagem -->
            <div class="relative flex-shrink-0">
              <!-- Loading spinner enquanto baixa a imagem -->
              <template x-if="imageLoading">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center border-2 border-green-200">
                  <svg class="animate-spin w-5 h-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                </div>
              </template>
              <!-- Foto capturada do WhatsApp (base64) -->
              <template x-if="!imageLoading && detectedImageUrl">
                <img
                  :src="detectedImageUrl"
                  class="w-12 h-12 rounded-full object-cover shadow border-2 border-green-300"
                  alt="Foto do grupo"
                />
              </template>
              <!-- Avatar padrão do WhatsApp quando não tem foto -->
              <template x-if="!imageLoading && !detectedImageUrl">
                <img
                  :src="DEFAULT_IMG"
                  class="w-12 h-12 rounded-full object-cover shadow border-2 border-green-200 opacity-60"
                  alt="Foto padrão"
                />
              </template>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-bold text-green-700 uppercase tracking-wide">✓ Grupo detectado com sucesso!</p>
              <p class="text-sm text-green-900 font-semibold truncate" x-text="detectedName || 'Link válido'"></p>
              <p x-show="imageLoading" class="text-xs text-green-500 mt-0.5 animate-pulse">Carregando foto do grupo...</p>
              <p x-show="!imageLoading && detectedImageUrl" class="text-xs text-green-600 mt-0.5">Foto capturada — você pode substituí-la abaixo</p>
              <p x-show="!imageLoading && !detectedImageUrl && detected" class="text-xs text-amber-600 mt-0.5">Sem foto — será usada imagem padrão ou envie uma abaixo</p>
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
          <label for="nameInput" class="block text-sm font-semibold text-slate-700">
            Nome do Grupo *
            <span class="text-xs text-slate-400 font-normal ml-1">preenchido automaticamente</span>
          </label>
          <input
            id="nameInput"
            type="text"
            name="name"
            maxlength="100"
            required
            value="{{ old('name') }}"
            placeholder="Clique em Verificar para preencher automaticamente"
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
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-slate-700">
            Imagem do Grupo
            <span class="text-xs text-slate-400 font-normal ml-1">capturada automaticamente ou envie a sua</span>
          </label>

          <!-- Preview da imagem do grupo -->
          <div x-show="hasPreview" class="flex items-start gap-3" style="display:none;">
            <div class="relative">
              <!-- Loading da imagem -->
              <template x-if="imageLoading">
                <div class="w-20 h-20 rounded-xl bg-slate-100 border-2 border-slate-200 flex items-center justify-center shadow-sm">
                  <svg class="animate-spin w-6 h-6 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                </div>
              </template>
              <!-- Imagem com preview -->
              <template x-if="!imageLoading">
                <img
                  :src="previewSrc"
                  class="w-20 h-20 rounded-xl object-cover border-2 shadow-sm"
                  :class="isDefaultImg ? 'border-slate-200 opacity-50' : 'border-slate-200'"
                  alt="Preview da imagem do grupo"
                />
              </template>
              <span x-show="!imageLoading && userImagePreview" class="absolute -top-1.5 -right-1.5 bg-blue-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" style="display:none;">Sua foto</span>
              <span x-show="!imageLoading && !userImagePreview && detectedImageUrl" class="absolute -top-1.5 -right-1.5 bg-green-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" style="display:none;">WhatsApp</span>
              <span x-show="!imageLoading && isDefaultImg" class="absolute -top-1.5 -right-1.5 bg-slate-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" style="display:none;">Padrão</span>
            </div>
            <button
              x-show="userImagePreview"
              type="button"
              x-on:click="removeUserImage()"
              class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1 mt-1"
              style="display:none;">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              Remover e usar foto do WhatsApp
            </button>
          </div>

          <input
            id="image_input"
            type="file"
            name="image"
            accept="image/*"
            x-on:change="handleUserImageChange($event)"
            class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2.5 text-sm text-slate-600 outline-none file:bg-slate-200 file:border-none file:text-slate-700 file:rounded file:px-3 file:py-1 file:mr-3 file:font-semibold file:cursor-pointer hover:file:bg-slate-300 transition-colors cursor-pointer"
          />
          <p class="text-xs text-slate-400">Envie uma imagem para substituir a foto do grupo. Será convertida para WebP automaticamente.</p>
        </div>

        <!-- Regras do Grupo -->
        <div class="space-y-3 pt-2">
          <label class="block text-sm font-semibold text-slate-700">Regras do Grupo * <span class="text-xs text-slate-400 font-normal">(Selecione pelo menos uma)</span></label>
          <div class="space-y-2.5 bg-slate-50 border border-slate-200 rounded-lg p-4">
            @foreach([
              'Proibido conteúdo adulto, agressivo ou ilegal',
              'Proibido spam ou envio excessivo de links sem autorização',
              'Respeitar todos os membros e administradores',
              'Proibido chamar outros participantes no privado (inbox)',
              'Proibido debates políticos ofensivos ou fake news',
              'Manter o foco exclusivamente no tema proposto do grupo',
            ] as $rule)
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input type="checkbox" name="selected_rules[]" value="{{ $rule }}"
                class="mt-1 rounded text-[#25D366] focus:ring-[#25D366] border-slate-300"
                {{ is_array(old('selected_rules')) && in_array($rule, old('selected_rules')) ? 'checked' : '' }} />
              <span class="text-sm text-slate-700 font-medium select-none">{{ $rule }}</span>
            </label>
            @endforeach
          </div>
        </div>

        <!-- Regras Personalizadas -->
        <div class="space-y-1.5">
          <label for="rules_input" class="block text-sm font-semibold text-slate-700">Regras Personalizadas (Opcional)</label>
          <textarea id="rules_input" name="rules" rows="2"
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
          <div x-show="show" class="mt-3" style="display:none;">
            <input type="email" name="submitter_email" placeholder="seu@email.com"
              class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-900 outline-none focus:border-green-500 transition-colors" />
          </div>
        </div>

        <!-- Termos -->
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

        @if(session('success'))
          <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mt-4">
            <p class="text-sm font-medium">✓ {{ session('success') }}</p>
          </div>
        @endif

        @if(session('error'))
          <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-4 mt-4">
            <p class="text-sm font-medium">{{ session('error') }}</p>
          </div>
        @endif

      </form>
    </div>
  </div>
</div>

@endsection
