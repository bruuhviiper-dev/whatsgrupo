<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executa os seeders na ordem correta
        $this->call([
            CategorySeeder::class,
            BoostPackageSeeder::class,
            SeoPageSeeder::class,
            StatusPhrasesSeeder::class,
            GroupSeeder::class,
        ]);
    }
}
