<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Adquirentes\PagarMeController;
use App\Http\Controllers\Api\CallbackController;

// Rotas de callback para Pagar.me (novo sistema)
Route::post('pagarme/callback/deposit', [PagarMeController::class, 'callbackDeposit'])->middleware(['validate.webhook', 'throttle:30,1']);
Route::post('pagarme/callback/withdraw', [PagarMeController::class, 'callbackWithdraw'])->middleware(['validate.webhook', 'throttle:30,1']);
Route::post('pagarme/callback', [PagarMeController::class, 'callbackUnified'])->middleware(['validate.webhook', 'throttle:30,1']);
Route::post('pagarme/webhook', [PagarMeController::class, 'webhook'])->middleware(['validate.webhook', 'throttle:30,1']);

// Rota de compatibilidade com sistema antigo
Route::post('pagarme/webhook/legacy', [CallbackController::class, 'webhookPagarme'])->middleware(['validate.webhook', 'throttle:30,1']);
