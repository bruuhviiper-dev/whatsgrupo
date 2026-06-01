<!DOCTYPE html>
@php
    $adminTheme = session('admin_theme', \App\Models\Setting::get('admin_theme', 'light'));
@endphp
<html lang="pt-BR" class="{{ $adminTheme === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — WhatsGrupos</title>

    @php $dynamicFavicon = \App\Models\Setting::get('favicon'); @endphp
    @if($dynamicFavicon)
      <link rel="icon" href="{{ Storage::disk('public')->url($dynamicFavicon) }}">
      <link rel="apple-touch-icon" href="{{ Storage::disk('public')->url($dynamicFavicon) }}">
    @else
      <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
      <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    @endif

    {{-- IMPORTANTE: tailwind.config DEVE vir antes do CDN para darkMode:'class' ser lido na inicialização --}}
    <script>
      tailwind.config = {
        darkMode: 'class',
      }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
        .bg-body { background: #f1f5f9; }
        .dark .bg-body { background: #0f172a; }

        /* Sidebar */
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: #64748b; font-size: 13px; font-weight: 600;
            transition: all .15s; text-decoration: none; white-space: nowrap;
        }
        .dark .nav-link { color: #94a3b8; }
        .nav-link:hover { background: #f1f5f9; color: #0f172a; }
        .dark .nav-link:hover { background: rgba(255,255,255,.07); color: #e2e8f0; }
        .nav-link.active { background: rgba(37,211,102,.15); color: #16a34a; }
        .dark .nav-link.active { color: #4ade80; }
        .nav-link .icon { width:18px; height:18px; flex-shrink:0; }

        /* Tables */
        .data-table { width:100%; border-collapse:collapse; }
        .data-table thead tr { background:#f8fafc; }
        .dark .data-table thead tr { background:#1e293b; }
        .data-table th {
            padding: 11px 16px; font-size:10px; font-weight:800;
            text-transform:uppercase; letter-spacing:.8px; color:#94a3b8;
            border-bottom: 1px solid #e2e8f0; white-space:nowrap;
        }
        .dark .data-table th { border-bottom-color: #334155; color: #cbd5e1; }
        .data-table th:first-child { border-radius:0; }
        .data-table td {
            padding: 13px 16px; font-size:13px; color:#334155;
            border-bottom: 1px solid #f1f5f9; vertical-align: middle;
        }
        .dark .data-table td { color: #f8fafc; border-bottom-color: #1e293b; }
        .data-table tbody tr:hover td { background:#fafbfd; }
        .dark .data-table tbody tr:hover td { background:#0f172a; }
        .data-table tbody tr:last-child td { border-bottom:none; }

        /* Badges */
        .badge { display:inline-flex; align-items:center; gap:4px; border-radius:999px; padding:3px 10px; font-size:11px; font-weight:700; line-height:1.4; white-space:nowrap; }
        .badge-pending  { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
        .badge-approved { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .badge-rejected { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
        .badge-paid     { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .badge-failed   { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
        
        .dark .badge-pending  { background:#451a03 !important; color:#fde68a !important; border-color:#78350f !important; }
        .dark .badge-approved { background:#064e3b !important; color:#bbf7d0 !important; border-color:#065f46 !important; }
        .dark .badge-rejected { background:#7f1d1d !important; color:#fca5a5 !important; border-color:#991b1b !important; }
        .dark .badge-paid     { background:#064e3b !important; color:#bbf7d0 !important; border-color:#065f46 !important; }
        .dark .badge-failed   { background:#7f1d1d !important; color:#fca5a5 !important; border-color:#991b1b !important; }
        .badge-pending::before  { content:''; display:inline-block; width:6px; height:6px; border-radius:50%; background:#f59e0b; }
        .badge-approved::before { content:''; display:inline-block; width:6px; height:6px; border-radius:50%; background:#22c55e; }
        .badge-rejected::before { content:''; display:inline-block; width:6px; height:6px; border-radius:50%; background:#ef4444; }
        .badge-paid::before     { content:''; display:inline-block; width:6px; height:6px; border-radius:50%; background:#22c55e; }

        /* ── Buttons ── */
        .btn { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:700; border-radius:7px; padding:6px 12px; transition:all .15s; cursor:pointer; border:none; white-space:nowrap; }
        .btn:hover { transform:translateY(-1px); }
        .btn-green  { background:#25D366; color:#fff; }
        .btn-green:hover { background:#20bd5a; }
        .btn-red    { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
        .btn-red:hover { background:#fee2e2; }
        .btn-blue   { background:#eff6ff; color:#2563eb; border:1px solid #dbeafe; }
        .btn-blue:hover { background:#dbeafe; }
        .btn-orange { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
        .btn-orange:hover { background:#ffedd5; }
        .btn-slate  { background:#f8fafc; color:#475569; border:1px solid #e2e8f0; }
        .btn-slate:hover { background:#f1f5f9; }
        .dark .btn-slate { background:#1e293b; color:#cbd5e1; border-color:#334155; }
        .dark .btn-slate:hover { background:#334155; }

        /* admin-table alias for backwards compatibility */
        .admin-table { width:100%; border-collapse:collapse; }
        .admin-table thead tr { background:#f8fafc; }
        .dark .admin-table thead tr { background:#1e293b; }
        .admin-table th { padding:11px 16px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#94a3b8; border-bottom:1px solid #e2e8f0; white-space:nowrap; }
        .dark .admin-table th { border-bottom-color: #334155; color: #cbd5e1; }
        .admin-table td { padding:13px 16px; font-size:13px; color:#334155; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
        .dark .admin-table td { color: #f8fafc; border-bottom-color: #1e293b; }
        .admin-table tbody tr:hover td { background:#fafbfd; }
        .dark .admin-table tbody tr:hover td { background:#0f172a; }
        .admin-table tbody tr:last-child td { border-bottom:none; }

        /* Cards */
        .card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04); }
        .dark .card { background:#1e293b; border-color:#334155; }
        .card-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f1f5f9; }
        .dark .card-header { border-bottom-color: #334155; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:3px; }
        ::-webkit-scrollbar-thumb:hover { background:#94a3b8; }

        /* Transitions */
        .sidebar-transition { transition: width .25s cubic-bezier(.4,0,.2,1), transform .25s cubic-bezier(.4,0,.2,1); }

        /* Stat card hover */
        .stat-card { transition: box-shadow .15s, transform .15s; }
        .stat-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); transform:translateY(-1px); }

        /* Global Dark Mode Overrides */
        .dark .bg-white { background-color: #1e293b !important; }
        .dark .bg-slate-50 { background-color: #0f172a !important; }
        .dark .bg-slate-100 { background-color: #334155 !important; }
        .dark .hover\:bg-slate-50:hover { background-color: #0f172a !important; }
        .dark .hover\:bg-slate-100:hover { background-color: #334155 !important; }
        
        .dark .text-slate-900 { color: #f8fafc !important; }
        .dark .text-slate-800 { color: #f1f5f9 !important; }
        .dark .text-slate-700 { color: #cbd5e1 !important; }
        .dark .text-slate-600 { color: #94a3b8 !important; }
        .dark .text-slate-500 { color: #64748b !important; }
        
        .dark .border-slate-200 { border-color: #334155 !important; }
        .dark .border-slate-100 { border-color: #1e293b !important; }
        .dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]) { border-color: #334155 !important; }
        .dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]) { border-color: #1e293b !important; }

        /* Dark mode: form inputs, selects, textareas */
        .dark input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        .dark select,
        .dark textarea {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
            color-scheme: dark;
        }
        .dark input::placeholder,
        .dark textarea::placeholder { color: #475569 !important; }
        .dark input:focus,
        .dark select:focus,
        .dark textarea:focus {
            border-color: #4ade80 !important;
            outline: none;
        }
        .dark code { background: #334155 !important; color: #94a3b8 !important; }
        .dark .bg-amber-50 { background-color: #451a03 !important; }
        .dark .text-amber-700 { color: #fde68a !important; }
        .dark .border-amber-200 { border-color: #78350f !important; }
        .dark .bg-green-50:not(.badge-approved):not(.badge-paid) { background-color: #064e3b !important; }
        .dark .text-green-800 { color: #86efac !important; }
        .dark .bg-red-50:not(.badge-rejected):not(.badge-failed) { background-color: #450a0a !important; }
        .dark .text-red-800 { color: #fca5a5 !important; }
        .dark h1.text-slate-800, .dark h1.text-slate-900,
        .dark h2.text-slate-800, .dark h2.text-slate-900,
        .dark h3.text-slate-800, .dark h3.text-slate-900 { color: #f1f5f9 !important; }
        .dark p.text-slate-700 { color: #cbd5e1 !important; }
        .dark .text-slate-700 { color: #cbd5e1 !important; }
        .dark label.text-slate-700 { color: #cbd5e1 !important; }
        .dark .bg-slate-200 { background-color: #334155 !important; }
        .dark .hover\:bg-slate-300:hover { background-color: #475569 !important; }
        .dark .text-slate-800 { color: #f1f5f9 !important; }
        .dark a.text-slate-800 { color: #f1f5f9 !important; }
        .dark .divide-slate-100 > * { border-color: #1e293b; }
        .dark .hover\:bg-slate-50:hover { background-color: #0f172a !important; }
        .dark tr.hover\:bg-slate-50:hover td { background-color: #0f172a !important; }
        .dark .file\:bg-blue-50 { --tw-bg-opacity: 1; background-color: #1e3a5f !important; }
        .dark .file\:text-blue-700 { color: #93c5fd !important; }
    </style>
</head>
<body class="bg-body flex min-h-screen relative dark:bg-slate-900" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

    {{-- Sidebar --}}
    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-20 md:hidden"></div>

    {{-- ── SIDEBAR ── --}}
    <aside class="fixed md:sticky top-0 left-0 h-screen flex flex-col z-30 sidebar-transition flex-shrink-0 overflow-hidden bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800"
           :class="{'w-60': !sidebarCollapsed, 'w-[68px]': sidebarCollapsed, '-translate-x-full md:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen}">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-slate-200 dark:border-slate-800 flex-shrink-0 overflow-hidden"
             :class="{'justify-center': sidebarCollapsed}">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
                 style="background:linear-gradient(135deg,#25D366,#128C7E);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/>
                </svg>
            </div>
            <div x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms>
                <p class="font-black text-slate-800 dark:text-white text-sm leading-tight">WhatsGrupos</p>
                <p class="text-[10px] text-slate-500 font-bold tracking-widest uppercase mt-0.5">Admin Panel</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4 space-y-0.5">
            {{-- Section label --}}
            <p x-show="!sidebarCollapsed" x-transition.opacity class="text-[9px] font-black uppercase tracking-widest text-slate-500 px-3 mb-2 mt-1">Principal</p>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Dashboard">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Dashboard</span>
            </a>

            <a href="{{ route('admin.analytics') }}"
               class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Analytics">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Analytics</span>
            </a>

            <div class="border-t border-slate-100 dark:border-slate-800 my-3"></div>
            <p x-show="!sidebarCollapsed" x-transition.opacity class="text-[9px] font-black uppercase tracking-widest text-slate-500 px-3 mb-2">Conteúdo</p>

            <a href="{{ route('admin.groups.pending') }}"
               class="nav-link {{ request()->routeIs('admin.groups.pending') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Grupos Pendentes">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Pendentes</span>
            </a>

            <a href="{{ route('admin.groups.index') }}"
               class="nav-link {{ request()->routeIs('admin.groups.index') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Todos os Grupos">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Todos os Grupos</span>
            </a>

            <a href="{{ route('admin.figurinhas.index') }}"
               class="nav-link {{ request()->routeIs('admin.figurinhas.*') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Figurinhas">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Figurinhas</span>
            </a>

            <a href="{{ route('admin.phrases.index') }}"
               class="nav-link {{ request()->routeIs('admin.phrases.*') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Frases">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Frases</span>
            </a>

            <a href="{{ route('admin.blog.index') }}"
               class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Blog">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Blog</span>
            </a>

            <div class="border-t border-slate-100 dark:border-slate-800 my-3"></div>
            <p x-show="!sidebarCollapsed" x-transition.opacity class="text-[9px] font-black uppercase tracking-widest text-slate-500 px-3 mb-2">Financeiro</p>

            <a href="{{ route('admin.orders.index') }}"
               class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Pedidos VIP">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Pedidos VIP</span>
            </a>

            <div class="border-t border-slate-100 dark:border-slate-800 my-3"></div>
            <p x-show="!sidebarCollapsed" x-transition.opacity class="text-[9px] font-black uppercase tracking-widest text-slate-500 px-3 mb-2">Sistema</p>

            <a href="{{ route('admin.collector.index') }}"
               class="nav-link {{ request()->routeIs('admin.collector.*') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Coletor de Bot">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Coletor de Bot</span>
            </a>

            <a href="{{ route('admin.settings') }}"
               class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Configurações">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>AdSense / Config</span>
            </a>

            <div class="border-t border-slate-100 dark:border-slate-800 my-3"></div>

            <a href="{{ route('home') }}" target="_blank"
               class="nav-link"
               :class="{'justify-center px-0': sidebarCollapsed}" title="Ver Site Público">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.100ms>Ver Site Público</span>
            </a>
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex-shrink-0">
            <form action="{{ route('admin.logout') }}" method="POST" class="no-confirm">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-3 py-2 text-red-500 dark:text-red-400 font-bold text-xs rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                        :class="{'justify-center': sidebarCollapsed}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="!sidebarCollapsed">Sair do Painel</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- ── TOP BAR ── --}}
        <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 md:px-6 py-3 flex items-center justify-between sticky top-0 z-10 transition-colors shadow-sm flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Mobile hamburger --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 md:hidden flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Desktop collapse --}}
                <button @click="sidebarCollapsed = !sidebarCollapsed"
                        class="hidden md:flex p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all flex-shrink-0"
                        :class="{'rotate-180': sidebarCollapsed}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Breadcrumb --}}
                <div class="hidden sm:block">
                    <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-500 transition-colors">Admin</a>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="text-slate-500 dark:text-slate-400">@yield('page-title', 'Dashboard')</span>
                    </div>
                    <h1 class="text-base font-black text-slate-800 dark:text-white truncate">@yield('page-title', 'Painel')</h1>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-shrink-0">
                {{-- Dark mode toggle --}}
                <form action="{{ route('admin.theme.toggle') }}" method="POST" class="no-confirm">
                    @csrf
                    <button type="submit"
                            class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                            title="{{ session('admin_theme') === 'dark' ? 'Modo Claro' : 'Modo Escuro' }}">
                        @if(session('admin_theme') === 'dark')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                        @endif
                    </button>
                </form>

                {{-- Admin badge dropdown --}}
                <div class="relative hidden sm:block" x-data="{ userMenu: false }">
                    <button @click="userMenu = !userMenu" @click.away="userMenu = false" class="flex items-center gap-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                             style="background:linear-gradient(135deg,#25D366,#128C7E);">
                            A
                        </div>
                        <div class="leading-tight text-left">
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ session('admin_name') ?? 'Administrador' }}</p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ session('admin_email') ?? 'admin@whatsgrupos.com' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="userMenu" x-transition.opacity.duration.200ms
                         style="display: none;"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg overflow-hidden z-50">
                        <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 font-medium transition-colors">
                            Meu Perfil
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST" class="block border-t border-slate-100 dark:border-slate-700 no-confirm">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-slate-50 dark:hover:bg-slate-700 font-bold transition-colors">
                                Sair do Sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- ── PAGE CONTENT ── --}}
        <main class="flex-1 p-4 md:p-6 overflow-auto">

            {{-- Alerts --}}
            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-xl px-4 py-3 text-sm font-semibold shadow-sm">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 flex items-center gap-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 rounded-xl px-4 py-3 text-sm font-semibold shadow-sm">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.tiny.cloud/1/7z8np3ft9l8rtj4bcrbg73k20hcdds5eziv9n9f4psyk1oyx/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        setTimeout(() => {
            if (!document.querySelector('.tox-tinymce') && document.querySelector('textarea#content')) {
                tinymce.init({
                    selector: 'textarea#content',
                    plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
                    toolbar_mode: 'floating',
                    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                    height: 500,
                    setup: editor => editor.on('change', () => tinymce.triggerSave())
                });
            }
        }, 500);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Remove onsubmit nativo de todos os formulários e guarda a mensagem
            document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
                const onsubmitAttr = form.getAttribute('onsubmit');
                const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                if (match && match[1]) {
                    form.setAttribute('data-confirm', match[1]);
                }
                form.removeAttribute('onsubmit');
            });

            // Intercepta os submits de formulário para usar SweetAlert2
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                // Só intercepta formulários POST que não tenham a classe no-confirm
                if (form.method && form.method.toUpperCase() === 'POST' && !form.classList.contains('no-confirm')) {
                    e.preventDefault();
                    
                    let message = form.getAttribute('data-confirm') || 'Tem certeza que deseja salvar estas alterações?';

                    Swal.fire({
                        title: 'Confirmação',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: 'Sim, confirmar!',
                        cancelButtonText: 'Cancelar',
                        background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.classList.add('no-confirm');
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
