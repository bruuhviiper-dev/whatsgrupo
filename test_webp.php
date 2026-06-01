<?php
// Teste simples de conversão WebP

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

echo "=== TESTE DE CONVERSÃO WEBP ===\n\n";

// 1. Simular download de imagem
$testImageUrl = 'https://static.whatsapp.net/rsrc.php/v4/yO/r/rukeqTVNJDY.png';
echo "Baixando imagem: $testImageUrl\n";

try {
    $response = \Illuminate\Support\Facades\Http::timeout(15)
        ->withHeaders(['User-Agent' => 'facebookexternalhit/1.1'])
        ->get($testImageUrl);
    
    if ($response->successful() && strlen($response->body()) > 100) {
        echo "✓ Imagem baixada: " . strlen($response->body()) . " bytes\n\n";
        
        // 2. Converter para WebP 400x400
        echo "Convertendo para WebP 400x400 com qualidade 85...\n";
        $img = Image::make($response->body())
            ->fit(400, 400)
            ->encode('webp', 85);
        
        echo "✓ Convertido com sucesso\n";
        echo "✓ Tipo MIME: " . $img->mime() . "\n";
        echo "✓ Tamanho: " . strlen($img->getEncoded()) . " bytes\n\n";
        
        // 3. Salvar no storage
        $filename = 'groups/test_' . uniqid('grp_', true) . '.webp';
        echo "Salvando em: $filename\n";
        
        Storage::disk('public')->put($filename, $img->getEncoded());
        
        echo "✓ Arquivo salvo com sucesso!\n";
        echo "✓ URL pública: " . asset("storage/$filename") . "\n\n";
        
        // 4. Verificar arquivo
        $fullPath = storage_path("app/public/$filename");
        if (file_exists($fullPath)) {
            $fileSize = filesize($fullPath);
            echo "✓ Verificação: Arquivo existe ($fileSize bytes)\n";
        }
        
    } else {
        echo "✗ Falha ao baixar imagem\n";
    }
} catch (\Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
