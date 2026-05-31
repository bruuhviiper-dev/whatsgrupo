<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin — WhatsGrupos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-slate-50">

    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8 flex flex-col items-center">
            <div class="w-16 h-16 bg-[#25D366] rounded-2xl flex items-center justify-center mb-4 shadow-sm shadow-green-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">WhatsGrupos</h1>
            <p class="text-slate-500 text-sm mt-1 font-medium">Painel de Administração</p>
        </div>

        {{-- Card de login --}}
        <div class="rounded-2xl border border-slate-200 p-8 bg-white shadow-sm">

            {{-- Alertas --}}
            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- E-mail --}}
                <div>
                    <label for="admin-email" class="block text-slate-700 text-sm font-bold mb-2">E-mail</label>
                    <input type="email"
                           id="admin-email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="email"
                           placeholder="admin@whatsgrupos.com"
                           class="w-full rounded-xl px-4 py-3 text-sm text-slate-900 border border-slate-300 focus:border-[#25D366] focus:ring-1 focus:ring-[#25D366] outline-none transition-all bg-slate-50">
                    @error('email')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                {{-- Senha --}}
                <div>
                    <label for="admin-password" class="block text-slate-700 text-sm font-bold mb-2">Senha</label>
                    <input type="password"
                           id="admin-password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full rounded-xl px-4 py-3 text-sm text-slate-900 border border-slate-300 focus:border-[#25D366] focus:ring-1 focus:ring-[#25D366] outline-none transition-all bg-slate-50">
                    @error('password')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                        id="btn-admin-login"
                        class="w-full py-3.5 rounded-xl font-black text-white transition-all bg-[#25D366] hover:bg-[#20bd5a] shadow-sm flex items-center justify-center gap-2">
                    Entrar no Painel
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 text-xs mt-6 font-semibold">
            <a href="{{ route('home') }}" class="hover:text-slate-900 transition-colors inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Voltar ao site
            </a>
        </p>
    </div>

</body>
</html>
