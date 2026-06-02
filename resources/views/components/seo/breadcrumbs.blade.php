@props(['items' => []])
{{--
  BreadcrumbList JSON-LD. Recebe items = [['name' => '...', 'url' => '...'], ...].
  Use em páginas internas (grupo, categoria, blog, SEO) para gerar breadcrumb rico
  nos resultados do Google.
--}}
@php
    $elements = [];
    foreach ($items as $i => $crumb) {
        $el = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $crumb['name'] ?? '',
        ];
        if (!empty($crumb['url'])) {
            $el['item'] = $crumb['url'];
        }
        $elements[] = $el;
    }
@endphp
@if(count($elements))
<script type="application/ld+json">
{!! json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => $elements,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
