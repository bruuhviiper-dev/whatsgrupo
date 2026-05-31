<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller de autenticação simples do painel de administração.
 * Usa sessão sem o sistema de Auth do Laravel, comparando com dados do .env.
 */
class AdminAuthController extends Controller
{
    // -------------------------------------------------------------------------
    // Login do Admin
    // -------------------------------------------------------------------------

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Informe o e-mail de administrador.',
            'password.required' => 'Informe a senha.',
        ]);

        $envEmail    = config('app.admin_email', env('ADMIN_EMAIL'));
        $envPassword = config('app.admin_password', env('ADMIN_PASSWORD'));

        $adminEmail = Setting::get('admin_email', $envEmail);
        $adminPasswordHash = Setting::get('admin_password');

        $isValid = false;

        if ($request->email === $adminEmail) {
            if ($adminPasswordHash) {
                $isValid = Hash::check($request->password, $adminPasswordHash);
            } else {
                $isValid = ($request->password === $envPassword);
            }
        }

        // Comparaǜo para logar o admin
        if ($isValid) {
            // Registra a sessǜo de autenticaǜo do admin
            $request->session()->put('admin_logged', true);
            $request->session()->put('admin_email', $request->email);
            
            // Carrega outras preferências
            $request->session()->put('admin_name', Setting::get('admin_name', 'Administrador'));
            $request->session()->put('admin_theme', Setting::get('admin_theme', 'light'));
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Bem-vindo ao painel de administração!');
        }

        return back()->with('error', 'Credenciais incorretas. Verifique e-mail e senha.')->withInput();
    }

    // -------------------------------------------------------------------------
    // Logout do Admin
    // -------------------------------------------------------------------------

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_logged', 'admin_email', 'admin_name', 'admin_theme', 'my_groups_email']);
        $request->session()->regenerate();

        return redirect()->route('admin.login.form')
            ->with('success', 'Sessão encerrada com sucesso.')
            ->withoutCookie('submitted_groups');
    }
}
