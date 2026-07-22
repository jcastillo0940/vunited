// Service worker minimo: cachea el app shell para que /escaner cargue sin
// red. Las llamadas a /api/* NUNCA se sirven desde cache (los datos de
// validacion tienen que ser reales o quedar en la cola offline explicita
// del propio Scanner.tsx, no un cache silencioso que podria mentir sobre
// el estado real de un boleto).
const CACHE_NAME = 'veraguas-ticketing-shell-v2';
const SHELL_PATHS = ['/', '/escaner', '/escaner/login', '/manifest.json'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(SHELL_PATHS)).catch(() => null),
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))),
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    if (url.pathname.startsWith('/api/')) {
        return; // nunca cachear respuestas de la API
    }

    event.respondWith(
        caches.match(event.request).then((cached) => cached ?? fetch(event.request)),
    );
});
