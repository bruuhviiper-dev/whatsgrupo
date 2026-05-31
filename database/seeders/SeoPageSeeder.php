<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SeoPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder para gerar as páginas de SEO de cauda longa.
 * Combina categorias com estados e termos extras populares.
 */
class SeoPageSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa registros anteriores para evitar duplicidade de slug
        SeoPage::truncate();

        // Estados brasileiros
        $states = [
            'Acre', 'Alagoas', 'Amapá', 'Amazonas', 'Bahia', 'Ceará',
            'Distrito Federal', 'Espírito Santo', 'Goiás', 'Maranhão',
            'Mato Grosso', 'Mato Grosso do Sul', 'Minas Gerais', 'Pará',
            'Paraíba', 'Paraná', 'Pernambuco', 'Piauí', 'Rio de Janeiro',
            'Rio Grande do Norte', 'Rio Grande do Sul', 'Rondônia', 'Roraima',
            'Santa Catarina', 'São Paulo', 'Sergipe', 'Tocantins'
        ];

        // Termos extras populares
        $extraTerms = [
            'gratuito', '2025', 'ativo', 'novo', 'aberto',
            'para iniciantes', 'brasileiro', 'atualizado'
        ];

        $categories = Category::all();

        if ($categories->isEmpty()) {
            return;
        }

        $pages = [];
        $count = 0;

        // 1. COMBINAÇÃO: Categoria x Estado
        foreach ($categories as $category) {
            foreach ($states as $state) {
                // Previne estourar 2.000 se houver excesso por algum motivo
                if ($count >= 2000) {
                    break 2;
                }

                $catSlug = Str::slug($category->name);
                $stateSlug = Str::slug($state);
                $slug = "grupos-whatsapp-{$catSlug}-{$stateSlug}";

                // Keyword em minúsculas
                $keyword = "grupos whatsapp " . mb_strtolower($category->name) . " " . mb_strtolower($state);

                $title = "Grupos de WhatsApp de {$category->name} em {$state} | WhatsGrupos";
                $h1 = "Grupos de WhatsApp de {$category->name} em {$state}";
                $metaDescription = Str::limit("Encontre os melhores grupos de WhatsApp de {$category->name} em {$state}. Lista atualizada com links verificados e ativos para você entrar agora.", 155);

                $content = "Encontre os melhores grupos de WhatsApp de {$category->name} em {$state}. Nossa lista é atualizada diariamente com grupos ativos e verificados. Entre agora e faça parte da maior comunidade de {$category->name} do Brasil.";

                $pages[] = [
                    'title'            => $title,
                    'slug'             => $slug,
                    'h1'               => $h1,
                    'meta_description' => $metaDescription,
                    'category_id'      => $category->id,
                    'keyword'          => $keyword,
                    'state'            => $state,
                    'city'             => null,
                    'extra_term'       => null,
                    'content'          => $content,
                    'is_active'        => true,
                    'views'            => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];

                $count++;
            }
        }

        // 2. COMBINAÇÃO: Categoria x Termo Extra
        foreach ($categories as $category) {
            foreach ($extraTerms as $term) {
                if ($count >= 2000) {
                    break 2;
                }

                $catSlug = Str::slug($category->name);
                $termSlug = Str::slug($term);
                $slug = "grupos-whatsapp-{$catSlug}-{$termSlug}";

                $keyword = "grupos whatsapp " . mb_strtolower($category->name) . " " . mb_strtolower($term);

                $title = "Grupos de WhatsApp de {$category->name} {$term} | WhatsGrupos";
                $h1 = "Grupos de WhatsApp de {$category->name} " . Str::ucfirst($term);
                $metaDescription = Str::limit("Confira os grupos de WhatsApp de {$category->name} {$term} mais populares do Brasil. Encontre links ativos e verificados para entrar hoje.", 155);

                $content = "Encontre os melhores grupos de WhatsApp de {$category->name} {$term}. Nossa lista é atualizada diariamente com grupos ativos e verificados. Entre agora e faça parte da maior comunidade de {$category->name} do Brasil.";

                $pages[] = [
                    'title'            => $title,
                    'slug'             => $slug,
                    'h1'               => $h1,
                    'meta_description' => $metaDescription,
                    'category_id'      => $category->id,
                    'keyword'          => $keyword,
                    'state'            => null,
                    'city'             => null,
                    'extra_term'       => $term,
                    'content'          => $content,
                    'is_active'        => true,
                    'views'            => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];

                $count++;
            }
        }

        // Insere em blocos de 100 para alta performance no SQLite
        foreach (array_chunk($pages, 100) as $chunk) {
            SeoPage::insert($chunk);
        }
    }
}
