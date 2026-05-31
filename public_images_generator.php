<?php
// Cria a pasta se não existir
if (!is_dir(__DIR__ . '/public/images')) {
    mkdir(__DIR__ . '/public/images', 0777, true);
}

// 1. Gera og-default.png (1200x630)
$im1 = imagecreatetruecolor(1200, 630);
$bg1 = imagecolorallocate($im1, 15, 15, 26); // #0F0F1A
imagefill($im1, 0, 0, $bg1);
$purple = imagecolorallocate($im1, 108, 63, 197); // #6C3FC5
imagefilledrectangle($im1, 0, 0, 1200, 20, $purple);
// Adiciona um texto simples
$white = imagecolorallocate($im1, 232, 232, 240);
imagestring($im1, 5, 500, 300, "WhatsGrupos - Diretorio de Grupos", $white);
imagepng($im1, __DIR__ . '/public/images/og-default.png');
imagedestroy($im1);

// 2. Gera icon-192.png (192x192)
$im2 = imagecreatetruecolor(192, 192);
$bg2 = imagecolorallocate($im2, 15, 15, 26);
imagefill($im2, 0, 0, $bg2);
$green = imagecolorallocate($im2, 0, 200, 150); // #00C896
// Desenha um círculo no meio
imagefilledellipse($im2, 96, 96, 120, 120, $green);
imagepng($im2, __DIR__ . '/public/images/icon-192.png');
imagedestroy($im2);

// 3. Gera icon-512.png (512x512)
$im3 = imagecreatetruecolor(512, 512);
$bg3 = imagecolorallocate($im3, 15, 15, 26);
imagefill($im3, 0, 0, $bg3);
$green = imagecolorallocate($im3, 0, 200, 150); // #00C896
imagefilledellipse($im3, 256, 256, 320, 320, $green);
imagepng($im3, __DIR__ . '/public/images/icon-512.png');
imagedestroy($im3);

echo "Placeholder images generated successfully!\n";
