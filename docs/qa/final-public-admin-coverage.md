# Cobertura Final Público + Admin — Auditoría 2026-05-28

Auditoría de todas las páginas públicas en scope. Basada en lectura de código real.

---

## Tabla de cobertura por página

| Página | API consumida | Admin backoffice | Fallback mock | Estado |
|---|---|---|---|---|
| `/` | `/api/matches/featured`, `/api/matches?status=finished`, `/api/standings`, `/api/news`, `/api/site-settings`, `/api/menu/{loc}` | settings, news, match-events, standings | `homeMock.lastResult`, `homeMock.nextMatch`, `homeMock.standings`, noticias mock | ✓ Data-driven |
| `/calendario` | `/api/matches`, `/api/site-settings`, `/api/menu/{loc}` | admin/match-events | `calendarMock.matches`, `calendarMock.nextMatch`, `calendarMock.seasonSummary` (computado de API) | ✓ Data-driven |
| `/estadio` | `/api/stadium`, `/api/site-settings`, `/api/menu/{loc}` | admin/stadium | `stadiumMock` completo | ✓ Data-driven |
| `/plantilla` | `/api/players`, `/api/staff`, `/api/site-settings`, `/api/menu/{loc}` | admin/players, admin/staff-members | `playersMock` (hero, filters, squads, staff) | ✓ Parcial — datos de jugadores desde API; hero y filtros de mock |
| `/jugadores/{slug}` | `/api/players/{slug}`, `/api/site-settings`, `/api/menu/{loc}` | admin/players | `getPlayerBySlug()` si 404 | ✓ Data-driven con fallback |
| `/patrocinadores` | `/api/sponsors`, `/api/site-settings`, `/api/menu/{loc}` | admin/sponsors | `sponsorsMock.hero`, `.valueProps`, `.leadForm` siempre | ⚠ Hero/valueProps/leadForm hardcoded (CMS pendiente) |
| `/directiva` | `/api/board-members`, `/api/site-settings`, `/api/menu/{loc}` | admin/board-members | `boardMock.hero`, `boardMock.transparency` siempre | ⚠ Hero/transparency hardcoded (CMS pendiente) |
| `/fanfest` | `/api/fanfest`, `/api/site-settings`, `/api/menu/{loc}` | admin/fanfest-events, admin/fanfest-events/{id}/zones | `FALLBACK_EVENT` inline | ✓ Data-driven |
| `/expedicion-india` | `/api/expeditions`, `/api/site-settings`, `/api/menu/{loc}` | admin/bus-trips | `FALLBACK_TRIPS` inline | ✓ Data-driven |
| `/fanclub` | `/api/membership-plans/active`, `/api/site-settings`, `/api/menu/{loc}` | admin/membership-plans | `fanClubMock` como base + API enriquece | ✓ Parcial — API alimenta precios, beneficios; imágenes de mock |
| `/tienda` | `/api/store/products`, `/api/store/featured-product`, `/api/site-settings`, `/api/menu/{loc}` | admin/products, admin/product-categories | `productsMock.hero`, `.membershipBanner` en estado; productos de API | ✓ Parcial — hero/banner de mock; catálogo de API |
| `/boletos` | `/api/ticketing/matches/featured`, `/api/ticketing/matches/{code}/zones`, `/api/site-settings`, `/api/menu/{loc}` | admin/match-events, admin/match-events/{id}/ticket-zones | `ticketsMock` para partido/zonas si API falla | ✓ Data-driven con fallback |

---

## Admin backoffice disponible

| Módulo | Rutas admin | Permisos |
|---|---|---|
| Site Settings | `/admin/settings` | settings.view, settings.update |
| Menus | `/admin/menus` | menus.view, menus.manage |
| News | `/admin/news` | news.view, news.manage |
| Pages (CMS) | `/admin/pages` | pages.view, pages.manage |
| Stadium | `/admin/stadium` | stadium.view, stadium.manage |
| Clubs | `/admin/clubs` | clubs.view, clubs.manage |
| Match Events | `/admin/match-events` | match_events.view, match_events.manage |
| Match Goals | `/admin/match-events/{id}/goals` | match_events.view, match_goals.manage |
| Ticket Zones | `/admin/match-events/{id}/ticket-zones` | ticket_zones.view, ticket_zones.manage |
| League Standings | `/admin/standings` | standings.view, standings.manage |
| Players | `/admin/players` | players.view, players.manage |
| Staff | `/admin/staff-members` | staff.view, staff.manage |
| Sponsors | `/admin/sponsors` | sponsors.view, sponsors.manage |
| Board Members | `/admin/board-members` | board_members.view, board_members.manage |
| FanFest Events | `/admin/fanfest-events` | fanfest.view, fanfest.manage |
| FanFest Zones | `/admin/fanfest-events/{id}/zones` | fanfest.view, fanfest.manage |
| Bus Trips (Expedición) | `/admin/bus-trips` | expeditions.view, expeditions.manage |
| Membership Plans | `/admin/membership-plans` | membership_plans.view, membership_plans.manage |
| Products | `/admin/products` | products.view, products.manage |
| Product Categories | `/admin/product-categories` | product_categories.view, product_categories.manage |

---

## API pública disponible

| Endpoint | Uso |
|---|---|
| `GET /api/site-settings` | Configuración global (logo, colores, SEO) |
| `GET /api/menu/{header\|footer}` | Menús de navegación |
| `GET /api/news` | Noticias |
| `GET /api/news/{slug}` | Noticia individual |
| `GET /api/stadium` | Datos del estadio |
| `GET /api/clubs` | Clubes activos |
| `GET /api/matches` | Todos los partidos activos |
| `GET /api/matches/featured` | Partido destacado (próximo) |
| `GET /api/matches/{code}` | Partido por código |
| `GET /api/standings` | Tabla de posiciones |
| `GET /api/players` | Plantilla de jugadores |
| `GET /api/players/{slug}` | Perfil de jugador |
| `GET /api/staff` | Cuerpo técnico |
| `GET /api/sponsors` | Patrocinadores |
| `GET /api/board-members` | Directiva |
| `GET /api/fanfest` | Evento FanFest activo |
| `GET /api/expeditions` | Viajes / expediciones |
| `GET /api/membership-plans/active` | Plan de membresía activo |
| `GET /api/store/products` | Catálogo de tienda |
| `GET /api/store/featured-product` | Producto destacado |
| `GET /api/ticketing/matches/featured` | Partido destacado (boletos) |
| `GET /api/ticketing/matches/{code}/zones` | Zonas de boletos |

---

## Fallbacks mock restantes (documentados)

| Página | Sección | Fuente mock | Acción necesaria |
|---|---|---|---|
| `/` | Hero, ticker, academy, shopPreview, partners | `homeMock` | CMS o admin para contenido estático |
| `/calendario` | Hero, filters | `calendarMock.hero`, `.filters` | CMS pendiente |
| `/plantilla` | Hero, positionFilters | `playersMock.hero`, `.positionFilters` | CMS o admin settings |
| `/patrocinadores` | Hero, valueProps, leadForm | `sponsorsMock.hero`, etc. | CMS pendiente |
| `/directiva` | Hero, bloque transparencia | `boardMock.hero`, `.transparency` | CMS pendiente |
| `/fanclub` | Imágenes, badges, ally images | `fanClubMock` como base visual | CMS o campos en MembershipPlan |
| `/tienda` | Hero, membershipBanner | `productsMock.hero`, `.membershipBanner` | CMS o admin settings |
| `/registro-confirmado` | NextSteps copy, QR alt text | `registrationConfirmationMock` | CMS pendiente |
| `/carrito` | Mensajes de cupón, empty state | `cartMock` | Integración real de cupones |

---

## Textos mock corregidos en esta sesión

| Archivo | Texto eliminado |
|---|---|
| `TicketQuantitySelector.jsx` | "{limit} boletos por transaccion mock." → "Máximo {limit} boletos por pedido." |
| `SponsorLeadForm.jsx` | "Formulario visual en mock, sin envio real al backend." → copy limpio |
| `StoreCartPreview.jsx` | "Carrito visual" → "Tu carrito" / "carrito es local y visual" → copy limpio |
| `StoreHero.jsx` | "Carrito visual" → "Tu carrito" |
| `Store.jsx` | SEO: "Catalogo **visual** de la tienda..." → copy limpio |
| `NextMatchCard.jsx` (6B) | "Venta visual. Sin ticketing real..." → eliminado |
| `SeasonSummary.jsx` (6B) | "Calendario visual en mock..." → eliminado |
| `StadiumCTA.jsx` (6C) | "CTA visual. Sin ticketing real..." → eliminado |
| `StadiumInfo.jsx` (6C) | "Capacidad mock" → "Capacidad" / "Enlace visual..." → eliminado |
| `StadiumZones.jsx` (6C) | "lectura visual de las gradas..." → copy limpio |
| `StadiumMap.jsx` (6C) | "Placeholder visual de mapa" → eliminado |

---

## Textos mock fuera de scope (documentados para fases futuras)

| Archivo | Texto | Página |
|---|---|---|
| `DigitalMemberCard.jsx` | `alt="QR acceso mock"` | `/registro-confirmado` |
| `NextSteps.jsx` | "Tu registro es visual por ahora..." | `/registro-confirmado` |
| `Cart.jsx` | SEO "Carrito visual..." / mensajes de cupón mock | `/carrito` |
| `CartEmptyState.jsx` | "arma tu pedido visual" | `/carrito` |
| `StyleGuide.jsx` | Varios (página de desarrollo, no pública) | `/style-guide` |

---

## Links de navegación — estado

| Link | Estado |
|---|---|
| `/` Inicio | ✓ Activo |
| `/calendario` Calendario | ✓ Activo |
| `/estadio` Estadio | ✓ Activo |
| El Club → `/directiva` | ✓ Activo |
| El Club → `/patrocinadores` | ✓ Activo |
| El Club → `/fuerzas-basicas` | ✓ Activo |
| El Club → `/fanfest` | ✓ Activo (agregado Phase 5G) |
| El Club → `/expedicion-india` | ✓ Activo (agregado Phase 5G) |
| `/plantilla` Plantilla | ✓ Activo |
| `/noticias` Noticias | ✓ Activo |
| `/boletos` Boletos | ✓ Activo |
| `/pruebas` Pruebas | ✓ Activo |
| `/tienda` Tienda | ✓ Activo |
| `/fanclub` CTA "ÚNETE A LA TRIBU" | ✓ Activo |
| Aviso legal / Privacidad | ⚠ Pendiente CMS (`pending: true`) |
| Historia del club (footer) | ⚠ Pendiente CMS (`pending: true`) |
