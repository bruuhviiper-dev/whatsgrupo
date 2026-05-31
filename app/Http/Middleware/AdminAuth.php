<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de autenticação simples do painel de administração.
 * Verifica se a sessão 'admin_logged' está definida como true.
 * Redireciona para /admin/login se o admin não estiver autenticado.
 */
class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('admin_logged', false)) {
            return redirect()->route('admin.login.form')
                ->with('error', 'Você precisa fazer login para acessar o painel.');
        }

        return $next($request);
    }
}
