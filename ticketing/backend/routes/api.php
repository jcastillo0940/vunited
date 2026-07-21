<?php

use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\DoorController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ValidationController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Internal\PaymentEventController;
use Illuminate\Support\Facades\Route;

Route::get('/healthz', HealthController::class)->name('healthz');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{publicId}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{publicId}/zones', [EventController::class, 'zones'])->name('events.zones');

Route::post('/events/{publicId}/orders', [OrderController::class, 'store'])->name('orders.store');
// El public_id (ULID) es en si mismo el token de consulta: no adivinable,
// no expone el id autoincremental interno.
Route::get('/orders/{publicId}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{publicId}/tickets', [OrderController::class, 'tickets'])->name('orders.tickets');
Route::post('/orders/{publicId}/payment', [OrderController::class, 'requestPayment'])->name('orders.payment');

Route::get('/tickets/{publicId}', [TicketController::class, 'show'])->name('tickets.show');
Route::get('/tickets/{publicId}/wallet/google', [WalletController::class, 'google'])->name('tickets.wallet.google');
Route::get('/tickets/{publicId}/wallet/apple', [WalletController::class, 'apple'])->name('tickets.wallet.apple');

Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

// Escaner de puerta: requiere sesion de operador (Sanctum).
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/validate', [ValidationController::class, 'scan'])->name('tickets.validate');
    Route::get('/events/{eventPublicId}/doors', [DoorController::class, 'index'])->name('events.doors');
});

// Backoffice: Sanctum + rol admin.
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/events/{eventPublicId}/doors', [DoorController::class, 'index']);
    Route::post('/events/{eventPublicId}/doors', [DoorController::class, 'store']);
    Route::get('/operators', [OperatorController::class, 'index']);
    Route::post('/operators', [OperatorController::class, 'store']);
    Route::post('/operators/{operatorId}/assignments', [OperatorController::class, 'assign']);
    Route::post('/operators/{operatorId}/revoke', [OperatorController::class, 'revoke']);
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::post('/devices/{publicId}/revoke', [DeviceController::class, 'revoke']);
    Route::get('/reports/events', [ReportController::class, 'events']);
    Route::get('/reports/orders', [ReportController::class, 'orders']);
    Route::get('/reports/validations', [ReportController::class, 'validations']);
});

// Rutas internas: nunca las llama el navegador (VerifyInternalSecret).
Route::middleware('internal.secret')->prefix('internal')->group(function () {
    Route::post('/payments/events', PaymentEventController::class)
        ->name('internal.payments.events');
});
