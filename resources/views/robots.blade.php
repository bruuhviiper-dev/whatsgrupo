# Bots legítimos: indexação completa das páginas públicas
User-agent: Googlebot
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /g/
Disallow: /r/

User-agent: Bingbot
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /g/
Disallow: /r/

# Scrapers e bots de coleta conhecidos: bloqueio total
User-agent: SemrushBot
Disallow: /

User-agent: AhrefsBot
Disallow: /

User-agent: MJ12bot
Disallow: /

User-agent: DotBot
Disallow: /

User-agent: PetalBot
Disallow: /

User-agent: SeznamBot
Disallow: /

User-agent: Bytespider
Disallow: /

User-agent: GPTBot
Disallow: /

User-agent: CCBot
Disallow: /

User-agent: DataForSeoBot
Disallow: /

User-agent: ia_archiver
Disallow: /

User-agent: Scrapy
Disallow: /

# Todos os outros bots: páginas públicas liberadas, rotas sensíveis bloqueadas
User-agent: *
Allow: /

Disallow: /admin/
Disallow: /api/
Disallow: /pagamento/
Disallow: /meus-grupos
Disallow: /g/
Disallow: /r/
Disallow: /buscar
Disallow: /enviar-grupo

Sitemap: {{ config('app.url') }}/sitemap.xml
