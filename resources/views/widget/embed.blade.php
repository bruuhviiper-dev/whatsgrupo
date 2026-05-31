<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Widget WhatsGrupos</title>
    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#128C7E',
                        secondary: '#25D366',
                        bg: '#F8FAFC',
                        card: '#FFFFFF',
                        'text-main': '#0F172A',
                        'text-muted': '#64748B',
                        gold: '#F59E0B',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F8FAFC;
            color: #0F172A;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        /* VIP border animation */
        .vip-card-border {
            position: relative;
        }
        .vip-card-border::after {
            content: '';
            position: absolute;
            inset: -1.5px;
            border-radius: 12px;
            background: linear-gradient(135deg, #FFD700, #FFB700, #FFA500);
            z-index: -1;
            animation: pulseGold 2.5s ease-in-out infinite;
        }
        @keyframes pulseGold {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; filter: drop-shadow(0 0 4px rgba(255, 215, 0, 0.6)); }
        }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #F1F5F9; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #25D366; border-radius: 2px; }
    </style>
</head>
<body class="p-3 select-none custom-scroll">
    <div class="flex flex-col h-full min-h-[460px] justify-between">
        {{-- Header do Widget --}}
        <div class="flex items-center justify-between border-b border-black/5 pb-2 mb-3">
            <div class="flex items-center gap-1.5">
                <span class="text-base sm:text-lg">📱</span>
                <span class="font-black text-sm sm:text-base tracking-tight bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                    WhatsGrupos
                </span>
            </div>
            <span class="text-[10px] uppercase font-bold text-text-muted px-2 py-0.5 bg-black/5 rounded-full border border-black/5">
                {{ $categoryName }}
            </span>
        </div>

        {{-- Lista de Grupos --}}
        @if ($groups->isEmpty())
            <div class="flex-1 flex flex-col items-center justify-center text-center p-6 bg-card rounded-xl border border-black/5">
                <span class="text-3xl mb-2">😢</span>
                <p class="text-text-muted text-xs">Nenhum grupo ativo encontrado nesta categoria no momento.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">
                @foreach ($groups as $group)
                    @php $isVip = $group->is_currently_vip; @endphp
                    <a href="{{ route('group.show', $group->slug) }}" target="_blank" rel="noopener noreferrer"
                       class="group bg-card rounded-xl border border-black/5 p-3 flex gap-3 items-center hover:border-primary/40 transition-all hover:translate-y-[-1px] {{ $isVip ? 'vip-card-border' : '' }}">
                        
                        {{-- Imagem do Grupo --}}
                        @if ($group->image_path)
                            <img src="{{ asset('storage/' . $group->image_path) }}" alt="{{ $group->name }}"
                                 class="w-11 h-11 rounded-lg object-cover flex-shrink-0 border border-black/10">
                        @else
                            <div class="w-11 h-11 rounded-lg flex items-center justify-center font-bold text-base flex-shrink-0 text-white select-none"
                                 style="background: linear-gradient(135deg, {{ '#' . substr(md5($group->name), 0, 6) }}, {{ '#' . substr(md5($group->slug), 0, 6) }});">
                                {{ mb_strtoupper(mb_substr($group->name, 0, 1)) }}
                            </div>
                        @endif

                        {{-- Detalhes --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1">
                                <h3 class="text-text-main font-bold text-xs truncate group-hover:text-secondary transition-colors leading-tight">
                                    {{ $group->name }}
                                </h3>
                                @if ($isVip)
                                    <span class="text-[9px] text-gold font-black shrink-0">⭐</span>
                                @endif
                            </div>
                            <p class="text-text-muted text-[10px] mt-0.5 truncate leading-none">
                                {{ $group->category ? $group->category->name : 'Sem Categoria' }}
                            </p>
                            <div class="flex items-center justify-between mt-1 text-[9px] text-text-muted font-mono leading-none">
                                <span class="flex items-center gap-0.5">👁 {{ number_format($group->views) }}</span>
                                <span class="bg-primary/20 text-secondary-hover px-1.5 py-0.5 rounded font-sans font-bold">Entrar</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Footer/Branding --}}
        <div class="flex items-center justify-between border-t border-black/5 pt-2 mt-3 text-[10px]">
            <span class="text-text-muted">Quer divulgar seu grupo?</span>
            <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
               class="font-black text-secondary hover:text-white transition-colors flex items-center gap-0.5">
                whatsgrupos.com.br <span class="text-xs">↗</span>
            </a>
        </div>
    </div>
</body>
</html>
