<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use Illuminate\Http\Request;

/**
 * Controller responsável pelo widget embarcável de grupos de WhatsApp.
 */
class WidgetController extends Controller
{
    /**
     * Retorna a view stand-alone com 6 grupos de uma categoria específica (ou geral).
     */
    public function show(Request $request, string $category_slug = 'all')
    {
        $query = Group::approved()->with('category', 'verifiedGroup');

        $categoryName = 'Geral';
        if ($category_slug !== 'all' && $category_slug !== '') {
            $category = Category::where('slug', $category_slug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
                $categoryName = $category->name;
            }
        }

        // Ordenação: VIP ativo primeiro, depois score decrescente
        $groups = $query->orderByRaw('CASE WHEN is_vip = 1 AND vip_expires_at > ? THEN 1 ELSE 0 END DESC', [now()->toDateTimeString()])
            ->orderBy('score', 'desc')
            ->limit(6)
            ->get();

        return view('widget.embed', compact('groups', 'category_slug', 'categoryName'));
    }

    /**
     * Retorna o script JS de injeção dinâmica.
     */
    public function script(Request $request)
    {
        return response()->view('widget.script')
            ->header('Content-Type', 'application/javascript');
    }

    /**
     * Página do gerador de widgets públicos.
     */
    public function generator(Request $request)
    {
        $categories = Category::ordered()->get();
        return view('widget-gerador', compact('categories'));
    }
}
