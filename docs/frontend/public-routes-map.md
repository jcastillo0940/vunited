# Public Routes Map

Fecha: 2026-05-27

## Shell comun confirmado

Las siguientes rutas publicas renderizan con el mismo shell basado en `AppLayout`:

- `/`
- `/style-guide`
- `/noticias`
- `/noticias/{slug}`
- `/pagina/{slug}`
- `/patrocinadores`
- `/plantilla`
- `/jugadores/{slug}`
- `/fuerzas-basicas`
- `/pruebas`
- `/directiva`

Elementos comunes del shell:

- `TopTicker`
- `MainNavbar`
- `Footer`
- carga de `siteService`
- carga de `menuService` cuando el menu publico existe
- fallback navigation coherente via `resources/js/config/publicNavigation.js`

## Rutas reales con API

Estas pantallas consumen contenido real de Laravel:

### `/`

- shell: `siteService`, `menuService`
- contenido real: `newsService`
- contenido mock: ticker, resultado, proximo partido, tabla, tienda destacada, membresia, partners, cantera home

### `/noticias`

- shell: `siteService`, `menuService`
- contenido real: `newsService`

### `/noticias/{slug}`

- shell: `siteService`, `menuService`
- contenido real: `newsService`

### `/pagina/{slug}`

- shell: `siteService`, `menuService`
- contenido real: `pageService`

## Rutas visuales con mock

Estas pantallas son publicas y funcionales en frontend, pero su contenido principal sigue viniendo de mocks:

### `/patrocinadores`

- shell real: `siteService`, `menuService`
- contenido mock: `sponsorsMock`

### `/plantilla`

- shell real: `siteService`, `menuService`
- contenido mock: `playersMock`

### `/jugadores/{slug}`

- shell real: `siteService`, `menuService`
- contenido mock: `playersMock`

### `/fuerzas-basicas`

- shell real: `siteService`, `menuService`
- contenido mock: `academyMock`

### `/pruebas`

- shell real: `siteService`, `menuService`
- contenido mock: `tryoutsMock`

### `/directiva`

- shell real: `siteService`, `menuService`
- contenido mock: `boardMock`

## Rutas utilitarias

### `/style-guide`

- shell real: `siteService`, `menuService`
- pagina de QA visual y validacion de componentes

## Rutas completadas fase 2 (visual mock)

Estas rutas tienen pantalla publica implementada y verificada como mock visual:

- `/calendario` — `calendarMock.js`, shell real
- `/estadio` — `stadiumMock.js`, shell real — ver `docs/frontend/stadium-screen-status.md`
- `/tienda` — `productsMock.js`, shell real
- `/carrito` — `cartMock.js`, estado local
- `/fanclub` — `fanClubMock.js`, shell real
- `/registro-tribu` — `registerTribeMock.js`, shell real
- `/registro-confirmado` — `registrationConfirmationMock.js`, shell real
- `/boletos` — `ticketsMock.js`, shell real

## Rutas pendientes

Estas rutas aun no tienen pantalla publica implementada:

- `/mi-cuenta`
- `/fanfest`
- `/expedicion-india`

## Nota

Las rutas pendientes no se dejaron como links muertos globales. En la navegacion publica quedaron marcadas como pendientes cuando aplica, o documentadas para una fase posterior.

## Dependencias bloqueadas para rutas comerciales

Las rutas de comercio (`/boletos`, `/tienda`, `/carrito`, `/registro-tribu`) son visuales mock hasta que exista Payment Foundation.

Ver: `docs/payments/payment-foundation-technical-review.md`
