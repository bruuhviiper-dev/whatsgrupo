<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclui as rotas de webhook de pagamentos externos da validação de CSRF
        $middleware->validateCsrfTokens(except: [
            '/webhook/efi',
            '/webhook/stripe',
            '/webhook/asaas',
        ]);

        // Adiciona cabeçalhos de segurança globais
        $middleware->append(\App\Http\Middleware\AddSecurityHeaders::class);

        // Registra o middleware de autenticação do painel de administração
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
