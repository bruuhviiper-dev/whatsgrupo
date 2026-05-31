@props(['title', 'groups'])

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => htmlspecialchars_decode($title),
    'numberOfItems' => $groups->count(),
    'itemListElement' => $groups->map(function ($group, $index) {
        return [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'url' => route('group.show', $group->id),
            'name' => htmlspecialchars_decode($group->name),
        ];
    })->values()->all()
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

