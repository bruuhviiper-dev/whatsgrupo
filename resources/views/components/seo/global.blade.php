{{--
  JSON-LD global (Organization + WebSite + SearchAction) — injetado em TODAS as páginas.
  Dá ao Google a entidade da marca e a caixa de busca de sitelinks (Sitelinks Searchbox),
  acelerando o reconhecimento e o ranqueamento. O concorrente não possui structured data.
--}}
@php
    $siteUrl  = rtrim(url('/'), '/');
    $siteName = 'WhatsGrupos';
    $logoUrl  = asset('favicon.svg');
    $ogImage  = asset('images/og-default.png');

    $graph = [
        [
            '@type'  => 'Organization',
            '@id'    => $siteUrl . '/#organization',
            'name'   => $siteName,
            'url'    => $siteUrl . '/',
            'logo'   => [
                '@type' => 'ImageObject',
                'url'   => $logoUrl,
            ],
            'image'       => $ogImage,
            'description' => 'O maior diretório independente de grupos e canais de WhatsApp do Brasil, com grupos ativos organizados por categoria.',
            'sameAs'      => [],
        ],
        [
            '@type'           => 'WebSite',
            '@id'             => $siteUrl . '/#website',
            'url'             => $siteUrl . '/',
            'name'            => $siteName,
            'inLanguage'      => 'pt-BR',
            'publisher'       => ['@id' => $siteUrl . '/#organization'],
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => $siteUrl . '/buscar?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
