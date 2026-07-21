<?php

use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

// Backend API-only; el frontend vive en ticketing/frontend. Esta ruta solo
// confirma que el servicio responde (equivalente al bootstrap de Fase 2).
Route::get('/', function () {
    return response()->json([
        'service' => 'ticketing',
        'status' => 'ok',
    ]);
});

// nginx (Fase 2) apunta /healthz aqui, en la raiz, para el health check del
// pool - misma logica que /api/healthz, expuesta tambien sin el prefijo.
Route::get('/healthz', HealthController::class)->name('healthz.root');
