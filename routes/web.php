<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Public/Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/style-guide', function () {
    return Inertia::render('Public/StyleGuide');
})->name('style-guide');

Route::get('/noticias', function () {
    return Inertia::render('Public/NewsIndex');
})->name('news.index');

Route::get('/noticias/{slug}', function (string $slug) {
    return Inertia::render('Public/NewsShow', [
        'slug' => $slug,
    ]);
})->name('news.show');

Route::get('/pagina/{slug}', function (string $slug) {
    return Inertia::render('Public/CmsPage', [
        'slug' => $slug,
    ]);
})->name('cms.page');

Route::get('/patrocinadores', function () {
    return Inertia::render('Public/Sponsors');
})->name('sponsors.index');

Route::get('/plantilla', function () {
    return Inertia::render('Public/Squad');
})->name('squad.index');

Route::get('/jugadores/{slug}', function (string $slug) {
    return Inertia::render('Public/PlayerProfile', [
        'slug' => $slug,
    ]);
})->name('players.show');

Route::get('/fuerzas-basicas', function () {
    return Inertia::render('Public/Academy');
})->name('academy.index');

Route::get('/pruebas', function () {
    return Inertia::render('Public/Tryouts');
})->name('tryouts.index');

Route::get('/directiva', function () {
    return Inertia::render('Public/Board');
})->name('board.index');

Route::get('/fanclub', function () {
    return Inertia::render('Public/FanClub');
})->name('fanclub.index');

Route::get('/fanfest', function () {
    return Inertia::render('Public/FanFest');
})->name('fanfest.index');

Route::get('/expedicion-india', function () {
    return Inertia::render('Public/Expedition');
})->name('expedition.index');

Route::get('/registro-tribu', function () {
    return Inertia::render('Public/RegisterTribe');
})->name('tribe.register');

Route::get('/registro-confirmado', function () {
    return Inertia::render('Public/RegistrationConfirmed');
})->name('tribe.confirmed');

Route::get('/tienda', function () {
    return Inertia::render('Public/Store');
})->name('store.index');

Route::get('/carrito', function () {
    return Inertia::render('Public/Cart');
})->name('cart.index');

Route::get('/orden-tienda-confirmada', function () {
    return Inertia::render('Public/StoreOrderConfirmed');
})->name('store.orders.confirmed');

Route::get('/boletos', function () {
    return Inertia::render('Public/Tickets');
})->name('tickets.index');

Route::get('/orden-boletos-confirmada', function () {
    return Inertia::render('Public/TicketOrderConfirmed');
})->name('ticket-orders.confirmed');

Route::get('/calendario', function () {
    return Inertia::render('Public/Calendar');
})->name('calendar.index');

Route::get('/estadio', function () {
    return Inertia::render('Public/Stadium');
})->name('stadium.index');

Route::get('/tabla-posiciones', function () {
    return Inertia::render('Public/Standings');
})->name('standings.public');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin_auth.php';
require __DIR__.'/admin.php';
