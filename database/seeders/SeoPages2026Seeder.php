<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SeoPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder para gerar páginas SEO com o termo "2026".
 * Substitui páginas com "2025" e cria novas com "2026" para manter o conteúdo atualizado.
 */
class SeoPages2026Seeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('Nenhuma categoria encontrada. Execute o CategorySeeder primeiro.');
            return;
        }

        $count = 0;

        foreach ($categories as $category) {
            $catSlug = Str::slug($category->name);
            $slug2026 = "grupos-whatsapp-{$catSlug}-2026";
            $slug2025 = "grupos-whatsapp-{$catSlug}-2025";

            // Desativa a página 2025 se existir
            SeoPage::where('slug', $slug2025)->update(['is_active' => false]);

            $keyword = "grupos whatsapp " . mb_strtolower($category->name) . " 2026";
            $title   = "Grupos de WhatsApp de {$category->name} 2026 | WhatsGrupos";
            $h1      = "Grupos de WhatsApp de {$category->name} 2026";
            $meta    = Str::limit("Os melhores grupos de WhatsApp de {$category->name} em 2026. Links verificados e ativos para você entrar agora. Lista atualizada!", 155);
            $content = "Você está procurando grupos de WhatsApp de {$category->name} em 2026? Aqui no WhatsGrupos você encontra os grupos mais ativos e atualizados do Brasil. Nossa lista é revisada diariamente para garantir apenas links válidos. Entre agora e faça parte das melhores comunidades de {$category->name}!";

            SeoPage::updateOrCreate(
                ['slug' => $slug2026],
                [
                    'title'            => $title,
                    'slug'             => $slug2026,
                    'h1'               => $h1,
                    'meta_description' => $meta,
                    'category_id'      => $category->id,
                    'keyword'          => $keyword,
                    'state'            => null,
                    'city'             => null,
                    'extra_term'       => '2026',
                    'content'          => $content,
                    'is_active'        => true,
                    'views'            => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );

            $count++;
        }

        // Páginas especiais com "2026"
        $specialTerms2026 = [
            ['term' => 'Figurinhas WhatsApp 2026',          'cat' => 'figurinhas'],
            ['term' => 'Free Fire WhatsApp 2026',           'cat' => 'games-e-jogos'],
            ['term' => 'Roblox WhatsApp 2026',              'cat' => 'games-e-jogos'],
            ['term' => 'Vagas de Emprego WhatsApp 2026',    'cat' => 'vagas-de-emprego'],
            ['term' => 'Criptomoedas WhatsApp 2026',        'cat' => 'investimentos'],
            ['term' => 'Concursos Públicos WhatsApp 2026',  'cat' => 'concursos'],
            ['term' => 'Memes e Zoeira WhatsApp 2026',      'cat' => 'memes-e-zoeira'],
            ['term' => 'Namoro WhatsApp 2026',              'cat' => 'namoro'],
            ['term' => 'Renda Extra WhatsApp 2026',         'cat' => 'ganhar-dinheiro'],
            ['term' => 'Marketing Digital WhatsApp 2026',   'cat' => 'negocios'],
            ['term' => 'Palmeiras WhatsApp 2026',           'cat' => 'futebol'],
            ['term' => 'Flamengo WhatsApp 2026',            'cat' => 'futebol'],
            ['term' => 'Corinthians WhatsApp 2026',         'cat' => 'futebol'],
            ['term' => 'Animes WhatsApp 2026',              'cat' => 'desenhos-e-animes'],
            ['term' => 'Amizade WhatsApp 2026',             'cat' => 'amizade'],
            ['term' => 'Evangélicos WhatsApp 2026',         'cat' => 'religiao'],
            ['term' => 'Sinais Blaze WhatsApp 2026',        'cat' => 'ganhar-dinheiro'],
            ['term' => 'Afiliados Hotmart WhatsApp 2026',   'cat' => 'negocios'],
            ['term' => 'TikTok WhatsApp 2026',              'cat' => 'redes-sociais'],
            ['term' => 'Minecraft WhatsApp 2026',           'cat' => 'games-e-jogos'],
        ];

        foreach ($specialTerms2026 as $item) {
            $category = Category::where('slug', $item['cat'])->first();
            $slug2026 = Str::slug("grupos-de-" . $item['term']);

            // Limpa nome para apresentação
            $cleanName = str_ireplace(['whatsapp 2026', 'whatsapp', '2026'], '', $item['term']);
            $cleanName = trim($cleanName);

            SeoPage::updateOrCreate(
                ['slug' => $slug2026],
                [
                    'title'            => "Grupos de WhatsApp de {$cleanName} 2026 | WhatsGrupos",
                    'h1'               => "Grupos de {$item['term']}",
                    'meta_description' => Str::limit("Encontre os melhores grupos de WhatsApp de {$cleanName} em 2026. Links verificados e ativos. Entre agora gratuitamente!", 155),
                    'category_id'      => $category ? $category->id : null,
                    'keyword'          => mb_strtolower($item['term']),
                    'state'            => null,
                    'city'             => null,
                    'extra_term'       => '2026',
                    'content'          => "Procurando grupos de {$cleanName} no WhatsApp em 2026? Você veio ao lugar certo! Aqui no WhatsGrupos temos os links mais atualizados e ativos. Entre agora e faça parte dessas comunidades incríveis!",
                    'is_active'        => true,
                    'views'            => rand(50, 500),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );

            $count++;
        }

        // Desativa todas as páginas com extra_term = '2025'
        $deactivated = SeoPage::where('extra_term', '2025')->update(['is_active' => false]);

        $this->command->info("✅ {$count} páginas SEO 2026 criadas/atualizadas.");
        $this->command->info("🔴 {$deactivated} páginas 2025 desativadas.");
    }
}
