<?php
$app = file_get_contents('resources/views/layouts/app.blade.php');
$startTag = '  <!-- Off-Canvas Menu Sidebar (Mobile & Desktop Sandwich) -->';
$endTag = '  <!-- MOBILE APP-LIKE BOTTOM NAVIGATION (Apenas Mobile) -->';

$startPos = strpos($app, $startTag);
$endPos = strpos($app, $endTag);
$offcanvasHtml = substr($app, $startPos, $endPos - $startPos);

// Modify the offcanvas to include the new tool
$searchFerramentas = '<a href="/ferramentas/analise-de-engajamento" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100 border border-green-200/70 transition-colors">
                      <x-heroicon-s-sparkles class="w-4 h-4 flex-shrink-0 text-green-500"/>
                      Análise de Engajamento
                      <span class="ml-auto bg-green-500 text-white text-[9px] uppercase tracking-widest px-1.5 py-0.5 rounded-full font-black flex-shrink-0">Novo</span>
                    </a>';

$newFerramenta = $searchFerramentas . '
                    <a href="/ferramentas/gerador-de-regras" class="mt-2 flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm text-blue-700 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 border border-blue-200/70 transition-colors">
                      <x-heroicon-s-document-check class="w-4 h-4 flex-shrink-0 text-blue-500"/>
                      Gerador de Regras
                      <span class="ml-auto bg-blue-500 text-white text-[9px] uppercase tracking-widest px-1.5 py-0.5 rounded-full font-black flex-shrink-0">Novo</span>
                    </a>';

$offcanvasHtml = str_replace($searchFerramentas, $newFerramenta, $offcanvasHtml);

// Save to component
file_put_contents('resources/views/components/offcanvas.blade.php', $offcanvasHtml);

// Replace in layouts
$layouts = ['app.blade.php', 'phrases.blade.php', 'figurinhas.blade.php', 'analyzer.blade.php'];
foreach ($layouts as $layout) {
    $file = 'resources/views/layouts/' . $layout;
    $content = file_get_contents($file);
    
    $sPos = strpos($content, $startTag);
    $ePos = strpos($content, $endTag);
    
    if ($sPos !== false && $ePos !== false) {
        $newContent = substr($content, 0, $sPos) . '  <x-offcanvas />' . "\n\n" . substr($content, $ePos);
        file_put_contents($file, $newContent);
        echo "Replaced in $layout\n";
    }
}
