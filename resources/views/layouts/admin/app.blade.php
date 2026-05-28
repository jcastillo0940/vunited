<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin')</title>
        <style>
            body {
                margin: 0;
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                background: #f1f5f9;
                color: #0f172a;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .admin-shell {
                min-height: 100vh;
                display: grid;
                grid-template-columns: 240px 1fr;
            }

            .admin-sidebar {
                background: #0f172a;
                color: #e2e8f0;
                padding: 1.5rem 1rem;
            }

            .admin-sidebar h1 {
                margin: 0 0 1.5rem;
                font-size: 1rem;
            }

            .admin-sidebar nav {
                display: grid;
                gap: 0.5rem;
            }

            .admin-sidebar a {
                display: block;
                padding: 0.75rem 0.9rem;
                border-radius: 0.6rem;
                background: rgba(148, 163, 184, 0.08);
            }

            .admin-main {
                padding: 2rem;
            }

            .admin-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: 2rem;
            }

            .admin-card {
                background: #ffffff;
                border: 1px solid #dbe4ee;
                border-radius: 1rem;
                padding: 1.5rem;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            }

            .admin-button {
                border: 1px solid #cbd5e1;
                background: #ffffff;
                color: #0f172a;
                border-radius: 0.7rem;
                padding: 0.7rem 1rem;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="admin-shell">
            <aside class="admin-sidebar">
                <h1>Veraguas United Admin</h1>

                <nav>
                    @auth('admin')
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.admin-users.index') }}">Admin Users</a>
                    <a href="{{ route('admin.roles.index') }}">Roles</a>
                    <a href="{{ route('admin.settings.edit') }}">Settings</a>
                    <a href="{{ route('admin.menus.index') }}">Menus</a>
                    <a href="{{ route('admin.pages.index') }}">Pages</a>
                    <a href="{{ route('admin.news.index') }}">News</a>
                        @if(auth('admin')->user()->hasPermission('product_categories.view'))
                            <a href="{{ route('admin.product-categories.index') }}">Product Categories</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('products.view'))
                            <a href="{{ route('admin.products.index') }}">Products</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('store_orders.view'))
                            <a href="{{ route('admin.store-orders.index') }}">Store Orders</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('match_events.view'))
                            <a href="{{ route('admin.match-events.index') }}">Match Events</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('ticket_zones.view'))
                            <a href="{{ route('admin.match-events.index') }}">Ticket Zones</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('standings.view'))
                            <a href="{{ route('admin.standings.index') }}">Tabla de posiciones</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('payment_settings.view'))
                            <a href="{{ route('admin.payment-settings.edit') }}">Payment Settings</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('membership_orders.view'))
                            <a href="{{ route('admin.membership-orders.index') }}">Membership Orders</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('membership_plans.view'))
                            <a href="{{ route('admin.membership-plans.index') }}">Membership Plans</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('payments.view'))
                            <a href="{{ route('admin.payments.index') }}">Payments</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('payment_events.view'))
                            <a href="{{ route('admin.payment-events.index') }}">Payment Events</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('ticket_orders.view'))
                            <a href="{{ route('admin.ticket-orders.index') }}">Ticket Orders</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('issued_tickets.view'))
                            <a href="{{ route('admin.issued-tickets.index') }}">Issued Tickets</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('board_members.view'))
                            <a href="{{ route('admin.board-members.index') }}">Directiva</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('fanfest.view'))
                            <a href="{{ route('admin.fanfest-events.index') }}">FanFest</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('expeditions.view'))
                            <a href="{{ route('admin.bus-trips.index') }}">Expedición India</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('sponsors.view'))
                            <a href="{{ route('admin.sponsors.index') }}">Patrocinadores</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('players.view'))
                            <a href="{{ route('admin.players.index') }}">Jugadores</a>
                        @endif
                        @if(auth('admin')->user()->hasPermission('staff.view'))
                            <a href="{{ route('admin.staff-members.index') }}">Cuerpo Técnico</a>
                        @endif
                    @endauth
                </nav>
            </aside>

            <main class="admin-main">
                <div class="admin-topbar">
                    <div>
                        <strong>@yield('title', 'Admin')</strong>
                    </div>

                    @auth('admin')
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="admin-button">Logout</button>
                        </form>
                    @endauth
                </div>

                <div class="admin-card">
                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>
