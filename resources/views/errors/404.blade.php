<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada — 404</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Outfit:wght@700;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN para garantir o estilo sem depender do layout principal -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
              heading: ['Outfit', 'sans-serif'],
            }
          }
        }
      }
    </script>
</head>
<body class="bg-white min-h-screen flex flex-col items-center justify-center text-center px-4 m-0 font-sans">
    
    <!-- Emoji Triste -->
    <div class="mb-2">
        <svg class="w-32 h-32 md:w-40 md:h-40 mx-auto" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="42" fill="#FFD13B" stroke="#000" stroke-width="5"/>
            <path d="M 30 38 Q 35 30 40 38" fill="none" stroke="#000" stroke-width="5" stroke-linecap="round"/>
            <path d="M 60 38 Q 65 30 70 38" fill="none" stroke="#000" stroke-width="5" stroke-linecap="round"/>
            <path d="M 35 65 Q 50 50 65 65" fill="none" stroke="#000" stroke-width="5" stroke-linecap="round"/>
            <path d="M 35 43 C 25 55 25 65 35 65 C 45 65 45 55 35 43 Z" fill="#00C3FF" stroke="#000" stroke-width="3" stroke-linejoin="round"/>
        </svg>
    </div>

    <!-- 404 Title -->
    <h1 class="text-[80px] md:text-[100px] font-black text-[#2D3748] mb-0 leading-none font-heading tracking-tight">
        404
    </h1>

    <!-- Separator Line -->
    <div class="w-16 h-[5px] bg-[#25D366] rounded-full mx-auto mt-4 mb-6"></div>

    <!-- Main Message -->
    <h2 class="text-2xl md:text-3xl font-bold text-[#2D3748] mb-3">
        Ooooops, a página não foi encontrada
    </h2>

    <!-- Sub Message -->
    <p class="text-slate-500 mb-8 max-w-lg mx-auto text-sm md:text-base">
        Você pode continuar navegando no nosso site e conferindo vários grupos de whatsapp legais
    </p>

    <!-- Action Button -->
    <a href="/" class="bg-[#25D366] hover:bg-[#1da851] text-white font-bold py-3 px-8 rounded flex items-center justify-center transition-all duration-300 transform hover:-translate-y-1 shadow-[0_4px_14px_0_rgba(37,211,102,0.39)] uppercase text-sm md:text-base inline-flex">
        Continuar Navegando
    </a>
    
</body>
</html>
