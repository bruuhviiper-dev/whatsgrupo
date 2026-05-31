@php
    // Carrega as categorias dinamicamente para manter o componente 100% autônomo
    $categories = \App\Models\Category::ordered()->get();
    $vapidPublicKey = config('webpush.vapid_public');
@endphp

<div x-data="pushOptinData()" 
     x-init="initOptin()"
     class="relative">
     
    {{-- Banner de Opt-In --}}
    <div x-show="showBanner" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         class="fixed bottom-6 right-6 left-6 sm:left-auto sm:w-96 bg-card border border-primary/20 rounded-2xl p-5 shadow-2xl z-50 overflow-hidden"
         style="display: none; background: #1A1A2E;">
        <div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -z-10"></div>
        
        <div class="flex gap-3 items-start">
            <span class="text-3xl">🔔</span>
            <div class="flex-1">
                <h4 class="text-sm font-black text-text-main leading-tight mb-1">Novos grupos no seu radar!</h4>
                <p class="text-text-muted text-xs leading-relaxed mb-4">
                    Gostaria de receber notificações na tela sempre que novos grupos das suas categorias favoritas forem aprovados?
                </p>
                <div class="flex gap-2 justify-end">
                    <button @click="rejectOptin()" 
                            class="px-3 py-1.5 rounded-lg border border-white/5 text-text-muted text-xs font-bold hover:text-text-main transition-colors">
                        Agora não
                    </button>
                    <button @click="acceptOptin()" 
                            class="px-4 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-bold transition-all transform hover:scale-105">
                        Sim, quero!
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Categorias (Preferências) --}}
    <div x-show="showModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;"
         x-transition>
         
        <div class="bg-card border border-white/10 rounded-2xl w-full max-w-lg p-6 max-h-[85vh] flex flex-col relative overflow-hidden"
             style="background: #1A1A2E;"
             @click.away="showModal = false">
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-primary to-secondary"></div>
            
            <h3 class="text-lg font-black text-text-main flex items-center gap-2 mb-2">
                <span>🎯</span> Categorias de Interesse
            </h3>
            <p class="text-text-muted text-xs mb-4">
                Selecione as categorias que você deseja assinar. Enviaremos notificações somente para novos grupos destas categorias.
            </p>

            <div class="flex-1 overflow-y-auto pr-2 space-y-2 py-2 max-h-[50vh] grid grid-cols-2 gap-2">
                @foreach ($categories as $cat)
                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-white/5 hover:border-primary/20 hover:bg-white/5 cursor-pointer transition-all select-none">
                        <input type="checkbox" 
                               value="{{ $cat->id }}" 
                               x-model="selectedCategories"
                               class="rounded border-white/10 text-primary focus:ring-primary focus:ring-offset-0 bg-background w-4 h-4">
                        <span class="text-xs text-text-muted font-medium truncate flex items-center gap-1.5">
                            <span>{{ $cat->icon }}</span>
                            <span class="truncate">{{ $cat->name }}</span>
                        </span>
                    </label>
                @endforeach
            </div>

            <div class="flex gap-3 items-center justify-end pt-5 border-t border-white/5 mt-4 flex-shrink-0">
                <button @click="showModal = false" 
                        class="px-4 py-2 rounded-xl border border-white/5 text-text-muted text-xs font-bold hover:text-text-main transition-colors">
                    Fechar
                </button>
                <button @click="savePreferences()" 
                        class="btn-primary py-2 px-5 text-xs font-bold">
                    Salvar Preferências
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function pushOptinData() {
    return {
        showBanner: false,
        showModal: false,
        selectedCategories: [],
        endpoint: '',
        publicKey: '',
        authToken: '',
        vapidPublicKey: '{{ $vapidPublicKey }}',

        initOptin() {
            // Se o navegador não suportar service workers ou notificações, encerra silenciosamente
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                return;
            }

            // Verifica se o usuário já recusou ou aceitou nos últimos 7 dias
            const pushState = localStorage.getItem('push_optin_state');
            const expires = localStorage.getItem('push_optin_expires');
            
            if (pushState === 'rejected' && expires && new Date().getTime() < expires) {
                return;
            }

            // Exibe o banner após 30 segundos na Home
            setTimeout(() => {
                // Só exibe se a permissão padrão ainda não estiver decidida (default)
                if (Notification.permission === 'default') {
                    this.showBanner = true;
                }
            }, 30000); // 30 segundos
        },

        rejectOptin() {
            this.showBanner = false;
            // Define o prazo de rejeição para 7 dias no localStorage
            const nextCheck = new Date().getTime() + (7 * 24 * 60 * 60 * 1000);
            localStorage.setItem('push_optin_state', 'rejected');
            localStorage.setItem('push_optin_expires', nextCheck);
        },

        async acceptOptin() {
            this.showBanner = false;
            
            try {
                // Solicita a permissão no browser
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    return;
                }

                // Registra o service worker
                const registration = await navigator.serviceWorker.register('/sw.js');
                
                // Converte a chave VAPID pública
                const serverKey = this.urlBase64ToUint8Array(this.vapidPublicKey);

                // Assina o usuário com a chave pública VAPID
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: serverKey
                });

                // Criptografa as chaves de segurança
                const subJson = subscription.toJSON();
                this.endpoint = subJson.endpoint;
                this.publicKey = subJson.keys.p256dh;
                this.authToken = subJson.keys.auth;

                // Registra a assinatura na API do Laravel
                const response = await fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        endpoint: this.endpoint,
                        public_key: this.publicKey,
                        auth_token: this.authToken,
                        category_ids: []
                    })
                });

                const result = await response.json();
                if (result.success) {
                    localStorage.setItem('push_optin_state', 'accepted');
                    // Abre o modal de categorias para o usuário escolher suas preferências
                    this.showModal = true;
                }
            } catch (e) {
                console.error('[WebPush] Falha ao registrar inscrição de push:', e);
            }
        },

        async savePreferences() {
            try {
                const response = await fetch('/api/push/preferences', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        endpoint: this.endpoint,
                        category_ids: this.selectedCategories
                    })
                });

                const result = await response.json();
                if (result.success) {
                    this.showModal = false;
                    alert('Preferências de notificações salvas! Enviaremos novidades de acordo com seus interesses.');
                }
            } catch (e) {
                console.error('[WebPush] Erro ao salvar preferências:', e);
            }
        },

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    }
}
</script>
