@props(['group'])

@php
    $siteUrl = rtrim(url('/'), '/');
    $groupUrl = route('group.show', $group->slug ?: $group->id);
    $img = $group->image_url ?? asset('images/og-default.png');

    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'WebPage',
        '@id'              => $groupUrl . '#webpage',
        'url'              => $groupUrl,
        'name'             => html_entity_decode($group->name),
        'description'      => Str::limit(strip_tags($group->description), 200),
        'inLanguage'       => 'pt-BR',
        'datePublished'    => $group->created_at->toIso8601String(),
        'dateModified'     => $group->updated_at->toIso8601String(),
        'isPartOf'         => ['@id' => $siteUrl . '/#website'],
        'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $img],
        'image'            => $img,
        'about'            => [
            '@type' => 'Thing',
            'name'  => $group->category->name ?? 'Grupos de WhatsApp',
        ],
        'publisher'        => ['@id' => $siteUrl . '/#organization'],
    ];
@endphp

@push('schema')
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
