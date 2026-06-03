<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * BotProtection — blindagem anti-scraper sem impacto em SEO ou funcionalidade.
 *
 * Camadas ativas:
 *  1. Honeypot: se o IP acessar /hp (link invisível no HTML), é banido por 24h.
 *  2. IP banido: rejeita imediatamente qualquer request de IPs no honeypot-ban.
 *  3. User-Agent suspeito: bloqueia scrapers e UAs de bots conhecidos.
 *  4. Rate limit na rota de click (/g/{id}/entrar): 60 req/hora por IP.
 *
 * Googlebot, Bingbot e outros crawlers legítimos passam — têm UAs próprios
 * e não seguem links marcados nofollow nem links hidden do honeypot.
 * O painel admin (/admin) é excluído — tem autenticação própria.
 */
class BotProtection
{
    /** UAs de scrapers/coletores conhecidos (lowercase, partial match). */
    private const BLOCKED_UAS = [
        'python-requests', 'scrapy', 'curl/', 'wget/', 'go-http-client',
        'java/', 'axios/', 'node-fetch', 'got/', 'httpx', 'aiohttp',
        'cloudscraper', 'mechanize', 'beautifulsoup', 'headless',
        'phantomjs', 'htmlunit', 'libwww-perl', 'lwp-', 'pycurl',
        'crawler', 'spider', 'scraper', 'dataprovider',
        'semrushbot', 'ahrefsbot', 'mj12bot', 'dotbot', 'petalbot',
        'bytespider', 'gptbot', 'ccbot', 'dataforseobot',
    ];

    /** UAs de bots legítimos que nunca devem ser bloqueados. */
    private const ALLOWED_UAS = [
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
        'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
        'twitterbot', 'linkedinbot', 'whatsapp', 'telegrambot',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $ua = strtolower($request->userAgent() ?? '');

        // Admin tem autenticação própria — não aplica as camadas de bot.
        if ($request->is('admin*')) {
            return $next($request);
        }

        // ── 1. Honeypot: registra acesso e bane o IP por 24h ─────────────────
        if ($request->is('hp')) {
            // Em desenvolvimento (localhost) não bane para não bloquear testes.
            if (! in_array($ip, ['127.0.0.1', '::1'])) {
                Cache::put("bot_ban:{$ip}", true, now()->addHours(24));
            }
            abort(404);
        }

        // ── 2. IP banido pelo honeypot ────────────────────────────────────────
        if (Cache::has("bot_ban:{$ip}")) {
            abort(403, 'Acesso bloqueado.');
        }

        // ── 3. User-Agent suspeito ────────────────────────────────────────────
        // UA vazio = scraper sem identificação.
        if (empty($ua)) {
            abort(403, 'Acesso bloqueado.');
        }

        // Bots legítimos (Googlebot, Bingbot etc.) sempre passam.
        foreach (self::ALLOWED_UAS as $allowed) {
            if (str_contains($ua, $allowed)) {
                return $next($request);
            }
        }

        // Bloqueia UAs de scrapers/coletores conhecidos.
        foreach (self::BLOCKED_UAS as $blocked) {
            if (str_contains($ua, $blocked)) {
                abort(403, 'Acesso bloqueado.');
            }
        }

        // ── 4. Rate limit na rota de click (/g/{id}/entrar) ───────────────
        // 60 clicks/hora por IP — suficiente para qualquer usuário humano,
        // proibitivo para um scraper que tenta coletar centenas de links.
        if ($request->is('g/*/entrar')) {
            $key = "click_limit:{$ip}";
            if (RateLimiter::tooManyAttempts($key, 60)) {
                $wait = RateLimiter::availableIn($key);
                abort(429, "Muitas requisições. Aguarde {$wait} segundos.");
            }
            RateLimiter::hit($key, 3600);
        }

        return $next($request);
    }
}
