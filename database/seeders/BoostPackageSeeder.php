<?php

namespace Database\Seeders;

use App\Models\BoostPackage;
use Illuminate\Database\Seeder;

class BoostPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'boosts_count' => 3,
                'price' => 14.90,
                'original_price' => 14.90,
                'savings_percent' => 0,
                'duration_hours' => 12,
                'is_popular' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Prata',
                'slug' => 'prata',
                'boosts_count' => 7,
                'price' => 29.90,
                'original_price' => 34.93,
                'savings_percent' => 14,
                'duration_hours' => 12,
                'is_popular' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Ouro',
                'slug' => 'ouro',
                'boosts_count' => 15,
                'price' => 54.90,
                'original_price' => 74.50,
                'savings_percent' => 26,
                'duration_hours' => 12,
                'is_popular' => true, // Pacote em destaque
                'is_active' => true,
            ],
            [
                'name' => 'Diamante',
                'slug' => 'diamante',
                'boosts_count' => 30,
                'price' => 89.90,
                'original_price' => 149.00,
                'savings_percent' => 40,
                'duration_hours' => 12,
                'is_popular' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Estrela',
                'slug' => 'estrela',
                'boosts_count' => 60,
                'price' => 149.90,
                'original_price' => 298.00,
                'savings_percent' => 50,
                'duration_hours' => 12,
                'is_popular' => false,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            BoostPackage::updateOrCreate(
                ['slug' => $pkg['slug']],
                $pkg
            );
        }
    }
}
