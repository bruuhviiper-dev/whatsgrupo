<?php
$app = file_get_contents('resources/views/layouts/app.blade.php');
$startTag = '  <!-- Off-Canvas Menu Sidebar (Mobile & Desktop Sandwich) -->';
$endTag = '  <!-- MOBILE APP-LIKE BOTTOM NAVIGATION (Apenas Mobile) -->';

$startPos = strpos($app, $startTag);
$endPos = strpos($app, $endTag);
$offcanvasHtml = substr($app, $startPos, $endPos - $startPos);

$layouts = ['phrases.blade.php', 'figurinhas.blade.php', 'analyzer.blade.php'];
foreach ($layouts as $layout) {
    $file = 'resources/views/layouts/' . $layout;
    $content = file_get_contents($file);
    
    $sPos = strpos($content, $startTag);
    $ePos = strpos($content, $endTag);
    
    if ($sPos !== false && $ePos !== false) {
        $newContent = substr($content, 0, $sPos) . $offcanvasHtml . substr($content, $ePos);
        file_put_contents($file, $newContent);
        echo "Updated $layout\n";
    } else {
        echo "Tags not found in $layout\n";
    }
}
