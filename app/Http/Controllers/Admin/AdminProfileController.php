<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function edit()
    {
        $name  = Setting::get('admin_name', 'Administrador');
        $email = Setting::get('admin_email', env('ADMIN_EMAIL'));
        $theme = Setting::get('admin_theme', 'light');

        return view('admin.profile', compact('name', 'email', 'theme'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'theme' => 'required|in:light,dark',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password.min'       => 'A senha deve ter no mínimo 6 caracteres.',
        ]);

        Setting::set('admin_name', $request->name);
        Setting::set('admin_email', $request->email);
        Setting::set('admin_theme', $request->theme);

        if ($request->filled('password')) {
            Setting::set('admin_password', Hash::make($request->password));
        }

        // Atualiza a sessão atual
        $request->session()->put('admin_name', $request->name);
        $request->session()->put('admin_email', $request->email);
        $request->session()->put('admin_theme', $request->theme);

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}
