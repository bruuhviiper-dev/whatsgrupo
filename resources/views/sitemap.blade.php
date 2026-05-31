<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- URLs estáticas --}}
    @foreach ($staticUrls as $url)
    <url>
        <loc>{{ htmlspecialchars($url['loc']) }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        <priority>{{ $url['priority'] }}</priority>
    </url>
    @endforeach

    {{-- Categorias --}}
    @foreach ($categories as $category)
    <url>
        <loc>{{ htmlspecialchars($baseUrl . '/categoria/' . $category->slug) }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Grupos aprovados --}}
    @foreach ($groups as $group)
    <url>
        <loc>{{ htmlspecialchars($baseUrl . '/grupo/' . $group->slug) }}</loc>
        <lastmod>{{ $group->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

</urlset>
