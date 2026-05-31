<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tutoriais',
                'slug' => 'tutoriais',
                'icon' => 'heroicon-o-book-open',
            ],
            [
                'name' => 'Dicas e Truques',
                'slug' => 'dicas-e-truques',
                'icon' => 'heroicon-o-light-bulb',
            ],
            [
                'name' => 'Notícias',
                'slug' => 'noticias',
                'icon' => 'heroicon-o-newspaper',
            ],
            [
                'name' => 'Atualizações',
                'slug' => 'atualizacoes',
                'icon' => 'heroicon-o-arrow-path',
            ],
            [
                'name' => 'Comunidade',
                'slug' => 'comunidade',
                'icon' => 'heroicon-o-users',
            ],
        ];

        foreach ($categories as $cat) {
            \App\Models\BlogCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'icon' => $cat['icon']]
            );
        }

        // Associar postagens existentes à categoria Tutoriais (primeira)
        $defaultCat = \App\Models\BlogCategory::first();
        if ($defaultCat) {
            \App\Models\BlogPost::whereNull('blog_category_id')->update([
                'blog_category_id' => $defaultCat->id
            ]);
        }
    }
}
