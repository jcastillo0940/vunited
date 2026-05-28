# Frontend QA Checklist

Fecha: 2026-05-27

## Shell publico

- [x] Todas las rutas publicas activas usan `AppLayout`
- [x] `TopTicker` visible en shell publico
- [x] `MainNavbar` visible en shell publico
- [x] `Footer` visible en shell publico
- [x] `siteService` cargado en rutas publicas activas
- [x] `menuService` cargado en rutas publicas activas
- [x] fallback navigation coherente configurado en `publicNavigation.js`

## Rutas publicas activas

- [x] `/`
- [x] `/style-guide`
- [x] `/noticias`
- [x] `/noticias/{slug}`
- [x] `/pagina/{slug}`
- [x] `/patrocinadores`
- [x] `/plantilla`
- [x] `/jugadores/{slug}`
- [x] `/fuerzas-basicas`
- [x] `/pruebas`
- [x] `/directiva`

## Navegacion

- [x] Header principal con rutas reales activas
- [x] Grupo `El Club` visible en navbar desktop/mobile
- [x] CTA principal marcado como pendiente controlado
- [x] Footer alineado con rutas reales o pendientes documentados
- [x] Links globales sin `href="#"` silencioso
- [x] Links publicos de noticias usan `/noticias/{slug}`

## Estado de contenido

- [x] Rutas con API real documentadas
- [x] Rutas con mock documentadas
- [x] Rutas futuras pendientes documentadas

## QA tecnico

- [x] `npm run build`
- [x] `php artisan test`
- [x] `php artisan route:list`
- [x] `/` responde `200`
- [x] `/style-guide` responde `200`
- [x] `/noticias` responde `200`
- [x] `/patrocinadores` responde `200`
- [x] `/plantilla` responde `200`
- [x] `/jugadores/alexis-canto` responde `200`
- [x] `/fuerzas-basicas` responde `200`
- [x] `/pruebas` responde `200`
- [x] `/directiva` responde `200`

## Nota Vite local

- [x] mitigacion de manifest Vite revisada
- [x] fallback relativo/absoluto documentado para entorno local Windows
- [x] no bloquea continuar con pantallas visuales mock

## Pendientes de QA manual

- [ ] comparar navbar desktop con Stitch en todas las paginas
- [ ] revisar navbar mobile y dropdown `El Club`
- [ ] revisar CTA principal pendiente para asegurar que no confunda
- [ ] revisar footer en todas las paginas
- [ ] revisar consistencia del ticker en todas las rutas
- [ ] validar visualmente estados hover de cards y links
