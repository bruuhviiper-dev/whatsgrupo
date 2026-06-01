<?php

use App\Jobs\CheckLinksJob;
use App\Jobs\CollectGroupsJob;
use App\Jobs\ExpireBoostsJob;
use App\Jobs\GenerateSitemapJob;
use App\Jobs\RecalculateScoresJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Comando de inspiração padrão do Laravel
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expira grupos VIP a cada 1 minuto (garante expiração precisa sem delay)
Schedule::job(new ExpireBoostsJob)->everyMinute();

// Gera sitemap diariamente à meia-noite
Schedule::job(new GenerateSitemapJob)->daily();

// Coleta automática de grupos: toda segunda-feira às 3h
// Usa a fila 'coleta' para não bloquear as demais
Schedule::job((new CollectGroupsJob)->onQueue('coleta'))->weeklyOn(1, '03:00');

// Verifica links inativos diariamente às 2h
Schedule::job(new CheckLinksJob)->dailyAt('02:00');

// Recalcula score dos grupos aprovados a cada 6 horas
Schedule::job(new RecalculateScoresJob)->everySixHours();
