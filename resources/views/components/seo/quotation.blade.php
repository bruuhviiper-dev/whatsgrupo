@props(['phrase', 'categoryName' => null])
{{--
  Quotation JSON-LD para uma frase de status individual.
  schema.org/Quotation — ideal para rich results de citações.
--}}
@php
    $siteUrl = rtrim(url('/'), '/');
    $url = route('phrases.show', $phrase);
    $data = [
        '@context'   => 'https://schema.org',
        '@type'      => 'Quotation',
        'text'       => $phrase->phrase,
        'url'        => $url,
        'inLanguage' => 'pt-BR',
        'isPartOf'   => ['@id' => $siteUrl . '/#website'],
    ];
    if ($categoryName) {
        $data['about'] = ['@type' => 'Thing', 'name' => $categoryName];
    }
    if (!empty($phrase->author)) {
        $data['creator'] = ['@type' => 'Person', 'name' => $phrase->author];
    }
@endphp
<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
