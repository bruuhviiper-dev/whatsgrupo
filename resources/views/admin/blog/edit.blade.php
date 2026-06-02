@extends('admin.layout')

@section('title', 'Editar Notícia do Blog')
@section('page-title', 'Editar Notícia')

@section('content')

<div class="card dark:bg-slate-800 dark:border-slate-700 overflow-hidden p-6">
    <form action="{{ route('admin.blog.update', $post->id) }}" method="POST" class="no-confirm">
        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Título</label>
            <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-white bg-slate-50 dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 transition">
            @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Categoria</label>
            <select name="blog_category_id" required
                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 transition font-semibold">
                <option value="">Selecione uma categoria...</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('blog_category_id', $post->blog_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('blog_category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="mb-5" x-ignore>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Conteúdo</label>
            <textarea id="content" name="content" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm min-h-[300px]">{{ old('content', $post->content) }}</textarea>
            @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Meta Description (SEO)</label>
            <input type="text" name="meta_description" value="{{ old('meta_description', $post->meta_description) }}" maxlength="255"
                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-white bg-slate-50 dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 transition">
        </div>

        <div class="mb-6 flex items-center gap-3">
            <input type="hidden" name="is_published" value="0">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 transition-colors duration-200 dark:bg-slate-700"></div>
                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
            </label>
            <label for="is_published" class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Publicado</label>
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t border-slate-100 dark:border-slate-700">
            <a href="{{ route('admin.blog.index') }}" class="btn btn-slate" style="padding:8px 16px;font-size:13px;">Cancelar</a>
            <button type="submit" class="btn btn-green shadow-sm" style="padding:8px 20px;font-size:13px;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Atualizar Notícia
            </button>
        </div>
    </form>
</div>

{{-- O editor TinyMCE (vendor self-hosted) é inicializado de forma centralizada
     em resources/views/admin/layout.blade.php para qualquer textarea#content. --}}

@endsection
