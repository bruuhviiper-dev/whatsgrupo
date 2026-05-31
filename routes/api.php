<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\PushController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// API INTERNA — usada pelo Alpine.js nas views públicas
// =============================================================================

// Validação de link de WhatsApp chamada pelo formulário de envio de grupo
// POST /api/validate-link — retorna JSON com valid, name, image, error, warning
Route::post('/validate-link', [GroupController::class, 'validateLink'])->name('api.validate-link');

// Proxy de metadados do WhatsApp (og:title + og:image) — evita CORS no browser
// GET /api/wa-meta?url=... — retorna JSON com name, image
Route::get('/wa-meta', [GroupController::class, 'waMetaProxy'])->name('api.wa-meta');

// Proxy de imagem do WhatsApp — repassa a imagem ao browser sem CORS
// GET /api/wa-image?url=... — retorna bytes da imagem
Route::get('/wa-image', [GroupController::class, 'waImageProxy'])->name('api.wa-image');

// Web Push Notifications (Inscrições e preferências VAPID)
Route::post('/push/subscribe', [PushController::class, 'subscribe'])->name('api.push.subscribe');
Route::post('/push/unsubscribe', [PushController::class, 'unsubscribe'])->name('api.push.unsubscribe');
Route::post('/push/preferences', [PushController::class, 'preferences'])->name('api.push.preferences');
