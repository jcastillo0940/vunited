<?php

use App\Http\Controllers\Admin\CashPaymentController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\DoorController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerAuthController;
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

Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

Route::post('/customers/register', [CustomerAuthController::class, 'register'])->name('customers.register');
Route::post('/customers/login', [CustomerAuthController::class, 'login'])->name('customers.login');

// Escaner de puerta: requiere sesion de operador (Sanctum).
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/validate', [ValidationController::class, 'scan'])->name('tickets.validate');
    Route::get('/events/{eventPublicId}/doors', [DoorController::class, 'index'])->name('events.doors');
});

// Todo el proceso de compra y consulta de boletos requiere sesion de cliente:
// el public_id (ULID) ya no es suficiente por si solo, se verifica dueno real.
// Guard separado de 'sanctum' (el de Operator/Admin): cada guard sanctum
// valida el tokenable contra SU propio provider, ver config/auth.php.
Route::middleware(['auth:sanctum_customers', 'customer'])->group(function () {
    Route::post('/customers/logout', [CustomerAuthController::class, 'logout'])->name('customers.logout');
    Route::get('/customers/me', [CustomerAuthController::class, 'me'])->name('customers.me');
    Route::get('/customers/orders', [OrderController::class, 'mine'])->name('customers.orders');
    Route::get('/customers/tickets', [OrderController::class, 'myTickets'])->name('customers.tickets');

    Route::post('/events/{publicId}/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{publicId}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{publicId}/tickets', [OrderController::class, 'tickets'])->name('orders.tickets');
    Route::post('/orders/{publicId}/payment', [OrderController::class, 'requestPayment'])->name('orders.payment');

    Route::get('/tickets/{publicId}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{publicId}/wallet/google', [WalletController::class, 'google'])->name('tickets.wallet.google');
    Route::get('/tickets/{publicId}/wallet/apple', [WalletController::class, 'apple'])->name('tickets.wallet.apple');
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
    Route::get('/cash-payments', [CashPaymentController::class, 'index']);
    Route::post('/cash-payments/{publicId}/confirm', [CashPaymentController::class, 'confirm']);
});

// Rutas internas: nunca las llama el navegador (VerifyInternalSecret).
Route::middleware('internal.secret')->prefix('internal')->group(function () {
    Route::post('/payments/events', PaymentEventController::class)
        ->name('internal.payments.events');
});
