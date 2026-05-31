<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Figurinha;
use App\Enums\FigurinhaCategoria;
use App\Enums\FigurinhaStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FigurinhasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('figurinhas')->truncate();

        $files = Storage::disk('public')->files('figurinhas');
        $memeFiles = array_filter($files, function($file) {
            return str_contains($file, 'meme-') || str_contains($file, 'figurinha-');
        });

        if (empty($memeFiles)) {
            $this->command->info('Nenhum arquivo de figurinha encontrado.');
            return;
        }

        // Converter para array indexado
        $memeFiles = array_values($memeFiles);

        $categorias = FigurinhaCategoria::cases();
        
        foreach ($categorias as $categoria) {
            for ($i = 1; $i <= 5; $i++) {
                $randomFile = $memeFiles[array_rand($memeFiles)];
                $name = str_replace(['figurinhas/meme-', 'figurinhas/figurinha-', '.png', '.jpg'], '', $randomFile);
                $name = Str::title(str_replace('-', ' ', $name));

                Figurinha::create([
                    'titulo' => $name,
                    'slug' => Str::slug($name . '-' . uniqid()),
                    'arquivo_path' => $randomFile,
                    'arquivo_original' => $name . '.png',
                    'status' => FigurinhaStatus::Aprovado,
                    'categoria' => $categoria,
                    'downloads' => rand(100, 5000),
                    'user_id' => null,
                    'ip_envio' => '127.0.0.1',
                    'tags' => ['engracado', 'whatsapp', 'meme'],
                    'aprovado_em' => now(),
                ]);
            }
        }
    }
}
