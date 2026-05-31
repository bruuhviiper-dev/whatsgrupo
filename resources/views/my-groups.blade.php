@extends('layouts.app')

@section('title', 'Meus Grupos — WhatsGrupos')
@section('description', 'Gerencie e impulsione seus grupos de WhatsApp cadastrados no WhatsGrupos.')

@section('content')

<div class="max-w-2xl mx-auto py-8" x-data="{
    boostModal: false,
    selectedGroup: null,
    boostCode: '',
    boostLoading: false,
    boostResult: null,
    showEmailForm: {{ $groups->isEmpty() ? 'true' : 'false' }},

    openBoostModal(groupId, groupName, groupEmail) {
        this.selectedGroup = { id: groupId, name: groupName, email: groupEmail };
        this.boostCode = '';
        this.boostResult = null;
        this.boostModal = true;
    },

    closeBoostModal() {
        this.boostModal = false;
        this.selectedGroup = null;
        this.boostResult = null;
    },

    applyBoost() {
        if (!this.boostCode || this.boostCode.length !== 12) {
            this.boostResult = { success: false, message: 'O código deve ter exatamente 12 caracteres.' };
            return;
        }
        this.boostLoading = true;
        this.boostResult = null;
        fetch('{{ route('group.apply-boost') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
            },
            body: JSON.stringify({
                group_id: this.selectedGroup.id,
                boost_code: this.boostCode.toUpperCase(),
                email: this.selectedGroup.email
            })
        })
        .then(r => r.json())
        .then(data => {
            this.boostResult = data;
            this.boostLoading = false;
            if (data.success) {
                setTimeout(() => location.reload(), 2500);
            }
        })
        .catch(() => {
            this.boostLoading = false;
            this.boostResult = { success: false, message: 'Erro de conexão. Tente novamente.' };
        });
    }
}">

<meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-green-50 border border-green-100 shadow-sm">
            <x-heroicon-o-queue-list class="w-8 h-8 text-green-600" />
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">Meus Grupos</h1>
        <p class="text-slate-500 text-sm">Gerencie e impulsione de forma direta os seus grupos cadastrados neste dispositivo</p>
    </div>

    <x-adsense class="mb-8" />

    {{-- Lista de grupos encontrados (do Cookie ou E-mail) --}}
    @if ($groups->isNotEmpty())
        <div class="rounded-2xl border border-slate-200 p-6 mb-6 bg-white shadow-sm">
            @if ($email)
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <p class="text-slate-500 text-xs inline-flex items-center gap-2">
                        <x-heroicon-o-envelope class="w-4 h-4" /> Grupos vinculados ao e-mail: <span class="text-slate-900 font-semibold">{{ $email }}</span>
                    </p>
                    <a href="{{ route('my-groups') }}" class="text-xs text-[#25D366] font-semibold hover:underline transition-all">
                        ← Ver grupos locais
                    </a>
                </div>
            @else
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <p class="text-slate-500 text-xs inline-flex items-center gap-2">
                        <x-heroicon-o-device-phone-mobile class="w-4 h-4" /> Grupos detectados neste navegador
                    </p>
                    <button @click="showEmailForm = !showEmailForm" class="text-xs text-[#25D366] font-semibold hover:underline transition-all inline-flex items-center gap-1">
                        <x-heroicon-o-arrow-path class="w-3 h-3" /> Resgatar grupos antigos
                    </button>
                </div>
            @endif

            <div class="space-y-4">
                @foreach ($groups as $group)
                    <div class="flex flex-col sm:flex-row gap-4 p-4 rounded-xl border border-slate-100 hover:border-slate-300 transition-all bg-slate-50">

                        {{-- Imagem miniatura --}}
                        @if ($group->image_path)
                            <img src="{{ Storage::url($group->image_path) }}"
                                 alt="{{ $group->name }}"
                                 class="w-14 h-14 rounded-xl object-cover flex-shrink-0 border border-slate-200 shadow-sm">
                        @else
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center font-black text-lg flex-shrink-0 text-slate-400 bg-slate-200 border border-slate-300 uppercase">
                                {{ Str::substr($group->name, 0, 1) }}
                            </div>
                        @endif

                        {{-- Info do grupo --}}
                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <div class="flex flex-wrap gap-2 items-center mb-1">
                                <p class="text-slate-900 font-bold text-sm truncate">{{ $group->name }}</p>
                                
                                {{-- Badge de status --}}
                                @php
                                    $statusConfig = [
                                        'pending'  => ['label' => 'Pendente', 'class' => 'bg-amber-50 text-amber-600 border-amber-200'],
                                        'approved' => ['label' => 'Aprovado', 'class' => 'bg-green-50 text-green-600 border-green-200'],
                                        'rejected' => ['label' => 'Rejeitado', 'class' => 'bg-red-50 text-red-600 border-red-200'],
                                    ];
                                    $sc = $statusConfig[$group->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $sc['class'] }}">
                                    {{ $sc['label'] }}
                                </span>
                                
                                @if ($group->is_currently_vip)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200 inline-flex items-center gap-1">
                                        <x-heroicon-s-star class="w-3 h-3" /> VIP ativo
                                    </span>
                                @endif
                            </div>
                            <p class="text-slate-500 text-xs">{{ $group->category->name ?? '' }} · Enviado em {{ $group->created_at->format('d/m/Y') }}</p>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if ($group->status === 'pending')
                                <a href="{{ route('my-groups.edit', $group) }}"
                                   class="text-xs text-slate-500 font-semibold hover:text-blue-600 transition-colors px-3 py-2 rounded-lg bg-slate-200 hover:bg-blue-100 text-center">
                                    Editar
                                </a>
                            @endif

                            <form action="{{ route('my-groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este grupo?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-slate-500 font-semibold hover:text-red-600 transition-colors px-3 py-2 rounded-lg bg-slate-200 hover:bg-red-100 text-center">
                                    Excluir
                                </button>
                            </form>

                            @if ($group->status === 'approved')
                                <a href="{{ route('group.show', $group->id) }}"
                                   class="text-xs text-slate-500 font-semibold hover:text-slate-900 transition-colors px-3 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-center">
                                    Ver →
                                </a>
                                @if ($group->can_boost)
                                    <button type="button"
                                            @click="openBoostModal({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ $group->submitter_email ?? '' }}')"
                                            class="btn-vip text-xs px-4 py-2 bg-amber-400 hover:bg-amber-500 text-slate-900 font-black rounded-lg transition-all inline-flex items-center gap-1 shadow-sm">
                                        <x-heroicon-s-rocket-launch class="w-3 h-3" /> SUPER VIP
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Lista de Pedidos VIP / Códigos do Usuário --}}
    @if (isset($orders) && $orders->isNotEmpty())
        <div class="rounded-2xl border border-amber-200 p-6 mb-6 bg-gradient-to-b from-amber-50/50 to-white shadow-sm relative overflow-hidden" x-data="{ copiedCode: null }">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-100 rounded-full opacity-50"></div>
            
            <div class="flex items-center gap-2 mb-6 border-b border-amber-100 pb-4 relative z-10">
                <x-heroicon-s-star class="w-6 h-6 text-amber-500" />
                <h2 class="text-lg font-black text-slate-900">Meus Códigos VIP</h2>
            </div>

            <div class="space-y-4 relative z-10">
                @foreach ($orders as $order)
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-5 rounded-xl border border-amber-200 bg-white shadow-sm hover:border-amber-300 transition-all">
                        <div class="flex-1 w-full">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-bold text-slate-900">{{ $order->boostPackage->name ?? 'Pacote VIP' }}</p>
                                @if ($order->remaining_boosts > 0)
                                    <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">Ativo</span>
                                @else
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold">Esgotado</span>
                                @endif
                            </div>
                            <p class="text-slate-500 text-xs">Comprado em {{ $order->created_at->format('d/m/Y') }}</p>
                        </div>

                        <div class="flex flex-col md:items-end items-center w-full md:w-auto">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Seu Código de Ativação</p>
                            <div class="flex gap-2 w-full md:w-auto">
                                <input type="text" readonly value="{{ $order->boost_code }}"
                                       class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-sm text-amber-700 font-mono font-bold tracking-widest text-center outline-none w-full md:w-40" />
                                <button @click="navigator.clipboard.writeText('{{ $order->boost_code }}'); copiedCode = '{{ $order->boost_code }}'; setTimeout(() => copiedCode = null, 2000)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-lg transition-colors font-bold text-xs whitespace-nowrap shadow-sm">
                                    <span x-show="copiedCode !== '{{ $order->boost_code }}'">Copiar</span>
                                    <span x-show="copiedCode === '{{ $order->boost_code }}'">Copiado!</span>
                                </button>
                            </div>
                            <p class="text-xs font-semibold mt-2" :class="{'text-amber-600': {{ $order->remaining_boosts }} > 0, 'text-red-500': {{ $order->remaining_boosts }} <= 0}">
                                Restam {{ $order->remaining_boosts }} impulsos ({{ $order->boosts_used }} de {{ $order->boosts_total }} usados)
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <p class="text-xs text-slate-500 mt-4 text-center">Para usar seus impulsos, clique no botão "SUPER VIP" nos grupos aprovados acima e cole o código.</p>
        </div>
    @endif

    {{-- Estado 1: Formulário de e-mail --}}
    <div x-show="showEmailForm" style="display: none;" class="mt-4">
        <form action="{{ route('my-groups.search') }}" method="POST"
              class="rounded-2xl border border-slate-200 p-6 sm:p-8 bg-white shadow-sm">
            @csrf
            
            <div class="flex justify-between items-center mb-3">
                <label for="email-input" class="block text-slate-900 font-bold text-sm inline-flex items-center gap-2">
                    <x-heroicon-o-envelope-open class="w-5 h-5 text-slate-400" /> Resgatar grupos por e-mail
                </label>
                @if ($groups->isNotEmpty())
                    <button type="button" @click="showEmailForm = false" class="text-xs text-slate-400 hover:text-slate-900 font-bold"><x-heroicon-o-x-mark class="w-4 h-4" /></button>
                @endif
            </div>
            
            <p class="text-xs text-slate-500 mb-4">Caso tenha cadastrado grupos usando um e-mail em outro dispositivo, digite-o abaixo para importá-los para cá.</p>
            
            <div class="flex gap-3 flex-wrap sm:flex-nowrap">
                <input type="email"
                       id="email-input"
                       name="email"
                       placeholder="seu@email.com"
                       required
                       class="flex-1 rounded-xl px-4 py-3 text-sm text-slate-900 border border-slate-200 outline-none focus:border-green-500 transition-all bg-slate-50">
                <button type="submit" class="w-full sm:w-auto bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold text-sm px-6 py-3 rounded-xl transition-all cursor-pointer border-none shadow-sm inline-flex items-center justify-center gap-2">
                    Importar <x-heroicon-m-arrow-right class="w-4 h-4" />
                </button>
            </div>
            @error('email')
                <p class="text-red-500 text-xs font-semibold mt-2">{{ $message }}</p>
            @enderror
        </form>
    </div>

    {{-- Sem grupos locais nem busca realizada --}}
    @if ($groups->isEmpty())
        <div class="rounded-2xl border border-slate-200 p-10 text-center bg-white shadow-sm mt-6">
            <div class="flex justify-center mb-4">
                <div class="p-4 bg-green-50 rounded-full">
                    <x-heroicon-o-megaphone class="w-10 h-10 text-green-500" />
                </div>
            </div>
            <h2 class="text-slate-900 font-black text-xl mb-2">Divulgue seus grupos!</h2>
            <p class="text-slate-500 text-sm mb-6 max-w-md mx-auto">Cadastre seus grupos de WhatsApp em nossa plataforma e atraia centenas de novos membros ativos todos os dias.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ route('send-group.create') }}" class="btn-primary flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white font-black text-sm px-6 py-3.5 rounded-xl transition-all shadow-sm">
                    <x-heroicon-s-plus-circle class="w-5 h-5" /> Enviar Grupo Grátis
                </a>
                <button @click="showEmailForm = true" class="px-6 py-3.5 rounded-xl text-sm font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-50 border border-slate-200 transition-all inline-flex justify-center items-center gap-2">
                    <x-heroicon-o-envelope class="w-4 h-4" /> Já tenho grupos
                </button>
            </div>
        </div>
    @endif

    {{-- Modal de aplicação de boost --}}
    <div x-show="boostModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px);"
         @click.self="closeBoostModal()">

        <div class="w-full max-w-md rounded-2xl border border-slate-200 p-6 bg-white shadow-xl"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-slate-900 font-black text-lg flex items-center gap-2">
                    <x-heroicon-s-star class="w-5 h-5 text-amber-500" /> Aplicar Super VIP
                </h3>
                <button @click="closeBoostModal()" class="text-slate-400 hover:text-slate-600 transition-colors"><x-heroicon-m-x-mark class="w-6 h-6" /></button>
            </div>

            <p class="text-slate-500 text-xs mb-1">Grupo selecionado:</p>
            <p class="text-slate-900 font-bold text-sm mb-4" x-text="selectedGroup?.name"></p>

            <div class="mb-4">
                <label class="block text-slate-700 font-bold text-sm mb-2">Código de Impulso (12 dígitos)</label>
                <input type="text"
                       x-model="boostCode"
                       @input="boostCode = boostCode.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 12)"
                       id="boost-code-input"
                       placeholder="Ex: ABC123DEF456"
                       maxlength="12"
                       class="w-full rounded-xl px-4 py-3.5 text-center text-lg font-mono font-bold tracking-widest text-slate-900 border border-slate-300 outline-none focus:border-amber-500 transition-all bg-slate-50"
                       style="letter-spacing: 4px;">
                <p class="text-slate-400 text-[10px] mt-2 text-center font-bold uppercase" x-text="boostCode.length + '/12 caracteres'"></p>
            </div>

            {{-- Feedback do boost --}}
            <div x-show="boostResult" class="mb-4 rounded-xl px-4 py-3 text-xs font-bold"
                 :class="boostResult?.success ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'">
                <span x-text="boostResult?.message"></span>
            </div>

            <div class="flex gap-3">
                <button type="button"
                        @click="closeBoostModal()"
                        class="flex-1 px-4 py-3.5 rounded-xl text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 transition-all">
                    Cancelar
                </button>
                <button type="button"
                        id="btn-confirmar-boost"
                        @click="applyBoost()"
                        :disabled="boostCode.length !== 12 || boostLoading"
                        class="flex-1 text-sm py-3.5 flex items-center justify-center gap-2 font-black rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 cursor-pointer border-none shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!boostLoading" class="inline-flex items-center gap-1"><x-heroicon-m-check class="w-5 h-5" /> Confirmar</span>
                    <span x-show="boostLoading" class="inline-flex items-center gap-1"><x-heroicon-m-arrow-path class="w-5 h-5 animate-spin" /> Aplicando...</span>
                </button>
            </div>

            <p class="text-slate-500 text-xs text-center mt-4 font-semibold">
                Não tem um código? <a href="{{ route('boost.packages') }}" class="text-amber-600 hover:underline">Comprar impulsos →</a>
            </p>
        </div>
    </div>

</div>

@endsection
