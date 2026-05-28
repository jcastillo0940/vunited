<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @routes
        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath)
                ? json_decode(file_get_contents($manifestPath), true) ?? []
                : [];

            $relativeAppEntry = 'resources/js/app.jsx';
            $absoluteAppEntry = str_replace('\\', '/', resource_path('js/app.jsx'));
            $viteAppEntry = array_key_exists($relativeAppEntry, $manifest)
                ? $relativeAppEntry
                : (array_key_exists($absoluteAppEntry, $manifest) ? $absoluteAppEntry : $relativeAppEntry);

            $relativePageEntry = "resources/js/Pages/{$page['component']}.jsx";
            $absolutePageEntry = str_replace('\\', '/', resource_path("js/Pages/{$page['component']}.jsx"));
            $vitePageEntry = array_key_exists($relativePageEntry, $manifest)
                ? $relativePageEntry
                : (array_key_exists($absolutePageEntry, $manifest) ? $absolutePageEntry : $relativePageEntry);
        @endphp
        @viteReactRefresh
        @vite([$viteAppEntry, $vitePageEntry])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
