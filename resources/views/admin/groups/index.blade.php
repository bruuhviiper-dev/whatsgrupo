@extends('admin.layout')

@section('title', 'Moderação de Grupos')
@section('page-title', 'Moderação de Grupos')

@section('content')

{{-- Filtros --}}
<form action="{{ route('admin.groups.index') }}" method="GET"
      class="card p-4 md:p-5 mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[140px]">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
            <select name="status" class="w-full rounded-lg px-3 py-2 text-sm text-slate-700 bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 font-semibold">
                <option value="">Todos</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pendente</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
            </select>
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Categoria</label>
            <select name="category_id" class="w-full rounded-lg px-3 py-2 text-sm text-slate-700 bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 font-semibold">
                <option value="">Todas</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-[2] min-w-[200px]">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Busca</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome do grupo..."
                       class="w-full pl-9 pr-4 py-2 text-sm text-slate-900 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 font-medium">
            </div>
        </div>
        <div class="flex-1 min-w-[130px]">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Apostas</label>
            <select name="gambling" class="w-full rounded-lg px-3 py-2 text-sm text-slate-700 bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 font-semibold">
                <option value="">Todos</option>
                <option value="1" {{ request('gambling') === '1' ? 'selected' : '' }}>Somente apostas</option>
                <option value="0" {{ request('gambling') === '0' ? 'selected' : '' }}>Sem apostas</option>
            </select>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <button type="submit" class="btn btn-green shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zm3 4a1 1 0 011-1h10a1 1 0 010 2H7a1 1 0 01-1-1zm4 4a1 1 0 011-1h2a1 1 0 010 2h-2a1 1 0 01-1-1z"/></svg>
                Filtrar
            </button>
            <a href="{{ route('admin.groups.index') }}" class="btn btn-slate">Limpar</a>
        </div>
    </div>
</form>

{{-- Header da tabela --}}
<div class="card overflow-hidden">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500">{{ $groups->total() }} grupo(s)</span>
            @if(request('status') || request('search') || request('category_id') || request('gambling'))
                <span class="badge" style="background:#eff6ff;color:#2563eb;border:1px solid #dbeafe;font-size:10px;">Filtrado</span>
            @endif
        </div>
        <a href="{{ route('admin.groups.pending') }}"
           class="btn btn-orange text-[11px]">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ver Pendentes
        </a>
    </div>

    @if ($groups->isEmpty())
        <div class="py-16 text-center">
            <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-slate-500 text-sm font-medium">Nenhum grupo encontrado com estes filtros.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Grupo</th>
                        <th class="text-left">Categoria</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">VIP</th>
                        <th class="text-left">Apostas</th>
                        <th class="text-left">Data</th>
                        <th class="text-left" style="min-width:200px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groups as $group)
                    <tr x-data="{ showRejectForm: false }"
                        class="{{ $group->is_gambling ? 'border-l-2' : '' }}"
                        style="{{ $group->is_gambling ? 'border-left-color:#f97316;' : '' }}">

                        {{-- Grupo --}}
                        <td>
                            <div class="flex items-center gap-3">
                                @if ($group->image_path)
                                    <img src="{{ asset('storage/' . $group->image_path) }}"
                                         alt="{{ $group->name }}"
                                         class="w-9 h-9 rounded-lg object-cover flex-shrink-0 border border-slate-100">
                                @else
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-black text-xs flex-shrink-0 text-white relative overflow-hidden"
                                         style="background:linear-gradient(135deg,#25D366,#128C7E);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white opacity-20 absolute" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                                        <span class="z-10">{{ mb_strtoupper(mb_substr($group->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 text-[13px] truncate max-w-[180px]">{{ $group->name }}</p>
                                    @if ($group->submitter_email)
                                        <p class="text-slate-400 text-[11px] font-medium truncate max-w-[180px]">{{ $group->submitter_email }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Categoria --}}
                        <td>
                            <div class="flex items-center gap-1.5 text-[12px] font-semibold text-slate-500">
                                @if($group->category->icon && str_starts_with($group->category->icon, 'heroicon'))
                                    <x-dynamic-component :component="$group->category->icon" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                                @endif
                                <span>{{ $group->category->name ?? '—' }}</span>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="badge badge-{{ $group->status }}">
                                {{ ['pending'=>'Pendente','approved'=>'Aprovado','rejected'=>'Rejeitado'][$group->status] ?? $group->status }}
                            </span>
                        </td>

                        {{-- VIP --}}
                        <td>
                            @if ($group->is_currently_vip)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-1">
                                    <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    até {{ $group->vip_expires_at->format('d/m H:i') }}
                                </span>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Apostas --}}
                        <td>
                            @if ($group->is_gambling)
                                <div>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-orange-700 bg-orange-50 border border-orange-200 rounded-full px-2.5 py-1">
                                        🎲 Aposta
                                    </span>
                                    <p class="text-orange-400 text-[10px] font-medium mt-0.5 pl-1">sem boost</p>
                                </div>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Data --}}
                        <td>
                            <p class="text-[12px] text-slate-600 font-semibold">{{ $group->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-slate-400 font-medium">{{ $group->created_at->format('H:i') }}</p>
                        </td>

                        {{-- Ações --}}
                        <td>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if ($group->status === 'approved')
                                    <a href="{{ route('group.show', $group->id) }}" target="_blank" class="btn btn-blue" style="padding:5px 10px;font-size:11px;">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Ver
                                    </a>
                                @endif

                                @if ($group->status !== 'approved')
                                    <form action="{{ route('admin.groups.approve', $group) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-green" style="padding:5px 10px;font-size:11px;">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Aprovar
                                        </button>
                                    </form>
                                @endif

                                @if ($group->status !== 'rejected')
                                    <button type="button" @click="showRejectForm = !showRejectForm"
                                            class="btn btn-red" style="padding:5px 10px;font-size:11px;">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Rejeitar
                                    </button>
                                @endif

                                @if ($group->is_gambling)
                                    <form action="{{ route('admin.groups.gambling.toggle', $group) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Remover a tag de apostas do grupo \'{{ addslashes($group->name) }}\'?')">
                                        @csrf
                                        <button type="submit" class="btn btn-orange" style="padding:5px 10px;font-size:11px;">
                                            🎲 Remover tag
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.groups.gambling.toggle', $group) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Marcar \'{{ addslashes($group->name) }}\' como grupo de APOSTAS?')">
                                        @csrf
                                        <button type="submit" class="btn btn-slate" style="padding:5px 10px;font-size:11px;" title="Marcar como apostas">
                                            🎲 É aposta?
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Excluir permanentemente o grupo {{ addslashes($group->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-slate" style="padding:5px 8px;font-size:11px;" title="Excluir">
                                        <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>

                            {{-- Formulário de rejeição inline --}}
                            <div x-show="showRejectForm"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="mt-3 p-3 rounded-xl border border-red-200 bg-red-50">
                                <form action="{{ route('admin.groups.reject', $group) }}" method="POST">
                                    @csrf
                                    <p class="text-red-700 text-[11px] font-bold mb-2">Motivo da rejeição:</p>
                                    <textarea name="reason" rows="2" required minlength="10"
                                              placeholder="Descreva o motivo..."
                                              class="w-full rounded-lg px-3 py-2 text-xs text-slate-900 border border-red-200 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none mb-2 bg-white"></textarea>
                                    <div class="flex gap-2">
                                        <button type="submit" class="flex-1 py-1.5 rounded-lg text-[11px] font-bold text-white bg-red-600 hover:bg-red-700 transition-colors">Confirmar</button>
                                        <button type="button" @click="showRejectForm = false"
                                                class="px-3 py-1.5 rounded-lg text-[11px] font-bold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 transition-colors">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if ($groups->hasPages())
            <div class="flex justify-center items-center gap-1.5 px-5 py-4 border-t border-slate-100 bg-slate-50/50 flex-wrap">
                @if (!$groups->onFirstPage())
                    <a href="{{ $groups->previousPageUrl() }}"
                       class="btn btn-slate" style="padding:6px 14px;font-size:12px;">← Anterior</a>
                @endif
                @for ($i = max(1, $groups->currentPage() - 2); $i <= min($groups->lastPage(), $groups->currentPage() + 2); $i++)
                    <a href="{{ $groups->url($i) }}"
                       class="btn {{ $i === $groups->currentPage() ? 'btn-green' : 'btn-slate' }}"
                       style="padding:6px 12px;font-size:12px;min-width:36px;justify-content:center;">{{ $i }}</a>
                @endfor
                @if ($groups->hasMorePages())
                    <a href="{{ $groups->nextPageUrl() }}"
                       class="btn btn-slate" style="padding:6px 14px;font-size:12px;">Próxima →</a>
                @endif
            </div>
        @endif
    @endif
</div>

@endsection
