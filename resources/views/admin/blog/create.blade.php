@extends('admin.layout')

@section('title', 'Nova Notícia do Blog')
@section('page-title', 'Nova Notícia')

@section('content')


<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6">
    <form action="{{ route('admin.blog.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-1">Título</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-1">Categoria</label>
            <select name="blog_category_id" required class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Selecione uma categoria...</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('blog_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('blog_category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4" x-ignore>
            <label class="block text-sm font-bold text-slate-700 mb-1">Conteúdo</label>
            <textarea id="content" name="content" class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm min-h-[300px]">{{ old('content') }}</textarea>
            @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-1">Meta Description (SEO)</label>
            <input type="text" name="meta_description" value="{{ old('meta_description') }}" maxlength="255" class="w-full px-4 py-2 border border-slate-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="mb-6 flex items-center">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <label for="is_published" class="ml-2 text-sm font-bold text-slate-700">Publicar imediatamente</label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.blog.index') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg font-bold hover:bg-slate-300">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">Salvar Notícia</button>
        </div>
    </form>

<script src="{{ asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>

<!-- <script src="https://cdn.tiny.cloud/1/7z8np3ft9l8rtj4bcrbg73k20hcdds5eziv9n9f4psyk1oyx/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
  tinymce.init({
    selector: 'textarea#content', // Replace this CSS selector to match the placeholder element for TinyMCE
    plugins: 'code table lists',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
  });
</script> -->


<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: 'textarea#content',
            license_key: 'gpl',
            plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table directionality emoticons template',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            toolbar_mode: 'sliding',
            height: 500,
            language: 'pt_BR',
            skin: 'oxide',
            promotion: false
        });
    });
</script>
</div>

@endsection

