@props(['name', 'description' => '', 'url' => null])
{{--
  Structured data para páginas de ferramentas: WebApplication (ferramenta gratuita
  baseada no navegador) + BreadcrumbList (Início › Ferramentas › ferramenta).
--}}
@php
    $siteUrl = rtrim(url('/'), '/');
    $u = $url ?: url()->current();
    $appData = [
        '@context'            => 'https://schema.org',
        '@type'               => 'WebApplication',
        'name'                => $name,
        'url'                 => $u,
        'description'         => $description,
        'applicationCategory' => 'UtilitiesApplication',
        'operatingSystem'     => 'Web',
        'inLanguage'          => 'pt-BR',
        'isAccessibleForFree' => true,
        'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'BRL'],
        'isPartOf'            => ['@id' => $siteUrl . '/#website'],
        'publisher'           => ['@id' => $siteUrl . '/#organization'],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($appData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<x-seo.breadcrumbs :items="[
    ['name' => 'Início', 'url' => $siteUrl . '/'],
    ['name' => $name, 'url' => $u],
]" />
