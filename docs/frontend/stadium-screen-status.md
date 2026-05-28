# Stadium Screen Status

Fecha: 2026-05-27

## Status

DONE — pantalla /estadio verificada, construida y funcional como pantalla visual mock.

---

## Archivos verificados

### Página principal

- `resources/js/Pages/Public/Stadium.jsx`
  - carga shell via `siteService` y `menuService`
  - fallback local coherente via `fallbackSettings`
  - usa `homeMock.ticker` para el ticker global
  - renderiza los 7 componentes de stadiumMock

### Mock de datos

- `resources/js/mocks/stadiumMock.js`
  - hero: título, highlight, descripción, imageUrl (Unsplash)
  - info: nombre, subtítulo, ubicación, capacidad, dirección, venueType, CTA "COMO LLEGAR"
  - map: título, descripción, pinLabel, CTA "ABRIR EN GOOGLE MAPS"
  - zones: 4 zonas (general, preferencial, vip, visitante)
  - matchday: 5 items con icon Material Symbols, título, descripción
  - rules: 4 cadenas de recomendaciones
  - cta: título, descripción, actionLabel "COMPRAR BOLETOS", actionHref "/boletos"

### Componentes

- `resources/js/Components/stadium/StadiumHero.jsx`
  - hero full-width con imagen overlay, gradiente primary, título + highlight accent, descripción con border-l accent
- `resources/js/Components/stadium/StadiumInfo.jsx`
  - grid 4 columnas con datos del estadio + CTA "Cómo llegar" a Google Maps
- `resources/js/Components/stadium/StadiumMap.jsx`
  - placeholder visual de mapa con grid background, pin icon, botón Google Maps externo
- `resources/js/Components/stadium/StadiumZones.jsx`
  - 4 cards de zonas con badge, icono, descripción y feature highlight
- `resources/js/Components/stadium/MatchdayExperience.jsx`
  - 5 cards con iconos Material Symbols para experiencia matchday
- `resources/js/Components/stadium/StadiumRules.jsx`
  - grid 2 col de reglas con icono `verified`, fondo primary
- `resources/js/Components/stadium/StadiumCTA.jsx`
  - bloque CTA sobre fondo primary, apunta a /boletos

### Infraestructura

- `routes/web.php` línea 91
  - `Route::get('/estadio', ...)` nombre `stadium.index` → `Inertia::render('Public/Stadium')`

- `resources/js/config/publicNavigation.js`
  - "Estadio" aparece en `buildPublicHeaderLinks` con url `/estadio`
  - "Estadio" aparece en `buildPublicFooterLinks` con url `/estadio`

---

## Comandos ejecutados y resultados

### npm run build

```
vite v7.3.3 building client environment for production...
✓ 1144 modules transformed.
Stadium-DAXW1TlL.js  14.06 kB │ gzip: 4.23 kB
✓ built in 2.59s
```

Resultado: OK — sin errores, sin warnings, Stadium bundle generado correctamente.

### php artisan test

```
Tests:    81 passed (302 assertions)
Duration: 5.19s
```

Resultado: OK — 81 tests, 302 assertions, todos verdes.

### php artisan route:list | grep estadio

```
GET|HEAD  estadio ........... stadium.index › routes/web.php:91
```

Resultado: OK — ruta registrada correctamente con nombre y referencia de archivo.

### Smoke HTTP (servidor local puerto 8765)

| URL | HTTP |
|---|---|
| / | 200 |
| /calendario | 200 |
| /boletos | 200 |
| /estadio | 200 |

Resultado: OK — todos los endpoints responden 200.

---

## Confirmaciones

- No hay mapa real integrado. StadiumMap es un placeholder visual con grid CSS y pin icon.
- No hay ticketing real. El botón "COMPRAR BOLETOS" apunta a /boletos que también es mock visual.
- No hay pagos. No se invoca ningún endpoint de pago ni PayPal.
- No hay PayPal. No existe ninguna referencia a SDK, credencial ni flujo PayPal.
- No se tocó backend Laravel funcional. Zero cambios en controllers, models o servicios.
- No se crearon migraciones.
- No se tocó auth, admin, guards ni permisos.
- Todo el contenido es mock/local via stadiumMock.js.

---

## Riesgos visuales

### Pendientes de revisión manual en browser

- Verificar que las 7 secciones rendericen correctamente en desktop y mobile.
- El componente `MatchdayExperience` usa `xl:grid-cols-5` para 5 cards — en viewports intermedios puede quedar menos legible. Revisar breakpoints.
- El `StadiumHero` tiene imagen de Unsplash (`photo-1574629810360-7efbbe195018`). Si el entorno no tiene acceso a internet, el hero queda en fondo primary sólido — aceptable visualmente.
- El `StadiumInfo` y `StadiumMap` tienen botones que apuntan a `https://maps.google.com` sin coordenadas reales del Estadio Atalaya. Son CTAs visuales funcionales.
- El `StadiumCTA` muestra la nota "CTA visual. Sin ticketing real ni cobro activo en esta fase." — confirmar que el copy es apropiado para la fase actual.

### Dependencias externas visuales

- Imagen hero: URL Unsplash — carga en producción, puede fallar en entornos sin internet.
- Google Maps: enlace genérico, no apunta a las coordenadas reales del estadio.
- Material Symbols: cargados via Google Fonts en `resources/css/app.css` — requieren internet para carga inicial.

---

## Dependencias bloqueadas

Las siguientes capacidades siguen bloqueadas y no forman parte de esta pantalla:

- Mapa real (Google Maps Embed o similar)
- Ticketing real con QR
- Reserva de zona real
- Payment Foundation
- PayPal

Ver: `docs/payments/paypal-future-integration.md`
