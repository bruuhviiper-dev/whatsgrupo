<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SeoPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecialSeoPagesSeeder extends Seeder
{
    public function run(): void
    {
        $specialTerms = [
            ['term' => 'Figurinhas WhatsApp', 'cat' => 'figurinhas'],
            ['term' => 'Stickers WhatsApp', 'cat' => 'figurinhas'],
            ['term' => 'Vendas WhatsApp', 'cat' => 'compra-e-venda'],
            ['term' => 'WhatsApp Amizade', 'cat' => 'amizade'],
            ['term' => 'WhatsApp BTS', 'cat' => 'fas'],
            ['term' => 'WhatsApp Corinthians', 'cat' => 'futebol'],
            ['term' => 'WhatsApp Palmeiras', 'cat' => 'futebol'],
            ['term' => 'WhatsApp Flamengo', 'cat' => 'futebol'],
            ['term' => 'WhatsApp São Paulo FC', 'cat' => 'futebol'],
            ['term' => 'A Fazenda no WhatsApp', 'cat' => 'tv'],
            ['term' => 'Apostas Esportivas no WhatsApp', 'cat' => 'esportes'],
            ['term' => 'Big Brother Brasil do WhatsApp', 'cat' => 'tv'],
            ['term' => 'Bolsonaro no WhatsApp', 'cat' => 'politica'],
            ['term' => 'Caminhão no WhatsApp', 'cat' => 'carros-e-motos'],
            ['term' => 'LoL no WhatsApp', 'cat' => 'games-e-jogos'],
            ['term' => 'Lula no WhatsApp', 'cat' => 'politica'],
            ['term' => 'Otakus no WhatsApp', 'cat' => 'desenhos-e-animes'],
            ['term' => 'Pix do WhatsApp', 'cat' => 'ganhar-dinheiro'],
            ['term' => 'Sinais Blaze no WhatsApp', 'cat' => 'ganhar-dinheiro'],
            ['term' => 'WhatsApp Amigos', 'cat' => 'amizade'],
            ['term' => 'WhatsApp de Blox Fruits', 'cat' => 'games-e-jogos'],
            ['term' => 'WhatsApp de Caminhoneiros', 'cat' => 'carros-e-motos'],
            ['term' => 'WhatsApp de Kpop', 'cat' => 'fas'],
            ['term' => 'WhatsApp de Kwai', 'cat' => 'redes-sociais'],
            ['term' => 'WhatsApp de Now United', 'cat' => 'fas'],
            ['term' => 'WhatsApp de Roblox', 'cat' => 'games-e-jogos'],
            ['term' => 'WhatsApp de Minecraft', 'cat' => 'games-e-jogos'],
            ['term' => 'WhatsApp de Free Fire', 'cat' => 'games-e-jogos'],
            ['term' => 'WhatsApp de Animes', 'cat' => 'desenhos-e-animes'],
            ['term' => 'WhatsApp Memes e Zoeira', 'cat' => 'memes-e-zoeira'],
            ['term' => 'Vagas de Emprego WhatsApp', 'cat' => 'vagas-de-emprego'],
            ['term' => 'WhatsApp Concursos Públicos', 'cat' => 'concursos'],
            ['term' => 'WhatsApp Criptomoedas e Bitcoin', 'cat' => 'investimentos'],
            ['term' => 'WhatsApp Marketing Digital', 'cat' => 'negocios'],
            ['term' => 'WhatsApp Gacha Life', 'cat' => 'games-e-jogos'],
            ['term' => 'Frases e Status WhatsApp', 'cat' => 'frases-e-mensagens'],
            ['term' => 'WhatsApp Namoro e Cupido', 'cat' => 'namoro'],
            ['term' => 'WhatsApp Renda Extra', 'cat' => 'ganhar-dinheiro'],
            ['term' => 'WhatsApp Afiliados e Hotmart', 'cat' => 'negocios'],
            ['term' => 'WhatsApp Jovens Cristãos', 'cat' => 'religiao'],
            ['term' => 'WhatsApp Evangélicos e Oração', 'cat' => 'religiao'],
            ['term' => 'WhatsApp TikTokers e Divulgação', 'cat' => 'redes-sociais'],
        ];

        foreach ($specialTerms as $item) {
            $category = Category::where('slug', $item['cat'])->first();
            $slug = Str::slug("grupos-de-" . $item['term']);
            
            // Clean up name for visual presentation
            $cleanName = str_ireplace(['no WhatsApp', 'do WhatsApp', 'no whatsapp', 'do whatsapp', 'WhatsApp de', 'WhatsApp'], '', $item['term']);
            $cleanName = trim($cleanName);

            $title = "Grupos de WhatsApp de {$cleanName} | WhatsGrupos";
            $h1 = "Grupos de " . $item['term'];
            $metaDescription = "Encontre links ativos e verificados de " . mb_strtolower($item['term']) . " no WhatsGrupos. Entre na conversa, faça novas amizades e interaja gratuitamente!";
            
            $content = "Procurando por " . mb_strtolower($item['term']) . "? Você veio ao lugar certo! Nossa plataforma varre a internet para trazer os convites de grupos mais ativos e relevantes sobre esse assunto. Navegue, escolha o grupo ideal e junte-se à comunidade hoje mesmo!";

            SeoPage::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'            => $title,
                    'h1'               => $h1,
                    'meta_description' => $metaDescription,
                    'category_id'      => $category ? $category->id : null,
                    'keyword'          => mb_strtolower($item['term']),
                    'state'            => null,
                    'city'             => null,
                    'extra_term'       => null,
                    'content'          => $content,
                    'is_active'        => true,
                    'views'            => rand(100, 900),
                ]
            );
        }
    }
}
