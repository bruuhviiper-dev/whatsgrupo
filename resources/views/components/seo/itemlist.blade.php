@props(['name' => '', 'items' => []])
{{--
  ItemList JSON-LD genérico. items = [['name' => '...', 'url' => '...'], ...].
  Reutilizável para qualquer listagem (frases, posts, etc.).
--}}
@php
    $elements = [];
    $pos = 1;
    foreach ($items as $it) {
        $nm = trim($it['name'] ?? '');
        if ($nm === '') { continue; }
        $el = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $nm];
        if (!empty($it['url'])) { $el['url'] = $it['url']; }
        $elements[] = $el;
    }
@endphp
@if(count($elements))
<script type="application/ld+json">
{!! json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => $name,
    'numberOfItems'   => count($elements),
    'itemListElement' => $elements,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
