# Fase 7 — Subdominio público real: boletos.wp-pa.com

## DNS ya apuntaba a esta VM

Confirmado antes de tocar nada: `boletos.wp-pa.com`, `tienda.wp-pa.com`,
`admin.wp-pa.com` y `united.wp-pa.com` resuelven todos a `34.58.185.9`, la
IP pública real de esta VM (`curl -4 ifconfig.me`). Solo `united.wp-pa.com`
tenía certificado emitido antes de esta fase.

`tienda.wp-pa.com` y `admin.wp-pa.com` **no se configuraron** en esta fase
(pertenecen a Store y Administración, fuera del alcance de "ejecuta
únicamente la Fase 7") — hoy caen en el `server` `default` de nginx
("Welcome to nginx!", inofensivo). Quedan listos para cuando toque esa
fase.

## Qué se hizo para boletos.wp-pa.com

1. Vhost nuevo `/etc/nginx/sites-available/boletos.wp-pa.com.conf`:
   - `root` en `/var/www/veraguas-ticketing/builds` (el SPA de
     `ticketing/frontend`, compilado y copiado ahí).
   - `location ^~ /api/` proxea al Laravel real de `ticketing/backend` vía
     el socket PHP-FPM aislado de Fase 2
     (`/var/www/veraguas-ticketing/sockets/php-fpm.sock`).
   - `location /` sirve el SPA con fallback a `index.html` (rutas del
     cliente como `/eventos/:id` no son archivos reales).
   - `/assets/` con cache inmutable de 1 año (hash de Vite);
     `index.html`/`sw.js` con `no-cache`.
2. **Certificado real** emitido con `certbot --nginx -d boletos.wp-pa.com`
   (autenticador+instalador nginx, el mismo mecanismo que ya usa
   `united.wp-pa.com` — sin tocar ese vhost). Redirección HTTP→HTTPS
   automática. Renovación automática ya programada por certbot (igual que
   el resto de certificados de esta VM).
3. HSTS habilitado explícitamente para este dominio (certificado real, a
   diferencia de los vhosts internos autofirmados de Fase 2).
4. Se desplegó el backend real (no el bootstrap de Fase 2) a través del
   pipeline de Fase 2 (`infrastructure/scripts/deploy.sh ticketing
   ticketing/backend`): build (`composer install`), activar release,
   reiniciar worker/scheduler, verificar salud — 7/7 checks en verde.

## Dos bugs reales de nginx encontrados y corregidos en el camino

1. **PHP-FPM sirvió la release anterior varios minutos después de
   activar la nueva.** `realpath_cache` de PHP cachea a qué inodo resuelve
   un symlink; cambiar `current` no invalida ese cache por sí solo.
   Corregido agregando `systemctl reload php8.3-fpm` dentro de
   `infrastructure/scripts/activate-release.sh` (Fase 2) — beneficia a
   **todos** los servicios, no solo Ticketing, para cualquier deploy futuro.
2. **`add_header` no se hereda si el mismo `location` define su propio
   `add_header`.** Los locations `/assets/`, `/sw.js` y `/` tenían su
   propio `Cache-Control`, lo que silenciosamente anulaba TODAS las
   cabeceras de seguridad heredadas del `server` (nosniff, CSP,
   Referrer-Policy, etc. — ninguna aparecía en la respuesta). Corregido
   agregando `include veraguas-security-headers.conf;` dentro de cada uno
   de esos `location`, no solo a nivel `server`.

## Verificación end-to-end real contra el dominio público

```
GET  https://boletos.wp-pa.com/                       -> 200, SPA real
GET  https://boletos.wp-pa.com/api/events              -> 200, datos reales de MySQL
POST https://boletos.wp-pa.com/api/events/{id}/orders  -> 201, orden real (hold_active),
                                                            cupo reservado atomicamente
POST https://boletos.wp-pa.com/api/orders/{id}/payment -> 422 "No se pudo contactar a
                                                            Payments" (correcto: Payments
                                                            no existe todavia) -> orden
                                                            pasa a failed, cupo liberado
GET  https://boletos.wp-pa.com/api/orders/{id}         -> confirma status=failed
GET  https://boletos.wp-pa.com/api/events/{id}/zones   -> confirma capacity_available
                                                            de vuelta al valor original
http://boletos.wp-pa.com/                              -> 301 a https
Headers de https://boletos.wp-pa.com/ y /api/*          -> HSTS, CSP, nosniff,
                                                            Referrer-Policy, Permissions-Policy
https://united.wp-pa.com/                               -> 200 sin interrupcion en
                                                            ningun momento de esta fase
```

Se limpiaron los datos de estas pruebas (órdenes/holds de prueba borrados,
`capacity_available` de las 3 zonas restaurado a su total) antes de cerrar
la fase.
