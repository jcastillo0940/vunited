# Fase 3 — Builds y verificación de nginx sirviendo estáticos

## Builds

`web/frontend`, `store/frontend` y `ticketing/frontend` tienen `package.json`,
`vite.config.ts`, `tsconfig.json`, `eslint.config.js` y `vitest.config.ts`
**independientes**. Cada `npm run build` corre `tsc --noEmit` + `vite build`
(y en `web/frontend`, además genera `robots.txt`/`sitemap.xml` antes del
build). Salida en `dist/` de cada paquete, con `base: '/builds/'`.

## Despliegue a los directorios de Fase 2

El `dist/` de cada frontend se copió a
`/var/www/veraguas-<servicio>/builds/` (creado en Fase 2, ya con ACL para que
`www-data` pueda leer sin necesitar acceso al resto del árbol del servicio).
Este es un despliegue manual de verificación para esta fase — el mecanismo
repetible es `infrastructure/scripts/build.sh` + `deploy.sh` de Fase 2 (que
ya soportan `npm run build` si el `package.json` de la release lo define).

## Bug real encontrado y corregido

El location `/builds/` de Fase 2 aplicaba
`Cache-Control: public, max-age=31536000, immutable` a **todo** dentro de
`builds/`, incluyendo `index.html`, `robots.txt` y `sitemap.xml` — no solo a
los archivos con hash de Vite. Eso habría dejado a los usuarios con un
`index.html` viejo en caché durante un año después de cada deploy, sin
enterarse nunca de una release nueva (los assets con hash sí pueden
cachearse para siempre porque su nombre cambia; `index.html` no cambia de
nombre nunca).

Corregido dividiendo el location en dos, en los 3 vhosts
(`veraguas-web.conf`, `veraguas-store.conf`, `veraguas-ticketing.conf`):

```
location ^~ /builds/assets/ { ... Cache-Control: public, max-age=31536000, immutable ... }
location ^~ /builds/        { ... Cache-Control: no-cache ... }
```

## Verificación

```
GET /builds/index.html      -> HTTP 200, Cache-Control: no-cache
GET /builds/assets/*.js     -> HTTP 200, Cache-Control: public, max-age=31536000, immutable
GET /builds/robots.txt      -> HTTP 200 (contenido correcto por ambiente)
```

Confirmado para los 3 frontends (web/store/ticketing). `ss -tlnp` confirma
que los 6 puertos de Fase 2 (8081-8086) siguen únicamente en `127.0.0.1`
después de este cambio. Producción (`https://united.wp-pa.com/`) verificada
en HTTP 200 antes y después del reload de nginx.

## Nota sobre el corte de tráfico

Los vhosts de Fase 2 siguen sirviendo el bootstrap PHP (JSON de
mantenimiento, HTTP 503) en `/` — **no se cambió el enrutamiento para que
`/` sirva el `index.html` de la SPA nueva**. Igual que con el gateway en
Fase 2, activar ese corte es una decisión de despliegue separada (requiere
decidir si Laravel deja de renderizar esas rutas), no algo que deba pasar
implícitamente al construir el frontend.
