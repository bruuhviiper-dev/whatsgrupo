<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Amizade', 'icon' => 'heroicon-o-users'],
            ['name' => 'Amor e Romance', 'icon' => 'heroicon-o-heart'],
            ['name' => 'Carros e Motos', 'icon' => 'heroicon-o-truck'],
            ['name' => 'Cidades', 'icon' => 'heroicon-o-building-office-2'],
            ['name' => 'Compra e Venda', 'icon' => 'heroicon-o-shopping-cart'],
            ['name' => 'Concursos', 'icon' => 'heroicon-o-academic-cap'],
            ['name' => 'Desenhos e Animes', 'icon' => 'heroicon-o-paint-brush'],
            ['name' => 'Divulgação', 'icon' => 'heroicon-o-megaphone'],
            ['name' => 'Educação', 'icon' => 'heroicon-o-book-open'],
            ['name' => 'Emagrecimento', 'icon' => 'heroicon-o-scale'],
            ['name' => 'Esportes', 'icon' => 'heroicon-o-trophy'],
            ['name' => 'Eventos', 'icon' => 'heroicon-o-calendar'],
            ['name' => 'Fãs', 'icon' => 'heroicon-o-star'],
            ['name' => 'Figurinhas', 'icon' => 'heroicon-o-face-smile'],
            ['name' => 'Filmes e Séries', 'icon' => 'heroicon-o-film'],
            ['name' => 'Frases e Mensagens', 'icon' => 'heroicon-o-chat-bubble-left-ellipsis'],
            ['name' => 'Futebol', 'icon' => 'heroicon-o-trophy'],
            ['name' => 'Games e Jogos', 'icon' => 'heroicon-o-puzzle-piece'],
            ['name' => 'Ganhar Dinheiro', 'icon' => 'heroicon-o-currency-dollar'],
            ['name' => 'Imobiliária', 'icon' => 'heroicon-o-home-modern'],
            ['name' => 'Investimentos', 'icon' => 'heroicon-o-chart-bar'],
            ['name' => 'Links', 'icon' => 'heroicon-o-link'],
            ['name' => 'Memes e Zoeira', 'icon' => 'heroicon-o-face-smile'],
            ['name' => 'Moda e Beleza', 'icon' => 'heroicon-o-sparkles'],
            ['name' => 'Música', 'icon' => 'heroicon-o-musical-note'],
            ['name' => 'Namoro', 'icon' => 'heroicon-o-heart'],
            ['name' => 'Negócios', 'icon' => 'heroicon-o-briefcase'],
            ['name' => 'Notícias', 'icon' => 'heroicon-o-newspaper'],
            ['name' => 'Outros', 'icon' => 'heroicon-o-squares-2x2'],
            ['name' => 'Política', 'icon' => 'heroicon-o-building-library'],
            ['name' => 'Profissões', 'icon' => 'heroicon-o-wrench'],
            ['name' => 'Receitas', 'icon' => 'heroicon-o-cake'],
            ['name' => 'Redes Sociais', 'icon' => 'heroicon-o-device-phone-mobile'],
            ['name' => 'Religião', 'icon' => 'heroicon-o-book-open'],
            ['name' => 'Shitpost', 'icon' => 'heroicon-o-trash'],
            ['name' => 'Tecnologia', 'icon' => 'heroicon-o-computer-desktop'],
            ['name' => 'TV', 'icon' => 'heroicon-o-tv'],
            ['name' => 'Vagas de Emprego', 'icon' => 'heroicon-o-briefcase'],
            ['name' => 'Viagem e Turismo', 'icon' => 'heroicon-o-globe-americas'],
            ['name' => 'Vídeos', 'icon' => 'heroicon-o-video-camera'],
        ];

        foreach ($categories as $index => $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'order' => $index + 1, // Mantém a ordem conforme definido na lista
                ]
            );
        }
    }
}
