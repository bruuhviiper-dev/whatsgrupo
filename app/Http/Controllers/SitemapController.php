<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Group;
use App\Models\SeoPage;

/**
 * Controller para geração e entrega do sitemap.xml sob demanda,
 * atuando como um Sitemap Index que agrupa sitemaps especializados.
 */
class SitemapController extends Controller
{
    /**
     * Entrega o Sitemap Index principal.
     */
    public function index()
    {
        return response()
            ->view('sitemap-index')
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Entrega o sitemap estático (home, páginas fixas, categorias).
     */
    public function static()
    {
        $baseUrl = rtrim(config('app.url'), '/');

        // ── URLs estáticas: todo o ecossistema público do site ──
        $staticUrls = [
            // Núcleo / listagens principais (alta prioridade)
            ['loc' => $baseUrl . '/',                      'changefreq' => 'daily',   'priority' => '1.0'],
            ['loc' => $baseUrl . '/grupos-novos',          'changefreq' => 'daily',   'priority' => '0.8'],
            ['loc' => $baseUrl . '/grupos-mais-populares', 'changefreq' => 'daily',   'priority' => '0.8'],
            ['loc' => $baseUrl . '/grupos-novos-hoje',     'changefreq' => 'daily',   'priority' => '0.8'],
            ['loc' => $baseUrl . '/blog',                  'changefreq' => 'daily',   'priority' => '0.8'],

            // Conversão / engajamento
            ['loc' => $baseUrl . '/enviar-grupo',          'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => $baseUrl . '/pacotes-vip',           'changefreq' => 'weekly',  'priority' => '0.8'],
            ['loc' => $baseUrl . '/anuncie',               'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/meus-grupos',           'changefreq' => 'monthly', 'priority' => '0.4'],

            // Hub de conteúdo: frases e figurinhas
            ['loc' => $baseUrl . '/frases',                'changefreq' => 'weekly',  'priority' => '0.7'],
            ['loc' => $baseUrl . '/enviar-frase',          'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => $baseUrl . '/figurinhas-whatsapp',   'changefreq' => 'weekly',  'priority' => '0.7'],

            // Ferramentas (URLs na raiz para SEO)
            ['loc' => $baseUrl . '/analise-de-engajamento','changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/gerador-de-regras',     'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/gerador-de-nomes',      'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/mensagem-de-boas-vindas','changefreq' => 'monthly','priority' => '0.6'],
            ['loc' => $baseUrl . '/verificador-de-link',   'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/gerador-de-enquete',    'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/gerador-de-letras',     'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/gerador-de-sorteios',   'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/detector-de-spam',      'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/widget-gerador',        'changefreq' => 'monthly', 'priority' => '0.4'],

            // Institucional / legal
            ['loc' => $baseUrl . '/faq',                   'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/contato',               'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => $baseUrl . '/termos-de-uso',         'changefreq' => 'yearly',  'priority' => '0.3'],
            ['loc' => $baseUrl . '/politica-de-privacidade','changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        // Categorias do diretório de grupos
        $categories = Category::ordered()->get();

        // Categorias do blog
        $blogCategories = \App\Models\BlogCategory::all(['slug']);

        // Categorias de frases (definidas no StatusPhraseController)
        $phraseCategories = collect(
            (new \App\Http\Controllers\StatusPhraseController)->getExtendedCategories()
        )->keys();

        return response()
            ->view('sitemap-static', compact('staticUrls', 'categories', 'blogCategories', 'phraseCategories', 'baseUrl'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Entrega o sitemap contendo os links de todos os grupos ativos aprovados (máximo 10.000).
     */
    public function groups()
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $groups = Group::approved()
            ->orderBy('updated_at', 'desc')
            ->limit(10000)
            ->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap-groups', compact('groups', 'baseUrl'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Entrega o sitemap contendo os links de todas as páginas SEO de cauda longa (máximo 10.000).
     */
    public function seo()
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $seoPages = SeoPage::active()
            ->orderBy('updated_at', 'desc')
            ->limit(10000)
            ->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap-seo', compact('seoPages', 'baseUrl'))
            ->header('Content-Type', 'application/xml');
    }

    public function blog()
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $posts = BlogPost::where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap-blog', compact('posts', 'baseUrl'))
            ->header('Content-Type', 'application/xml');
    }
}
