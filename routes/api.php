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

// Web Push Notifications (Inscrições e preferências VAPID)
Route::post('/push/subscribe', [PushController::class, 'subscribe'])->name('api.push.subscribe');
Route::post('/push/unsubscribe', [PushController::class, 'unsubscribe'])->name('api.push.unsubscribe');
Route::post('/push/preferences', [PushController::class, 'preferences'])->name('api.push.preferences');
