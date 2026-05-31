@echo off
echo.
echo ================================================================
echo    WHATSGRUPOS - AMBIENTE COMPLETO v3.0
echo    Coletor Universal + Servidor + Filas + Agendador
echo ================================================================
echo.

echo [0/6] Verificando dependencias...
where php >nul 2>&1
if errorlevel 1 (
    echo [ERRO] PHP nao encontrado no PATH.
    echo        Instale o PHP e adicione ao PATH antes de continuar.
    pause
    exit /b 1
)
echo       PHP encontrado.

where python >nul 2>&1
if errorlevel 1 (
    where python3 >nul 2>&1
    if errorlevel 1 (
        echo       [AVISO] Python nao encontrado. O coletor pode nao funcionar.
    ) else (
        echo       Python3 encontrado.
    )
) else (
    echo       Python encontrado.
)
echo.

echo [1/6] Iniciando Servidor Laravel em http://127.0.0.1:8000 ...
start /min "WG - Servidor" cmd /k "title WG Servidor && php artisan serve --host=127.0.0.1 --port=8000"
echo       OK
echo.

echo [2/6] Iniciando Vite (assets CSS/JS)...
start /min "WG - Vite" cmd /k "title WG Vite && npm run dev"
echo       OK
echo.

timeout /t 2 /nobreak >nul

echo [3/6] Iniciando Queue Worker (filas: coleta, default)...
start /min "WG - Queue" cmd /k "title WG Queue Worker && php artisan queue:work --queue=coleta,default --timeout=10800 --tries=3 --sleep=3"
echo       OK
echo.

echo [4/6] Iniciando Agendador Automatico...
start /min "WG - Agendador" cmd /k "title WG Agendador && php artisan schedule:work"
echo       OK
echo.

echo [5/6] Iniciando Monitor de Log (coleta em tempo real)...
start /min "WG - Log" cmd /k "title WG Monitor de Log && php artisan mineracao:log --follow"
echo       OK
echo.

timeout /t 3 /nobreak >nul

echo [6/6] Disparando Coletor Universal v3.0 para a fila...
php artisan grupos:coletar --queue
echo.

echo ================================================================
echo   AMBIENTE ONLINE!
echo.
echo   Site:       http://127.0.0.1:8000
echo   Coletor:    Rodando na fila "coleta" em background
echo   Log:        storage/logs/mineracao.log
echo.
echo   Janelas abertas (minimizadas na barra de tarefas):
echo     - WG Servidor          (Laravel)
echo     - WG Vite              (Assets)
echo     - WG Queue Worker      (Filas: coleta, default)
echo     - WG Agendador         (Schedule)
echo     - WG Monitor de Log    (Coleta em tempo real)
echo.
echo   Para ver grupos pendentes:
echo     php artisan tinker
echo     App\Models\Group::where("status","pending")->count();
echo ================================================================
echo.
pause
