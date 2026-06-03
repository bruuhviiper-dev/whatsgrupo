@props(['group'])

@php
    $siteUrl = rtrim(url('/'), '/');
    $groupUrl = route('group.show', $group->slug ?: $group->id);
    $img = $group->image_url ?? asset('images/og-default.png');

    // ItemPage é mais semântico que WebPage genérico para páginas de item de diretório.
    // mainEntity SocialMediaGroup dá ao Google contexto exato do conteúdo.
    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'ItemPage',
        '@id'              => $groupUrl . '#webpage',
        'url'              => $groupUrl,
        'name'             => html_entity_decode($group->name),
        'description'      => Str::limit(strip_tags($group->description), 200),
        'inLanguage'       => 'pt-BR',
        'datePublished'    => $group->created_at->toIso8601String(),
        'dateModified'     => $group->updated_at->toIso8601String(),
        'isPartOf'         => ['@id' => $siteUrl . '/#website'],
        'primaryImageOfPage' => $img ? ['@type' => 'ImageObject', 'url' => $img] : null,
        'image'            => $img ?: null,
        'publisher'        => ['@id' => $siteUrl . '/#organization'],
        // Entidade principal: o grupo de WhatsApp em si
        'mainEntity'       => [
            '@type'       => 'SocialMediaPosting',
            '@id'         => $groupUrl . '#group',
            'name'        => html_entity_decode($group->name),
            'description' => Str::limit(strip_tags($group->description), 200),
            'url'         => $groupUrl,
            'about'       => [
                '@type' => 'Thing',
                'name'  => $group->category->name ?? 'Grupos de WhatsApp',
            ],
            'locationCreated' => ['@type' => 'Place', 'name' => 'Brasil'],
        ],
        // Speakable: nome e descrição adequados para leitura em voz alta (Google Assistant)
        'speakable'        => [
            '@type'      => 'SpeakableSpecification',
            'cssSelector' => ['h1', '.group-description'],
        ],
        // AggregateRating: estrutura pronta para quando o sistema de avaliações for implementado.
        // Remova o comentário e preencha com dados reais quando houver reviews/votações.
        // 'aggregateRating' => [
        //     '@type'       => 'AggregateRating',
        //     'ratingValue' => '4.5',
        //     'reviewCount' => '10',
        //     'bestRating'  => '5',
        //     'worstRating' => '1',
        // ],
    ];
@endphp

@push('schema')
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
