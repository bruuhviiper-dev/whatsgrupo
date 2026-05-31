<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('blogCategory')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:255',
        ]);

        $slug = $this->generateUniqueSlug($request->title);

        BlogPost::create([
            'title' => $request->title,
            'slug' => $slug,
            'blog_category_id' => $request->blog_category_id,
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'is_published' => (bool) $request->input('is_published', false),
        ]);

        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('admin.blog.index')->with('success', 'Notícia criada com sucesso!');
    }

    public function edit($id)
    {
        $blog = BlogPost::findOrFail($id);
        $categories = BlogCategory::all();
        return view('admin.blog.edit', ['post' => $blog, 'categories' => $categories]);
    }

    public function update(Request $request, $id)
    {
        $blog = BlogPost::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:255',
        ]);

        // Only generate new slug if title changed
        if ($request->title !== $blog->title) {
            $blog->slug = $this->generateUniqueSlug($request->title, $blog->id);
        }

        $blog->update([
            'title' => $request->title,
            'blog_category_id' => $request->blog_category_id,
            'content' => $request->content,
            'meta_description' => $request->meta_description,
            'is_published' => (bool) $request->input('is_published', false),
        ]);

        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('admin.blog.index')->with('success', 'Notícia atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $blog = BlogPost::findOrFail($id);
        $blog->delete();
        \Illuminate\Support\Facades\Cache::flush();
        return redirect()->route('admin.blog.index')->with('success', 'Notícia excluída com sucesso!');
    }

    private function generateUniqueSlug($title, $ignoreId = 0)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (BlogPost::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
