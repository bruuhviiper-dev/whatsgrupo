<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($groups as $group)
    <url>
        <loc>{{ htmlspecialchars($baseUrl . '/grupo/' . $group->slug) }}</loc>
        <lastmod>{{ $group->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
