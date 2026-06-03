<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            '/webhook/mercadopago',
        ]);

        // Adiciona cabeçalhos de segurança globais
        $middleware->append(\App\Http\Middleware\AddSecurityHeaders::class);

        // Proteção anti-scraper: honeypot + ban por IP + UA suspeito + rate limit no /g/
        $middleware->append(\App\Http\Middleware\BotProtection::class);

        // Registra o middleware de autenticação do painel de administração
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Em produção (APP_DEBUG=false) nunca exibe stack trace, queries SQL
        // nem detalhes internos do Laravel ao usuário. Tudo é logado no lado
        // do servidor; o usuário vê apenas uma página de erro limpa.
        $exceptions->render(function (\Throwable $e, Request $request) {
            // Rotas de API retornam JSON sem detalhes internos
            if ($request->expectsJson() || $request->is('api/*')) {
                $status = $e instanceof HttpException ? $e->getStatusCode() : 500;
                return response()->json([
                    'error'   => 'Ocorreu um erro inesperado.',
                    'message' => $status === 404 ? 'Recurso não encontrado.' : 'Tente novamente em instantes.',
                ], $status);
            }

            // Em desenvolvimento mostra o debugger padrão do Laravel
            if (config('app.debug')) {
                return null;
            }

            // Em produção: mapeia para a view de erro correta sem vazar nenhum detalhe
            if ($e instanceof NotFoundHttpException) {
                return response()->view('errors.404', [], 404);
            }

            if ($e instanceof HttpException) {
                $code = $e->getStatusCode();
                $view = view()->exists("errors.{$code}") ? "errors.{$code}" : 'errors.500';
                return response()->view($view, [], $code);
            }

            return response()->view('errors.500', [], 500);
        });
    })->create();
