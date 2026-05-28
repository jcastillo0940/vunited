<?php

use App\Http\Controllers\Api\BoardMemberController;
use App\Http\Controllers\Api\ClubController;
use App\Http\Controllers\Api\StadiumController as ApiStadiumController;
use App\Http\Controllers\Api\ExpeditionController;
use App\Http\Controllers\Api\FanFestController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\StandingController;
use App\Http\Controllers\Api\IssuedTicketController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\SponsorController;
use App\Http\Controllers\Api\StaffMemberController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\MembershipOrderController;
use App\Http\Controllers\Api\MembershipPlanController;
use App\Http\Controllers\Api\MatchEventController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\StoreOrderController;
use App\Http\Controllers\Api\TicketOrderController;
use App\Http\Controllers\Api\TicketZoneController;
use App\Http\Controllers\Webhooks\PayPalWebhookController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::get('/site-settings', SiteSettingController::class);
    Route::get('/menu/{location}', MenuController::class)
        ->whereIn('location', ['header', 'footer']);
    Route::get('/pages/{slug}', PageController::class);
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{slug}', [NewsController::class, 'show']);

    Route::post('/webhooks/paypal', PayPalWebhookController::class)->name('webhooks.paypal');

    Route::get('/ticketing/matches/featured', [MatchEventController::class, 'featured'])->name('ticketing.matches.featured');
    Route::get('/ticketing/matches/{code}/zones', [TicketZoneController::class, 'index'])->name('ticketing.matches.zones');
    Route::get('/ticketing/matches/{code}', [MatchEventController::class, 'show'])->name('ticketing.matches.show');
    Route::get('/ticketing/matches', [MatchEventController::class, 'index'])->name('ticketing.matches.index');

    Route::get('/store/products', [ProductController::class, 'index'])->name('store.products.index');
    Route::get('/store/products/{slug}', [ProductController::class, 'show'])->name('store.products.show');
    Route::get('/store/categories', [ProductCategoryController::class, 'index'])->name('store.categories.index');
    Route::get('/store/featured-product', [ProductController::class, 'featured'])->name('store.products.featured');
    Route::post('/store/orders', [StoreOrderController::class, 'store'])->name('store.orders.store');
    Route::get('/store/orders/{orderNumber}', [StoreOrderController::class, 'show'])->name('store.orders.show');
    Route::get('/membership-plans/active', [MembershipPlanController::class, 'active'])->name('membership-plans.active');
    Route::post('/membership-orders', [MembershipOrderController::class, 'store'])->name('membership-orders.store');
    Route::get('/membership-orders/{orderNumber}', [MembershipOrderController::class, 'show'])->name('membership-orders.show');

    Route::get('/stadium', ApiStadiumController::class)->name('stadium.show');

    Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
    Route::get('/matches/featured', [MatchController::class, 'featured'])->name('matches.featured');
    Route::get('/matches/{code}', [MatchController::class, 'show'])->name('matches.show');
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/standings', [StandingController::class, 'index'])->name('standings.index');

    Route::get('/expeditions', [ExpeditionController::class, 'index'])->name('expeditions.index');
    Route::get('/fanfest', [FanFestController::class, 'index'])->name('fanfest.index');
    Route::get('/board-members', [BoardMemberController::class, 'index'])->name('board-members.index');
    Route::get('/sponsors', [SponsorController::class, 'index'])->name('sponsors.index');
    Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
    Route::get('/players/{slug}', [PlayerController::class, 'show'])->name('players.show');
    Route::get('/staff', [StaffMemberController::class, 'index'])->name('staff.index');

    Route::post('/ticketing/orders', [TicketOrderController::class, 'store'])->name('ticketing.orders.store');
    Route::get('/ticketing/orders/{orderNumber}', [TicketOrderController::class, 'show'])->name('ticketing.orders.show');
    Route::get('/ticketing/orders/{orderNumber}/tickets', [IssuedTicketController::class, 'forOrder'])->name('ticketing.orders.tickets');
});
