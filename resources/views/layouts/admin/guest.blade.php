<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin') — Veraguas United</title>
        <style>
            *, *::before, *::after { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #0f172a;
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: #0f172a;
            }

            .guest-card {
                width: 100%;
                max-width: 440px;
                margin: 2rem 1rem;
                background: #ffffff;
                border-radius: 1.25rem;
                padding: 2.5rem 2rem;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            }

            input[type="email"],
            input[type="password"] {
                display: block;
                width: 100%;
                border: 1px solid #cbd5e1;
                border-radius: 0.75rem;
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                outline: none;
                transition: border-color 0.15s;
            }

            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: #1D428A;
            }

            .guest-submit {
                display: flex;
                width: 100%;
                align-items: center;
                justify-content: center;
                border: none;
                border-radius: 0.75rem;
                background: #0f172a;
                color: #fff;
                padding: 0.85rem 1rem;
                font-size: 0.875rem;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.15s;
            }

            .guest-submit:hover { background: #1e293b; }

            label { display: block; margin-bottom: 0.35rem; font-size: 0.875rem; font-weight: 500; color: #374151; }

            .field { margin-bottom: 1rem; }

            .error-box {
                margin-bottom: 1rem;
                border: 1px solid #fca5a5;
                background: #fef2f2;
                border-radius: 0.75rem;
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                color: #991b1b;
            }
        </style>
    </head>
    <body>
        <div class="guest-card">
            @yield('content')
        </div>
    </body>
</html>
