<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job responsável por gerar o arquivo public/sitemap.xml com todas as
 * URLs estáticas, categorias e grupos aprovados do site.
 * Executado uma vez por dia via agendador do Laravel.
 */
class GenerateSitemapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Executa a geração do sitemap.xml e salva em public/.
     */
    public function handle(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $today = now()->toAtomString();

        // Monta o XML do sitemap
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Rotas estáticas principais
        $staticUrls = [
            ['loc' => $baseUrl . '/',                priority => '1.0', changefreq => 'daily'],
            ['loc' => $baseUrl . '/enviar-grupo',    priority => '0.8', changefreq => 'monthly'],
            ['loc' => $baseUrl . '/pacotes-vip',     priority => '0.9', changefreq => 'weekly'],
            ['loc' => $baseUrl . '/meus-grupos',     priority => '0.5', changefreq => 'monthly'],
            ['loc' => $baseUrl . '/buscar',          priority => '0.6', changefreq => 'monthly'],
        ];

        foreach ($staticUrls as $url) {
            $xml .= $this->buildUrlEntry($url['loc'], $today, $url['changefreq'], $url['priority']);
        }

        // URLs das categorias
        $categories = Category::ordered()->get();
        foreach ($categories as $category) {
            $url = $baseUrl . '/categoria/' . $category->slug;
            $xml .= $this->buildUrlEntry($url, $today, 'daily', '0.8');
        }

        // URLs dos grupos aprovados (limitado a 10.000 conforme especificado)
        $groups = Group::approved()
            ->orderBy('created_at', 'desc')
            ->limit(10000)
            ->get(['slug', 'updated_at']);

        foreach ($groups as $group) {
            $url = $baseUrl . '/grupo/' . $group->slug;
            $lastmod = $group->updated_at->toAtomString();
            $xml .= $this->buildUrlEntry($url, $lastmod, 'weekly', '0.7');
        }

        $xml .= '</urlset>';

        // Salva o sitemap em public/sitemap.xml
        $sitemapPath = public_path('sitemap.xml');
        file_put_contents($sitemapPath, $xml);

        Log::info('[GenerateSitemapJob] Sitemap gerado com ' . ($categories->count() + $groups->count() + count($staticUrls)) . ' URLs.');
    }

    /**
     * Constrói uma entrada de URL para o sitemap XML.
     */
    protected function buildUrlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n"
            . "    <loc>" . htmlspecialchars($loc) . "</loc>\n"
            . "    <lastmod>{$lastmod}</lastmod>\n"
            . "    <changefreq>{$changefreq}</changefreq>\n"
            . "    <priority>{$priority}</priority>\n"
            . "  </url>\n";
    }
}
