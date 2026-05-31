<?php

namespace App\Http\Controllers;

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

        // URLs estáticas fixas da plataforma
        $staticUrls = [
            ['loc' => $baseUrl . '/',                    'changefreq' => 'daily',   'priority' => '1.0'],
            ['loc' => $baseUrl . '/enviar-grupo',        'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => $baseUrl . '/pacotes-vip',         'changefreq' => 'weekly',  'priority' => '0.9'],
            ['loc' => $baseUrl . '/meus-grupos',         'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/grupos-novos',        'changefreq' => 'daily',   'priority' => '0.8'],
            ['loc' => $baseUrl . '/grupos-mais-populares','changefreq' => 'daily',   'priority' => '0.8'],
            ['loc' => $baseUrl . '/grupos-novos-hoje',   'changefreq' => 'daily',   'priority' => '0.8'],
        ];

        // Categorias do diretório
        $categories = Category::ordered()->get();

        return response()
            ->view('sitemap-static', compact('staticUrls', 'categories', 'baseUrl'))
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
}
