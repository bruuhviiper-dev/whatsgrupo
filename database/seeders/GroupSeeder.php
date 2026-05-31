<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Group;
use App\Models\VerifiedGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Encontra as categorias no banco de dados para associar os IDs de forma correta
        $catAmizade = Category::where('slug', 'amizade')->first();
        $catNamoro = Category::where('slug', 'namoro')->first();
        $catGames = Category::where('slug', 'games-e-jogos')->first();
        $catTecnologia = Category::where('slug', 'tecnologia')->first();
        $catFutebol = Category::where('slug', 'futebol')->first();
        $catEmagrecimento = Category::where('slug', 'emagrecimento')->first();
        $catMemes = Category::where('slug', 'memes-e-zoeira')->first();
        $catViagem = Category::where('slug', 'viagem-e-turismo')->first();
        $catModa = Category::where('slug', 'moda-e-beleza')->first();
        $catReceitas = Category::where('slug', 'receitas')->first();
        $catDinheiro = Category::where('slug', 'ganhar-dinheiro')->first();
        $catFilmes = Category::where('slug', 'filmes-e-series')->first();

        // Lista de grupos de teste premium com dados realistas
        $groupsData = [
            [
                'category_id' => $catAmizade?->id,
                'name' => 'Amigos do Discord Brasil 👥',
                'description' => 'Um grupo para fazer novas amizades, conversar sobre variados temas, jogar papo fora e conhecer pessoas de todo o Brasil. Respeito mútuo é a nossa única regra principal!',
                'whatsapp_link' => 'https://chat.whatsapp.com/DiscordBrasilAmigos123',
                'status' => 'approved',
                'is_vip' => true,
                'vip_expires_at' => now()->addDays(7),
                'views' => 1240,
                'clicks' => 540,
                'score' => 9.5,
                'verified' => true
            ],
            [
                'category_id' => $catNamoro?->id,
                'name' => 'Cupido WhatsApp BR 💕',
                'description' => 'Encontre a sua metade da laranja ou faça novas amizades românticas. O grupo é moderado de forma rigorosa contra spam e conteúdo adulto. Venha se apaixonar!',
                'whatsapp_link' => 'https://chat.whatsapp.com/CupidoRomanceBR98765',
                'status' => 'approved',
                'is_vip' => true,
                'vip_expires_at' => now()->addDays(5),
                'views' => 2300,
                'clicks' => 980,
                'score' => 9.8,
                'verified' => false
            ],
            [
                'category_id' => $catGames?->id,
                'name' => 'Minecraft Brasil Oficial 🎮',
                'description' => 'Comunidade dedicada aos amantes de Minecraft. Compartilhe IPs de servidores, construções épicas, mods, texturas e jogue multiplayer com a galera no chat de voz!',
                'whatsapp_link' => 'https://chat.whatsapp.com/MineCraftBrasilOficial',
                'status' => 'approved',
                'is_vip' => false,
                'vip_expires_at' => null,
                'views' => 840,
                'clicks' => 310,
                'score' => 8.2,
                'verified' => true
            ],
            [
                'category_id' => $catTecnologia?->id,
                'name' => 'Devs Fullstack Brasil 💻',
                'description' => 'Grupo voltado para programadores PHP, Laravel, Node.js, React e Python. Troca de conhecimentos, dicas de carreira, auxílio em bugs e oportunidades de emprego na área.',
                'whatsapp_link' => 'https://chat.whatsapp.com/DevsFullstackBrasil',
                'status' => 'approved',
                'is_vip' => false,
                'vip_expires_at' => null,
                'views' => 910,
                'clicks' => 420,
                'score' => 8.7,
                'verified' => false
            ],
            [
                'category_id' => $catFutebol?->id,
                'name' => 'Resenha do Brasileirão ⚽',
                'description' => 'Aqui a zoeira e o debate sobre o futebol brasileiro não param! Discussões sobre jogos, contratações, memes de rodadas e notícias quentes do futebol nacional e internacional.',
                'whatsapp_link' => 'https://chat.whatsapp.com/ResenhaBrasileiraoFutebol',
                'status' => 'approved',
                'is_vip' => false,
                'vip_expires_at' => null,
                'views' => 610,
                'clicks' => 210,
                'score' => 7.5,
                'verified' => false
            ],
            [
                'category_id' => $catEmagrecimento?->id,
                'name' => 'Vida Saudável & Fit 🥗',
                'description' => 'Mude de vida agora mesmo! Receitas de sucos detox, treinos diários para fazer em casa, dicas de alimentação limpa e apoio mútuo para alcançar o seu peso ideal de forma saudável.',
                'whatsapp_link' => 'https://chat.whatsapp.com/VidaSaudavelEmagrecimento',
                'status' => 'approved',
                'is_vip' => true,
                'vip_expires_at' => now()->addDays(10),
                'views' => 1740,
                'clicks' => 680,
                'score' => 9.2,
                'verified' => false
            ],
            [
                'category_id' => $catMemes?->id,
                'name' => 'Shitpost & Memes BR 😂',
                'description' => 'Apenas os melhores memes, vídeos engraçados e shitposts da internet para você começar o seu dia sorrindo. Proibido conteúdo ofensivo ou intolerante. Foco 100% no humor livre!',
                'whatsapp_link' => 'https://chat.whatsapp.com/ShitpostMemesBRZoeira',
                'status' => 'approved',
                'is_vip' => false,
                'vip_expires_at' => null,
                'views' => 3120,
                'clicks' => 1420,
                'score' => 9.4,
                'verified' => false
            ],
            [
                'category_id' => $catViagem?->id,
                'name' => 'Mochileiros pelo Mundo ✈️',
                'description' => 'Dicas de viagens baratas, passagens promocionais, roteiros de mochilão, hospedagem barata e relatos incríveis de pessoas que estão explorando as belezas do Brasil e do mundo inteiro.',
                'whatsapp_link' => 'https://chat.whatsapp.com/MochileirosPeloMundoViagem',
                'status' => 'approved',
                'is_vip' => false,
                'vip_expires_at' => null,
                'views' => 430,
                'clicks' => 120,
                'score' => 6.9,
                'verified' => false
            ],
            [
                'category_id' => $catModa?->id,
                'name' => 'Dicas de Maquiagem Premium 💄',
                'description' => 'Comunidade voltada para moda, maquiagem, skin care e beleza. Tutoriais em vídeo, indicação de produtos BBB (bom, bonito e barato) e muito empoderamento feminino.',
                'whatsapp_link' => 'https://chat.whatsapp.com/ModaBelezaMaquiagemDicas',
                'status' => 'approved',
                'is_vip' => true,
                'vip_expires_at' => now()->addDays(4),
                'views' => 1410,
                'clicks' => 690,
                'score' => 9.3,
                'verified' => true
            ],
            [
                'category_id' => $catReceitas?->id,
                'name' => 'Culinária Prática do Dia 🍳',
                'description' => 'Receitas rápidas, fáceis e extremamente saborosas para almoços, jantares e sobremesas. Dicas de confeitaria, culinária caseira e compartilhamento de pratos do dia a dia.',
                'whatsapp_link' => 'https://chat.whatsapp.com/CulinariaPraticaReceitasDia',
                'status' => 'approved',
                'is_vip' => false,
                'vip_expires_at' => null,
                'views' => 520,
                'clicks' => 230,
                'score' => 7.8,
                'verified' => false
            ],
            [
                'category_id' => $catDinheiro?->id,
                'name' => 'Dicas de Renda Extra 💰',
                'description' => 'Aprenda métodos legítimos de obter renda extra na internet através de sites, aplicativos, freelancing e marketing digital. Proibido esquemas de pirâmide ou promessas fáceis!',
                'whatsapp_link' => 'https://chat.whatsapp.com/RendaExtraDinheiroDicas',
                'status' => 'approved',
                'is_vip' => false,
                'vip_expires_at' => null,
                'views' => 1100,
                'clicks' => 500,
                'score' => 8.5,
                'verified' => false
            ],
            [
                'category_id' => $catFilmes?->id,
                'name' => 'Cinefilia BR — Cinema & TV 🎬',
                'description' => 'Espaço de debate para cinéfilos e maratonistas de plantão. Opiniões sobre filmes novos, séries em destaque na Netflix e HBO, teorias, spoilers moderados e indicações de obras.',
                'whatsapp_link' => 'https://chat.whatsapp.com/CinefiliaCinemaSeriesTV',
                'status' => 'approved',
                'is_vip' => false,
                'vip_expires_at' => null,
                'views' => 680,
                'clicks' => 290,
                'score' => 8.0,
                'verified' => false
            ],
        ];

        foreach ($groupsData as $data) {
            // Ignora se a categoria não existir para evitar erros
            if (!$data['category_id']) {
                continue;
            }

            $group = Group::updateOrCreate(
                ['whatsapp_link' => $data['whatsapp_link']],
                [
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'submitter_email' => 'teste@whatsgrupos.com',
                    'status' => $data['status'],
                    'is_vip' => false,
                    'vip_expires_at' => null,
                    'views' => $data['views'],
                    'clicks' => $data['clicks'],
                    'score' => $data['score'],
                ]
            );
        }
    }
}
