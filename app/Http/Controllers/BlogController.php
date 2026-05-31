<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Exibe a listagem do blog.
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');

        $postsQuery = BlogPost::where('is_published', true);

        if (!empty($query)) {
            $postsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('content', 'like', '%' . $query . '%');
            });
        }

        $posts = $postsQuery->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString();

        $blogCategories = \App\Models\BlogCategory::all();

        return view('blog.index', compact('posts', 'blogCategories', 'query'));
    }

    public function category(string $slug)
    {
        $category = \App\Models\BlogCategory::where('slug', $slug)->firstOrFail();
        
        $posts = BlogPost::where('is_published', true)
            ->where('blog_category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        $blogCategories = \App\Models\BlogCategory::all();
        $query = ''; // Vazio, mantido por compatibilidade com a view index

        return view('blog.index', compact('posts', 'blogCategories', 'query', 'category'));
    }

    /**
     * Exibe um artigo individual.
     */
    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Incrementa visualização
        $post->increment('views');

        // Artigos relacionados (preferencialmente da mesma categoria)
        $relatedPosts = BlogPost::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->where('blog_category_id', $post->blog_category_id)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        if ($relatedPosts->count() < 3) {
            $relatedPosts = BlogPost::where('is_published', true)
                ->where('id', '!=', $post->id)
                ->inRandomOrder()
                ->limit(5)
                ->get();
        }

        $blogCategories = \App\Models\BlogCategory::all();

        return view('blog.show', compact('post', 'relatedPosts', 'blogCategories'));
    }
}
