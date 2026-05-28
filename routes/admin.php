<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\BoardMemberController;
use App\Http\Controllers\Admin\BusTripController;
use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\StadiumController;
use App\Http\Controllers\Admin\LeagueStandingController;
use App\Http\Controllers\Admin\MatchGoalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FanFestEventController;
use App\Http\Controllers\Admin\FanFestZoneController;
use App\Http\Controllers\Admin\IssuedTicketController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\StaffMemberController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MembershipOrderController;
use App\Http\Controllers\Admin\MembershipPlanController;
use App\Http\Controllers\Admin\MatchEventController;
use App\Http\Controllers\Admin\NewsArticleController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentEventController;
use App\Http\Controllers\Admin\PaymentMonitorController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StoreOrderController;
use App\Http\Controllers\Admin\TicketOrderController;
use App\Http\Controllers\Admin\TicketZoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/admin-users', [AdminUserController::class, 'index'])
            ->middleware('admin.permission:admin_users.view')
            ->name('admin-users.index');
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('admin.permission:roles.view')
            ->name('roles.index');
        Route::get('/settings', [SiteSettingController::class, 'edit'])
            ->middleware('admin.permission:settings.view')
            ->name('settings.edit');
        Route::put('/settings', [SiteSettingController::class, 'update'])
            ->middleware('admin.permission:settings.update')
            ->name('settings.update');
        Route::get('/menus', [MenuController::class, 'index'])
            ->middleware('admin.permission:menus.view')
            ->name('menus.index');
        Route::get('/menus/create', [MenuController::class, 'create'])
            ->middleware('admin.permission:menus.manage')
            ->name('menus.create');
        Route::post('/menus', [MenuController::class, 'store'])
            ->middleware('admin.permission:menus.manage')
            ->name('menus.store');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])
            ->middleware('admin.permission:menus.manage')
            ->name('menus.edit');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])
            ->middleware('admin.permission:menus.manage')
            ->name('menus.update');
        Route::post('/menus/{menu}/items', [MenuController::class, 'storeItem'])
            ->middleware('admin.permission:menus.manage')
            ->name('menus.items.store');
        Route::put('/menus/{menu}/items/{menuItem}', [MenuController::class, 'updateItem'])
            ->middleware('admin.permission:menus.manage')
            ->name('menus.items.update');
        Route::delete('/menus/{menu}/items/{menuItem}', [MenuController::class, 'destroyItem'])
            ->middleware('admin.permission:menus.manage')
            ->name('menus.items.destroy');
        Route::get('/pages', [PageController::class, 'index'])
            ->middleware('admin.permission:pages.view')
            ->name('pages.index');
        Route::get('/pages/create', [PageController::class, 'create'])
            ->middleware('admin.permission:pages.manage')
            ->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])
            ->middleware('admin.permission:pages.manage')
            ->name('pages.store');
        Route::get('/pages/{page}/edit', [PageController::class, 'edit'])
            ->middleware('admin.permission:pages.manage')
            ->name('pages.edit');
        Route::put('/pages/{page}', [PageController::class, 'update'])
            ->middleware('admin.permission:pages.manage')
            ->name('pages.update');
        Route::delete('/pages/{page}', [PageController::class, 'destroy'])
            ->middleware('admin.permission:pages.manage')
            ->name('pages.destroy');
        Route::get('/news', [NewsArticleController::class, 'index'])
            ->middleware('admin.permission:news.view')
            ->name('news.index');
        Route::get('/news/create', [NewsArticleController::class, 'create'])
            ->middleware('admin.permission:news.manage')
            ->name('news.create');
        Route::post('/news', [NewsArticleController::class, 'store'])
            ->middleware('admin.permission:news.manage')
            ->name('news.store');
        Route::get('/news/{newsArticle}/edit', [NewsArticleController::class, 'edit'])
            ->middleware('admin.permission:news.manage')
            ->name('news.edit');
        Route::put('/news/{newsArticle}', [NewsArticleController::class, 'update'])
            ->middleware('admin.permission:news.manage')
            ->name('news.update');
        Route::delete('/news/{newsArticle}', [NewsArticleController::class, 'destroy'])
            ->middleware('admin.permission:news.manage')
            ->name('news.destroy');
        Route::get('/product-categories', [ProductCategoryController::class, 'index'])
            ->middleware('admin.permission:product_categories.view')
            ->name('product-categories.index');
        Route::get('/product-categories/create', [ProductCategoryController::class, 'create'])
            ->middleware('admin.permission:product_categories.manage')
            ->name('product-categories.create');
        Route::post('/product-categories', [ProductCategoryController::class, 'store'])
            ->middleware('admin.permission:product_categories.manage')
            ->name('product-categories.store');
        Route::get('/product-categories/{productCategory}/edit', [ProductCategoryController::class, 'edit'])
            ->middleware('admin.permission:product_categories.manage')
            ->name('product-categories.edit');
        Route::put('/product-categories/{productCategory}', [ProductCategoryController::class, 'update'])
            ->middleware('admin.permission:product_categories.manage')
            ->name('product-categories.update');
        Route::delete('/product-categories/{productCategory}', [ProductCategoryController::class, 'destroy'])
            ->middleware('admin.permission:product_categories.manage')
            ->name('product-categories.destroy');
        Route::get('/products', [ProductController::class, 'index'])
            ->middleware('admin.permission:products.view')
            ->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])
            ->middleware('admin.permission:products.manage')
            ->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])
            ->middleware('admin.permission:products.manage')
            ->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
            ->middleware('admin.permission:products.manage')
            ->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])
            ->middleware('admin.permission:products.manage')
            ->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])
            ->middleware('admin.permission:products.manage')
            ->name('products.destroy');
        Route::get('/store-orders', [StoreOrderController::class, 'index'])
            ->middleware('admin.permission:store_orders.view')
            ->name('store-orders.index');
        Route::get('/store-orders/{storeOrder}', [StoreOrderController::class, 'show'])
            ->middleware('admin.permission:store_orders.view')
            ->name('store-orders.show');
        Route::get('/match-events', [MatchEventController::class, 'index'])
            ->middleware('admin.permission:match_events.view')
            ->name('match-events.index');
        Route::get('/match-events/create', [MatchEventController::class, 'create'])
            ->middleware('admin.permission:match_events.manage')
            ->name('match-events.create');
        Route::post('/match-events', [MatchEventController::class, 'store'])
            ->middleware('admin.permission:match_events.manage')
            ->name('match-events.store');
        Route::get('/match-events/{matchEvent}/edit', [MatchEventController::class, 'edit'])
            ->middleware('admin.permission:match_events.manage')
            ->name('match-events.edit');
        Route::put('/match-events/{matchEvent}', [MatchEventController::class, 'update'])
            ->middleware('admin.permission:match_events.manage')
            ->name('match-events.update');
        Route::delete('/match-events/{matchEvent}', [MatchEventController::class, 'destroy'])
            ->middleware('admin.permission:match_events.manage')
            ->name('match-events.destroy');
        Route::get('/match-events/{matchEvent}/ticket-zones', [TicketZoneController::class, 'index'])
            ->middleware('admin.permission:ticket_zones.view')
            ->name('ticket-zones.index');
        Route::get('/match-events/{matchEvent}/ticket-zones/create', [TicketZoneController::class, 'create'])
            ->middleware('admin.permission:ticket_zones.manage')
            ->name('ticket-zones.create');
        Route::post('/match-events/{matchEvent}/ticket-zones', [TicketZoneController::class, 'store'])
            ->middleware('admin.permission:ticket_zones.manage')
            ->name('ticket-zones.store');
        Route::get('/match-events/{matchEvent}/ticket-zones/{ticketZone}/edit', [TicketZoneController::class, 'edit'])
            ->middleware('admin.permission:ticket_zones.manage')
            ->name('ticket-zones.edit');
        Route::put('/match-events/{matchEvent}/ticket-zones/{ticketZone}', [TicketZoneController::class, 'update'])
            ->middleware('admin.permission:ticket_zones.manage')
            ->name('ticket-zones.update');
        Route::delete('/match-events/{matchEvent}/ticket-zones/{ticketZone}', [TicketZoneController::class, 'destroy'])
            ->middleware('admin.permission:ticket_zones.manage')
            ->name('ticket-zones.destroy');
        Route::get('/payment-settings', [PaymentSettingController::class, 'edit'])
            ->middleware('admin.permission:payment_settings.view')
            ->name('payment-settings.edit');
        Route::put('/payment-settings', [PaymentSettingController::class, 'update'])
            ->middleware('admin.permission:payment_settings.update')
            ->name('payment-settings.update');
        Route::get('/membership-orders', [MembershipOrderController::class, 'index'])
            ->middleware('admin.permission:membership_orders.view')
            ->name('membership-orders.index');
        Route::get('/membership-orders/{membershipOrder}', [MembershipOrderController::class, 'show'])
            ->middleware('admin.permission:membership_orders.view')
            ->name('membership-orders.show');
        Route::get('/membership-plans', [MembershipPlanController::class, 'index'])
            ->middleware('admin.permission:membership_plans.view')
            ->name('membership-plans.index');
        Route::get('/membership-plans/create', [MembershipPlanController::class, 'create'])
            ->middleware('admin.permission:membership_plans.manage')
            ->name('membership-plans.create');
        Route::post('/membership-plans', [MembershipPlanController::class, 'store'])
            ->middleware('admin.permission:membership_plans.manage')
            ->name('membership-plans.store');
        Route::get('/membership-plans/{membershipPlan}/edit', [MembershipPlanController::class, 'edit'])
            ->middleware('admin.permission:membership_plans.manage')
            ->name('membership-plans.edit');
        Route::put('/membership-plans/{membershipPlan}', [MembershipPlanController::class, 'update'])
            ->middleware('admin.permission:membership_plans.manage')
            ->name('membership-plans.update');
        Route::delete('/membership-plans/{membershipPlan}', [MembershipPlanController::class, 'destroy'])
            ->middleware('admin.permission:membership_plans.manage')
            ->name('membership-plans.destroy');
        Route::get('/payments', [PaymentMonitorController::class, 'index'])
            ->middleware('admin.permission:payments.view')
            ->name('payments.index');
        Route::get('/payments/{payment}', [PaymentMonitorController::class, 'show'])
            ->middleware('admin.permission:payments.view')
            ->name('payments.show');
        Route::get('/payment-events', [PaymentEventController::class, 'index'])
            ->middleware('admin.permission:payment_events.view')
            ->name('payment-events.index');
        Route::get('/payment-events/{paymentEvent}', [PaymentEventController::class, 'show'])
            ->middleware('admin.permission:payment_events.view')
            ->name('payment-events.show');
        Route::get('/ticket-orders', [TicketOrderController::class, 'index'])
            ->middleware('admin.permission:ticket_orders.view')
            ->name('ticket-orders.index');
        Route::get('/ticket-orders/{ticketOrder}', [TicketOrderController::class, 'show'])
            ->middleware('admin.permission:ticket_orders.view')
            ->name('ticket-orders.show');
        Route::get('/bus-trips', [BusTripController::class, 'index'])
            ->middleware('admin.permission:expeditions.view')
            ->name('bus-trips.index');
        Route::get('/bus-trips/create', [BusTripController::class, 'create'])
            ->middleware('admin.permission:expeditions.manage')
            ->name('bus-trips.create');
        Route::post('/bus-trips', [BusTripController::class, 'store'])
            ->middleware('admin.permission:expeditions.manage')
            ->name('bus-trips.store');
        Route::get('/bus-trips/{busTrip}/edit', [BusTripController::class, 'edit'])
            ->middleware('admin.permission:expeditions.manage')
            ->name('bus-trips.edit');
        Route::put('/bus-trips/{busTrip}', [BusTripController::class, 'update'])
            ->middleware('admin.permission:expeditions.manage')
            ->name('bus-trips.update');
        Route::delete('/bus-trips/{busTrip}', [BusTripController::class, 'destroy'])
            ->middleware('admin.permission:expeditions.manage')
            ->name('bus-trips.destroy');
        Route::get('/fanfest-events', [FanFestEventController::class, 'index'])
            ->middleware('admin.permission:fanfest.view')
            ->name('fanfest-events.index');
        Route::get('/fanfest-events/create', [FanFestEventController::class, 'create'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.create');
        Route::post('/fanfest-events', [FanFestEventController::class, 'store'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.store');
        Route::get('/fanfest-events/{fanFestEvent}/edit', [FanFestEventController::class, 'edit'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.edit');
        Route::put('/fanfest-events/{fanFestEvent}', [FanFestEventController::class, 'update'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.update');
        Route::delete('/fanfest-events/{fanFestEvent}', [FanFestEventController::class, 'destroy'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.destroy');
        Route::get('/fanfest-events/{fanFestEvent}/zones', [FanFestZoneController::class, 'index'])
            ->middleware('admin.permission:fanfest.view')
            ->name('fanfest-events.zones.index');
        Route::get('/fanfest-events/{fanFestEvent}/zones/create', [FanFestZoneController::class, 'create'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.zones.create');
        Route::post('/fanfest-events/{fanFestEvent}/zones', [FanFestZoneController::class, 'store'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.zones.store');
        Route::get('/fanfest-events/{fanFestEvent}/zones/{fanFestZone}/edit', [FanFestZoneController::class, 'edit'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.zones.edit');
        Route::put('/fanfest-events/{fanFestEvent}/zones/{fanFestZone}', [FanFestZoneController::class, 'update'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.zones.update');
        Route::delete('/fanfest-events/{fanFestEvent}/zones/{fanFestZone}', [FanFestZoneController::class, 'destroy'])
            ->middleware('admin.permission:fanfest.manage')
            ->name('fanfest-events.zones.destroy');
        Route::get('/board-members', [BoardMemberController::class, 'index'])
            ->middleware('admin.permission:board_members.view')
            ->name('board-members.index');
        Route::get('/board-members/create', [BoardMemberController::class, 'create'])
            ->middleware('admin.permission:board_members.manage')
            ->name('board-members.create');
        Route::post('/board-members', [BoardMemberController::class, 'store'])
            ->middleware('admin.permission:board_members.manage')
            ->name('board-members.store');
        Route::get('/board-members/{boardMember}/edit', [BoardMemberController::class, 'edit'])
            ->middleware('admin.permission:board_members.manage')
            ->name('board-members.edit');
        Route::put('/board-members/{boardMember}', [BoardMemberController::class, 'update'])
            ->middleware('admin.permission:board_members.manage')
            ->name('board-members.update');
        Route::delete('/board-members/{boardMember}', [BoardMemberController::class, 'destroy'])
            ->middleware('admin.permission:board_members.manage')
            ->name('board-members.destroy');
        Route::get('/sponsors', [SponsorController::class, 'index'])
            ->middleware('admin.permission:sponsors.view')
            ->name('sponsors.index');
        Route::get('/sponsors/create', [SponsorController::class, 'create'])
            ->middleware('admin.permission:sponsors.manage')
            ->name('sponsors.create');
        Route::post('/sponsors', [SponsorController::class, 'store'])
            ->middleware('admin.permission:sponsors.manage')
            ->name('sponsors.store');
        Route::get('/sponsors/{sponsor}/edit', [SponsorController::class, 'edit'])
            ->middleware('admin.permission:sponsors.manage')
            ->name('sponsors.edit');
        Route::put('/sponsors/{sponsor}', [SponsorController::class, 'update'])
            ->middleware('admin.permission:sponsors.manage')
            ->name('sponsors.update');
        Route::delete('/sponsors/{sponsor}', [SponsorController::class, 'destroy'])
            ->middleware('admin.permission:sponsors.manage')
            ->name('sponsors.destroy');
        Route::get('/players', [PlayerController::class, 'index'])
            ->middleware('admin.permission:players.view')
            ->name('players.index');
        Route::get('/players/create', [PlayerController::class, 'create'])
            ->middleware('admin.permission:players.manage')
            ->name('players.create');
        Route::post('/players', [PlayerController::class, 'store'])
            ->middleware('admin.permission:players.manage')
            ->name('players.store');
        Route::get('/players/{player}/edit', [PlayerController::class, 'edit'])
            ->middleware('admin.permission:players.manage')
            ->name('players.edit');
        Route::put('/players/{player}', [PlayerController::class, 'update'])
            ->middleware('admin.permission:players.manage')
            ->name('players.update');
        Route::delete('/players/{player}', [PlayerController::class, 'destroy'])
            ->middleware('admin.permission:players.manage')
            ->name('players.destroy');
        Route::get('/staff-members', [StaffMemberController::class, 'index'])
            ->middleware('admin.permission:staff.view')
            ->name('staff-members.index');
        Route::get('/staff-members/create', [StaffMemberController::class, 'create'])
            ->middleware('admin.permission:staff.manage')
            ->name('staff-members.create');
        Route::post('/staff-members', [StaffMemberController::class, 'store'])
            ->middleware('admin.permission:staff.manage')
            ->name('staff-members.store');
        Route::get('/staff-members/{staffMember}/edit', [StaffMemberController::class, 'edit'])
            ->middleware('admin.permission:staff.manage')
            ->name('staff-members.edit');
        Route::put('/staff-members/{staffMember}', [StaffMemberController::class, 'update'])
            ->middleware('admin.permission:staff.manage')
            ->name('staff-members.update');
        Route::delete('/staff-members/{staffMember}', [StaffMemberController::class, 'destroy'])
            ->middleware('admin.permission:staff.manage')
            ->name('staff-members.destroy');
        Route::get('/issued-tickets', [IssuedTicketController::class, 'index'])
            ->middleware('admin.permission:issued_tickets.view')
            ->name('issued-tickets.index');
        Route::get('/issued-tickets/{issuedTicket}', [IssuedTicketController::class, 'show'])
            ->middleware('admin.permission:issued_tickets.view')
            ->name('issued-tickets.show');
        Route::post('/tickets/validate', [IssuedTicketController::class, 'validateTicket'])
            ->middleware('admin.permission:issued_tickets.validate')
            ->name('tickets.validate');

        // Stadium
        Route::get('/stadium', [StadiumController::class, 'index'])
            ->middleware('admin.permission:stadium.view')
            ->name('stadium.index');
        Route::get('/stadium/create', [StadiumController::class, 'create'])
            ->middleware('admin.permission:stadium.manage')
            ->name('stadium.create');
        Route::post('/stadium', [StadiumController::class, 'store'])
            ->middleware('admin.permission:stadium.manage')
            ->name('stadium.store');
        Route::get('/stadium/{stadium}/edit', [StadiumController::class, 'edit'])
            ->middleware('admin.permission:stadium.manage')
            ->name('stadium.edit');
        Route::put('/stadium/{stadium}', [StadiumController::class, 'update'])
            ->middleware('admin.permission:stadium.manage')
            ->name('stadium.update');
        Route::delete('/stadium/{stadium}', [StadiumController::class, 'destroy'])
            ->middleware('admin.permission:stadium.manage')
            ->name('stadium.destroy');

        // Clubs
        Route::get('/clubs', [ClubController::class, 'index'])
            ->middleware('admin.permission:clubs.view')
            ->name('clubs.index');
        Route::get('/clubs/create', [ClubController::class, 'create'])
            ->middleware('admin.permission:clubs.manage')
            ->name('clubs.create');
        Route::post('/clubs', [ClubController::class, 'store'])
            ->middleware('admin.permission:clubs.manage')
            ->name('clubs.store');
        Route::get('/clubs/{club}/edit', [ClubController::class, 'edit'])
            ->middleware('admin.permission:clubs.manage')
            ->name('clubs.edit');
        Route::put('/clubs/{club}', [ClubController::class, 'update'])
            ->middleware('admin.permission:clubs.manage')
            ->name('clubs.update');
        Route::delete('/clubs/{club}', [ClubController::class, 'destroy'])
            ->middleware('admin.permission:clubs.manage')
            ->name('clubs.destroy');

        // Match Goals (nested under match-events)
        Route::get('/match-events/{matchEvent}/goals', [MatchGoalController::class, 'index'])
            ->middleware('admin.permission:match_events.view')
            ->name('match-events.goals.index');
        Route::get('/match-events/{matchEvent}/goals/create', [MatchGoalController::class, 'create'])
            ->middleware('admin.permission:match_goals.manage')
            ->name('match-events.goals.create');
        Route::post('/match-events/{matchEvent}/goals', [MatchGoalController::class, 'store'])
            ->middleware('admin.permission:match_goals.manage')
            ->name('match-events.goals.store');
        Route::get('/match-events/{matchEvent}/goals/{goal}/edit', [MatchGoalController::class, 'edit'])
            ->middleware('admin.permission:match_goals.manage')
            ->name('match-events.goals.edit');
        Route::put('/match-events/{matchEvent}/goals/{goal}', [MatchGoalController::class, 'update'])
            ->middleware('admin.permission:match_goals.manage')
            ->name('match-events.goals.update');
        Route::delete('/match-events/{matchEvent}/goals/{goal}', [MatchGoalController::class, 'destroy'])
            ->middleware('admin.permission:match_goals.manage')
            ->name('match-events.goals.destroy');

        // League Standings
        Route::get('/standings', [LeagueStandingController::class, 'index'])
            ->middleware('admin.permission:standings.view')
            ->name('standings.index');
        Route::get('/standings/create', [LeagueStandingController::class, 'create'])
            ->middleware('admin.permission:standings.manage')
            ->name('standings.create');
        Route::post('/standings', [LeagueStandingController::class, 'store'])
            ->middleware('admin.permission:standings.manage')
            ->name('standings.store');
        Route::get('/standings/{standing}/edit', [LeagueStandingController::class, 'edit'])
            ->middleware('admin.permission:standings.manage')
            ->name('standings.edit');
        Route::put('/standings/{standing}', [LeagueStandingController::class, 'update'])
            ->middleware('admin.permission:standings.manage')
            ->name('standings.update');
        Route::delete('/standings/{standing}', [LeagueStandingController::class, 'destroy'])
            ->middleware('admin.permission:standings.manage')
            ->name('standings.destroy');
    });
