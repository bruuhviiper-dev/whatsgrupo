<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Widget WhatsGrupos</title>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #F8FAFC;
            color: #0F172A;
            font-family: 'Inter', sans-serif;
            overflow: hidden; /* iframe não mostra scroll próprio */
        }
        .widget-root {
            display: flex;
            flex-direction: column;
            height: 100vh; /* preenche o iframe inteiro */
            padding: 12px;
            box-sizing: border-box;
        }
        .groups-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0; /* essencial para flex + overflow funcionar */
        }
        .groups-scroll::-webkit-scrollbar { width: 4px; }
        .groups-scroll::-webkit-scrollbar-track { background: #F1F5F9; border-radius: 2px; }
        .groups-scroll::-webkit-scrollbar-thumb { background: #25D366; border-radius: 2px; }
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
        /* Grid de grupos — sempre 2 colunas independente do breakpoint */
        .groups-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
        /* 1 coluna se o widget for muito estreito (abaixo de 320px) */
        @media (max-width: 320px) {
            .groups-grid { grid-template-columns: 1fr; }
        }
        .group-item {
            background: #FFFFFF;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.06);
            padding: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
            text-decoration: none;
            transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
            position: relative;
        }
        .group-item:hover {
            border-color: rgba(37,211,102,0.4);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transform: translateY(-1px);
        }
        .group-item:hover .group-name { color: #25D366; }
    </style>
</head>
<body>
<div class="widget-root">

    {{-- Header fixo --}}
    <div style="flex-shrink:0;" class="flex items-center justify-between border-b border-black/5 pb-2 mb-3">
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;color:#25D366;">
                <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd" />
            </svg>
            <span style="font-weight:900;font-size:14px;letter-spacing:-0.02em;background:linear-gradient(to right,#128C7E,#25D366);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                WhatsGrupos
            </span>
        </div>
        <span style="font-size:10px;font-weight:700;text-transform:uppercase;color:#64748B;padding:2px 8px;background:rgba(0,0,0,0.05);border-radius:99px;border:1px solid rgba(0,0,0,0.05);">
            {{ $categoryName }}
        </span>
    </div>

    {{-- Lista scrollável --}}
    <div class="groups-scroll">
        @if ($groups->isEmpty())
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;text-align:center;padding:24px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:32px;height:32px;color:#CBD5E1;margin-bottom:8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <p style="color:#64748B;font-size:11px;">Nenhum grupo encontrado nesta categoria.</p>
            </div>
        @else
            <div class="groups-grid">
                @foreach ($groups as $group)
                    @php $isVip = $group->is_currently_vip; @endphp
                    <a href="{{ route('group.show', $group->id) }}" target="_blank" rel="noopener noreferrer"
                       class="group-item {{ $isVip ? 'vip-card-border' : '' }}">

                        {{-- Avatar --}}
                        @if ($group->image_path)
                            <img src="{{ asset('storage/' . $group->image_path) }}" alt="{{ $group->name }}"
                                 style="width:40px;height:40px;border-radius:10px;object-fit:cover;flex-shrink:0;border:1px solid rgba(0,0,0,0.08);">
                        @else
                            <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#128C7E,#25D366);display:flex;align-items:center;justify-content:center;flex-shrink:0;position:relative;overflow:hidden;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:26px;height:26px;color:white;opacity:0.25;position:absolute;">
                                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                </svg>
                                <span style="font-weight:900;font-size:14px;color:white;position:relative;z-index:1;">{{ mb_strtoupper(mb_substr($group->name, 0, 1)) }}</span>
                            </div>
                        @endif

                        {{-- Detalhes --}}
                        <div style="min-width:0;flex:1;">
                            <div style="display:flex;align-items:center;gap:3px;margin-bottom:2px;">
                                <span class="group-name" style="font-weight:700;font-size:11px;color:#0F172A;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;transition:color 0.15s;">
                                    {{ $group->name }}
                                </span>
                                @if ($isVip)
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" style="width:10px;height:10px;color:#F59E0B;flex-shrink:0;">
                                        <path d="M239.54,98.11l-36.88,86.07a16,16,0,0,1-14.66,9.82H68a16,16,0,0,1-14.66-9.82L16.46,98.11A8,8,0,0,1,24.63,86.3l57,21.36,39.11-65.18a8,8,0,0,1,13.72,0l39.11,65.18,57-21.36a8,8,0,0,1,8.17,11.81Z"></path>
                                    </svg>
                                @endif
                                @if ($group->is_verified)
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:10px;height:10px;color:#3B82F6;flex-shrink:0;">
                                        <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.491 4.491 0 01-3.497-1.307 4.491 4.491 0 01-1.307-3.497A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.492 4.492 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <p style="color:#64748B;font-size:10px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:4px;">
                                {{ $group->category ? $group->category->name : 'Geral' }}
                            </p>
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span style="display:flex;align-items:center;gap:2px;font-size:9px;color:#94A3B8;font-family:monospace;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:10px;height:10px;">
                                        <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ number_format($group->views) }}
                                </span>
                                <span style="background:#25D366;color:white;font-size:9px;padding:2px 8px;border-radius:6px;font-weight:700;line-height:1.4;">
                                    Entrar
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Footer fixo --}}
    <div style="flex-shrink:0;" class="flex items-center justify-between border-t border-black/5 pt-2 mt-3">
        <span style="font-size:10px;color:#64748B;">Divulgue seu grupo!</span>
        <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
           style="font-size:10px;font-weight:900;color:#25D366;text-decoration:none;display:flex;align-items:center;gap:2px;transition:color 0.15s;"
           onmouseover="this.style.color='#20bd5a'" onmouseout="this.style.color='#25D366'">
            whatsgrupos.com.br
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:10px;height:10px;">
                <path fill-rule="evenodd" d="M15.75 2.25H21a.75.75 0 01.75.75v5.25a.75.75 0 01-1.5 0V4.81L8.03 17.03a.75.75 0 01-1.06-1.06L19.19 3.75h-3.44a.75.75 0 010-1.5zm-10.5 4.5a1.5 1.5 0 00-1.5 1.5v10.5a1.5 1.5 0 001.5 1.5h10.5a1.5 1.5 0 001.5-1.5V10.5a.75.75 0 011.5 0v8.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V8.25a3 3 0 013-3H13.5a.75.75 0 010 1.5H5.25z" clip-rule="evenodd" />
            </svg>
        </a>
    </div>

</div>
</body>
</html>
