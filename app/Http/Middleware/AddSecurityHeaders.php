<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Adiciona headers de segurança apenas se for uma resposta HTTP padrão
        if (method_exists($response, 'header')) {
            // Evita clickjacking apenas para páginas normais, permitindo widget externo
            if (!$request->is('widget*')) {
                $response->header('X-Frame-Options', 'SAMEORIGIN');
            }

            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('Referrer-Policy', 'no-referrer-when-downgrade');
            
            // Content Security Policy flexível o suficiente para CDNs autorizados (Tailwind, Alpine, Chart.js, Google Fonts, Stripe)
            $response->header('Content-Security-Policy', "default-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://fonts.googleapis.com https://fonts.gstatic.com https://js.stripe.com https://api.stripe.com; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com; frame-src 'self' https:;");
        }

        return $response;
    }
}
