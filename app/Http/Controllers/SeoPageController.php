<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use App\Models\SeoPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controller responsável por exibir as páginas de SEO de cauda longa com suporte a Cache.
 */
class SeoPageController extends Controller
{
    /**
     * Exibe o índice de categorias especiais / páginas SEO.
     */
    public function index()
    {
        $seoPages = SeoPage::active()
            ->whereNull('state') // Apenas categorias especiais, sem repetições de estados
            ->orderBy('title', 'asc')
            ->get();

        $categories = Category::ordered()->get();

        return view('seo-page-index', compact('seoPages', 'categories'));
    }

    /**
     * Exibe grupos e detalhes de uma página SEO de cauda longa específica.
     */
    public function show(SeoPage $seoPage)
    {
        // Garante que a página está ativa
        abort_if(!$seoPage->is_active, 404);

        // Incrementa o contador de visualizações
        $seoPage->increment('views');

        $currentPage = request()->get('page', 1);
        $cacheKey = "seo_page_{$seoPage->slug}_groups_page_{$currentPage}";

        $data = Cache::remember($cacheKey, 300, function () use ($seoPage, $currentPage) {
            $category = $seoPage->category;

            // Extrai a palavra chave de busca limpando sufixos/prefixos comuns de whatsapp
            $searchTerm = trim(str_ireplace(
                ['grupos de whatsapp de', 'grupos de whatsapp', 'grupo de whatsapp', 'grupos whatsapp', 'grupo whatsapp', 'no whatsapp', 'do whatsapp', 'de whatsapp', 'whatsapp', 'grupos', 'grupo'],
                '',
                $seoPage->keyword
            ));

            $vipQuery = Group::with(['category', 'verifiedGroup'])->approved()->notExpiredVip();
            $normalQuery = Group::with(['category', 'verifiedGroup'])->approved()
                ->where(function ($q) {
                    $q->where('is_vip', false)
                      ->orWhereNull('vip_expires_at')
                      ->orWhere('vip_expires_at', '<=', now()->toDateTimeString());
                });

            // Define os sinônimos e termos relacionados para busca textual estrita
            $synonyms = [];
            $lowerSearch = mb_strtolower($searchTerm);

            if (str_contains($lowerSearch, 'palmeiras')) {
                $synonyms = ['palmeiras', 'verdão', 'alviverde'];
            } elseif (str_contains($lowerSearch, 'flamengo')) {
                $synonyms = ['flamengo', 'mengo', 'rubro-negro', 'fla'];
            } elseif (str_contains($lowerSearch, 'corinthians')) {
                $synonyms = ['corinthians', 'timão', 'alvinegro'];
            } elseif (str_contains($lowerSearch, 'são paulo')) {
                $synonyms = ['são paulo', 'tricolor', 'spfc'];
            } elseif (str_contains($lowerSearch, 'figurinhas') || str_contains($lowerSearch, 'stickers')) {
                $synonyms = ['figurinha', 'sticker', 'pack'];
            } elseif (str_contains($lowerSearch, 'blox fruits') || str_contains($lowerSearch, 'roblox')) {
                $synonyms = ['roblox', 'blox fruit', 'fruit'];
            } elseif (str_contains($lowerSearch, 'free fire')) {
                $synonyms = ['free fire', 'ff', 'guilda'];
            } elseif (str_contains($lowerSearch, 'cripto') || str_contains($lowerSearch, 'bitcoin')) {
                $synonyms = ['cripto', 'bitcoin', 'btc', 'ethereum', 'coin'];
            } elseif (str_contains($lowerSearch, 'aposta') || str_contains($lowerSearch, 'blaze') || str_contains($lowerSearch, 'sinais')) {
                $synonyms = ['aposta', 'sinal', 'blaze', 'bet', 'tigrinho', 'green'];
            } elseif (str_contains($lowerSearch, 'concurso')) {
                $synonyms = ['concurso', 'público', 'estudo', 'edital'];
            } elseif (str_contains($lowerSearch, 'afiliado') || str_contains($lowerSearch, 'hotmart')) {
                $synonyms = ['afiliado', 'hotmart', 'kiwify', 'monetizze', 'venda'];
            } elseif (str_contains($lowerSearch, 'cristão') || str_contains($lowerSearch, 'evangélico') || str_contains($lowerSearch, 'oração')) {
                $synonyms = ['cristão', 'evangélico', 'oração', 'jesus', 'deus', 'igreja'];
            } else {
                $synonyms = [$searchTerm];
            }

            // Realiza busca estrita por correspondência textual nos campos name ou description
            if (!empty($searchTerm) && strlen($searchTerm) >= 3) {
                $vipQuery->where(function ($q) use ($synonyms) {
                    foreach ($synonyms as $term) {
                        $q->orWhere('name', 'like', '%' . $term . '%')
                          ->orWhere('description', 'like', '%' . $term . '%');
                    }
                });

                $normalQuery->where(function ($q) use ($synonyms) {
                    foreach ($synonyms as $term) {
                        $q->orWhere('name', 'like', '%' . $term . '%')
                          ->orWhere('description', 'like', '%' . $term . '%');
                    }
                });
            } elseif ($category) {
                $vipQuery->where('category_id', $category->id);
                $normalQuery->where('category_id', $category->id);
            } else {
                $vipQuery->whereRaw('1 = 0');
                $normalQuery->whereRaw('1 = 0');
            }

            $vipGroups = $vipQuery->orderBy('vip_expires_at', 'desc')->get();
            $normalGroups = $normalQuery->orderBy('score', 'desc')->get();
            $allGroups = $vipGroups->concat($normalGroups);

            // Paginação de 20 grupos
            $perPage = 20;
            $groups = new \Illuminate\Pagination\LengthAwarePaginator(
                $allGroups->forPage($currentPage, $perPage),
                $allGroups->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            // Busca 5 páginas SEO relacionadas para linkagem interna
            $relatedPages = SeoPage::active()
                ->where('id', '!=', $seoPage->id)
                ->where(function ($q) use ($seoPage) {
                    if ($seoPage->category_id) {
                        $q->where('category_id', $seoPage->category_id);
                    }
                    if ($seoPage->state) {
                        $q->orWhere('state', $seoPage->state);
                    }
                })
                ->inRandomOrder()
                ->limit(5)
                ->get();

            // Categorias para a sidebar
            $categories = Category::ordered()->withCount(['groups' => function ($q) {
                $q->where('status', 'approved');
            }])->get();

            return compact('groups', 'relatedPages', 'categories');
        });

        $groups = $data['groups'];
        $relatedPages = $data['relatedPages'];
        $categories = $data['categories'];
        $category = $seoPage->category;

        return view('seo-page', compact('seoPage', 'groups', 'category', 'relatedPages', 'categories'));
    }
}
