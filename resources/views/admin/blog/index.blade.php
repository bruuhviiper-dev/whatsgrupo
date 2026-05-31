@extends('admin.layout')

@section('title', 'Notícias do Blog')
@section('page-title', 'Gerenciar Blog')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Notícias do Blog</h1>
    <a href="{{ route('admin.blog.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-sm">+ Nova Notícia</a>
</div>

@if(session('success'))
<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
  {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="admin-table w-full">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="text-left py-3 px-5 text-xs font-bold text-slate-500 uppercase tracking-wider">Título</th>
                <th class="text-left py-3 px-5 text-xs font-bold text-slate-500 uppercase tracking-wider">Categoria</th>
                <th class="text-center py-3 px-5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="text-right py-3 px-5 text-xs font-bold text-slate-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($posts as $post)
            <tr class="hover:bg-slate-50">
                <td class="py-3 px-5">
                    <p class="font-bold text-slate-900">{{ $post->title }}</p>
                    <p class="text-xs text-slate-500">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                </td>
                <td class="py-3 px-5 text-sm text-slate-700">
                    {{ $post->blogCategory->name ?? 'Sem categoria' }}
                </td>
                <td class="py-3 px-5 text-center">
                    @if($post->is_published)
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">Publicado</span>
                    @else
                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded">Rascunho</span>
                    @endif
                </td>
                <td class="py-3 px-5 text-right flex justify-end gap-2">
                    <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-slate" style="padding:6px 10px;font-size:11px;" title="Editar">
                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta notícia?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-slate" style="padding:6px 10px;font-size:11px;" title="Excluir">
                            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-slate-500">Nenhuma notícia encontrada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">
        {{ $posts->links() }}
    </div>
</div>

@endsection
