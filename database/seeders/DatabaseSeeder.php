<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Ordem de prioridade:
     *  1. CategorySeeder        — categorias devem existir antes de grupos e SEO
     *  2. BoostPackageSeeder    — pacotes de boost independentes
     *  3. SeoPageSeeder         — páginas SEO referenciam categorias
     *  4. StatusPhrasesSeeder   — frases de status independentes
     *  5. BlogCategorySeeder    — categorias de blog antes dos posts
     *  6. BlogPostSeeder        — posts referenciam blog_categories
     *  7. MoreBlogPostsSeeder   — posts adicionais
     *  8. NewPhrasesSeeder      — frases adicionais
     *  9. MorePhrasesSeeder     — mais frases
     * 10. FigurinhasSeeder      — figurinhas independentes
     * 11. SpecialSeoPagesSeeder — páginas SEO especiais
     * 12. SeoPages2026Seeder    — atualiza páginas para 2026 (desativa 2025)
     * 13. GroupSeeder           — grupos referenciam categorias (por último)
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            BoostPackageSeeder::class,
            SeoPageSeeder::class,
            StatusPhrasesSeeder::class,
            BlogCategorySeeder::class,
            BlogPostSeeder::class,
            MoreBlogPostsSeeder::class,
            NewPhrasesSeeder::class,
            MorePhrasesSeeder::class,
            FigurinhasSeeder::class,
            SpecialSeoPagesSeeder::class,
            SeoPages2026Seeder::class,
            // GroupSeeder::class, // desativado: criava grupos fake com links vanity
            // (ex.: chat.whatsapp.com/DevsFullstackBrasil) que NÃO resolvem no WhatsApp.
            // Agora o diretório é populado APENAS por grupos reais vindos do coletor.
        ]);
    }
}
