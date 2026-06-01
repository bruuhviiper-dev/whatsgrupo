@extends('admin.layout')

@section('title', 'Notícias do Blog')
@section('page-title', 'Gerenciar Blog')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-base font-black text-slate-900 dark:text-white">Notícias do Blog</h2>
        <p class="text-slate-400 text-sm mt-0.5">Gerencie as publicações do blog.</p>
    </div>
    <a href="{{ route('admin.blog.create') }}" class="btn btn-green shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Notícia
    </a>
</div>

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-xl px-4 py-3 text-sm font-semibold shadow-sm">
        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

<div class="card overflow-hidden dark:bg-slate-800 dark:border-slate-700">
    <div class="card-header dark:border-slate-700">
        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $posts->total() }} post(s)</span>
    </div>

    @if($posts->isEmpty())
        <div class="py-16 text-center">
            <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Nenhuma notícia encontrada.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Título</th>
                        <th class="text-left">Categoria</th>
                        <th class="text-center">Status</th>
                        <th class="text-right" style="min-width:110px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr>
                        <td>
                            <p class="font-bold text-slate-900 dark:text-white text-[13px]">{{ $post->title }}</p>
                            <p class="text-slate-400 text-[11px] font-medium">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="text-[12px] text-slate-600 dark:text-slate-400 font-semibold">
                            {{ $post->blogCategory->name ?? 'Sem categoria' }}
                        </td>
                        <td class="text-center">
                            @if($post->is_published)
                                <span class="badge badge-approved">Publicado</span>
                            @else
                                <span class="badge badge-pending">Rascunho</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.blog.edit', $post) }}"
                                   class="btn btn-blue" style="padding:5px 8px;font-size:11px;" title="Editar">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Excluir permanentemente esta notícia?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-slate" style="padding:5px 8px;font-size:11px;" title="Excluir">
                                        <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="flex justify-center items-center gap-1.5 px-5 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex-wrap">
                @if(!$posts->onFirstPage())
                    <a href="{{ $posts->previousPageUrl() }}" class="btn btn-slate" style="padding:6px 14px;font-size:12px;">← Anterior</a>
                @endif
                @if($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" class="btn btn-slate" style="padding:6px 14px;font-size:12px;">Próxima →</a>
                @endif
            </div>
        @endif
    @endif
</div>

@endsection
