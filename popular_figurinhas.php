<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Figurinha;
use App\Enums\FigurinhaCategoria;
use App\Enums\FigurinhaStatus;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$memesResponse = file_get_contents('https://api.imgflip.com/get_memes');
$memesData = json_decode($memesResponse, true);
$memes = $memesData['data']['memes'] ?? [];

$gifsFamosos = [
    ['url' => 'https://media.tenor.com/images/8b4226d9e03091e4db95cda2a3a5df6a/tenor.gif', 'name' => 'Homer Simpson Arbusto'],
    ['url' => 'https://media.tenor.com/images/3a479ff76f18dd46b415a7ee466a9df5/tenor.gif', 'name' => 'Nazaré Confusa'],
    ['url' => 'https://media.tenor.com/images/f875b1c8c5dcce5ec4f0c978b27ebcf3/tenor.gif', 'name' => 'Michael Jackson Pipoca'],
    ['url' => 'https://media.tenor.com/images/4f4df7e37604aeb92461df28dd5f9392/tenor.gif', 'name' => 'This is Fine Dog'],
    ['url' => 'https://media.tenor.com/images/062db20b904d9c8c9ba4c08cd4a1591e/tenor.gif', 'name' => 'Gretchen Conga'],
    ['url' => 'https://media.tenor.com/images/051740fa18a99268ab7f41cf69c7f9fb/tenor.gif', 'name' => 'Travolta Confuso'],
    ['url' => 'https://media.tenor.com/images/737edce4c219159938814529f52f8be2/tenor.gif', 'name' => 'Pikachu Surpreso'],
    ['url' => 'https://media.tenor.com/images/4523c93da6cbb54d2dc1e6a75f850d51/tenor.gif', 'name' => 'Mônica Computador'],
    ['url' => 'https://media.tenor.com/images/6dfdfbc96ec219fdfb46f5ee0dc025fb/tenor.gif', 'name' => 'Chitãozinho e Xororó Evidências'],
    ['url' => 'https://media.tenor.com/images/87b4f5353fffc7f0b5fc912dd517178a/tenor.gif', 'name' => 'Faustão Errou'],
];

Storage::disk('public')->makeDirectory('figurinhas');

echo "Inserindo GIFs Famosos...\n";
foreach ($gifsFamosos as $gif) {
    $titulo = "GIF " . $gif['name'];
    $slug = Str::slug($titulo) . '-' . uniqid();
    $content = @file_get_contents($gif['url']);
    if ($content) {
        $filename = "figurinhas/{$slug}.gif";
        Storage::disk('public')->put($filename, $content);
        Figurinha::create([
            'user_id' => 1,
            'titulo' => $titulo,
            'slug' => $slug,
            'categoria' => FigurinhaCategoria::Engracado,
            'status' => FigurinhaStatus::Aprovado,
            'arquivo_path' => $filename,
            'arquivo_original' => "{$slug}.gif",
            'tags' => ['gif', 'famoso', 'whatsapp'],
            'downloads' => rand(1000, 5000),
            'visualizacoes' => rand(5000, 20000),
            'aprovado_em' => now(),
            'ip_envio' => '127.0.0.1'
        ]);
        echo "Cadastrado GIF: $titulo\n";
    }
}

echo "Inserindo Memes Famosos (Imgflip)...\n";
$categorias = FigurinhaCategoria::cases();
$count = 0;
foreach ($memes as $meme) {
    if ($count >= 50) break; // Limit to 50 memes
    $titulo = "Meme " . $meme['name'];
    $slug = Str::slug($titulo) . '-' . uniqid();
    $content = @file_get_contents($meme['url']);
    if ($content) {
        $ext = pathinfo($meme['url'], PATHINFO_EXTENSION) ?: 'jpg';
        $filename = "figurinhas/{$slug}.{$ext}";
        Storage::disk('public')->put($filename, $content);
        
        $cat = $categorias[array_rand($categorias)];
        
        Figurinha::create([
            'user_id' => 1,
            'titulo' => $titulo,
            'slug' => $slug,
            'categoria' => $cat,
            'status' => FigurinhaStatus::Aprovado,
            'arquivo_path' => $filename,
            'arquivo_original' => "{$slug}.{$ext}",
            'tags' => ['meme', 'famoso', 'whatsapp'],
            'downloads' => rand(500, 5000),
            'visualizacoes' => rand(2000, 15000),
            'aprovado_em' => now(),
            'ip_envio' => '127.0.0.1'
        ]);
        echo "Cadastrado Meme: $titulo\n";
        $count++;
    }
}
echo "Concluído!\n";
