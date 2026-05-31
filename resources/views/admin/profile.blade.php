@extends('admin.layout')

@section('title', 'Admin > Perfil e Preferências')

@section('content')
<div class="px-4 py-6 max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Meu Perfil</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gerencie suas informações de acesso e preferências do sistema.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl mb-6 font-medium text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Seção: Informações Básicas -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4">Informações Básicas</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nome de Exibição</label>
                    <input type="text" name="name" value="{{ old('name', $name) }}" required
                           class="w-full rounded-xl px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:text-white transition-all">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">E-mail de Login</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required
                           class="w-full rounded-xl px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:text-white transition-all">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Seção: Alterar Senha -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Alterar Senha</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Deixe em branco se não quiser alterar a senha atual.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nova Senha</label>
                    <input type="password" name="password" placeholder="••••••••"
                           class="w-full rounded-xl px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:text-white transition-all">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••"
                           class="w-full rounded-xl px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:text-white transition-all">
                </div>
            </div>
        </div>

        <!-- Seção: Preferências do Sistema -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4">Preferências do Sistema</h2>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tema do Painel</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="theme" value="light" {{ old('theme', $theme) === 'light' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Light Mode ☀️</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="theme" value="dark" {{ old('theme', $theme) === 'dark' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 bg-slate-800">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Dark Mode 🌙</span>
                    </label>
                </div>
                @error('theme')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Botão Salvar -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl shadow-sm shadow-emerald-500/20 transition-all flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection
