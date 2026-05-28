# Estado Data-Driven del Frontend — Auditoría 2026-05-28

Auditoría realizada con grep sobre `resources/js/Pages/Public/`. Sin inventar datos.

---

## Páginas auditadas

| Ruta | Vista | Patrón de datos |
|---|---|---|
| `/plantilla` | `Squad.jsx` | API real + mock como estado inicial |
| `/jugadores/{slug}` | `PlayerProfile.jsx` | API real + mock como fallback en 404 |
| `/patrocinadores` | `Sponsors.jsx` | API real + mock como estado inicial |
| `/directiva` | `Board.jsx` | API real + mock como estado inicial |
| `/fanfest` | `FanFest.jsx` | Solo API real + fallback inline |
| `/expedicion-india` | `Expedition.jsx` | Solo API real + fallback inline |
| `/tienda` | `Store.jsx` | API real + mock como estado inicial |
| `/boletos` | `Tickets.jsx` | API real + mock como estado inicial |
| `/fanclub` | `FanClub.jsx` | API real; mock como base enriquecida por API |

---

## APIs usadas por página

| Ruta | Servicio(s) |
|---|---|
| `/plantilla` | `playerService`, `staffService`, `siteService`, `menuService` |
| `/jugadores/{slug}` | `playerService`, `siteService`, `menuService` |
| `/patrocinadores` | `sponsorService`, `siteService`, `menuService` |
| `/directiva` | `boardService`, `siteService`, `menuService` |
| `/fanfest` | `fanFestService`, `siteService`, `menuService` |
| `/expedicion-india` | `expeditionService`, `siteService`, `menuService` |
| `/tienda` | `productService`, `cartStorageService`, `siteService`, `menuService` |
| `/boletos` | `ticketingService`, `siteService`, `menuService` |
| `/fanclub` | `membershipService`, `siteService`, `menuService` |

---

## Fallbacks mock restantes

Secciones que **siempre** usan datos del mock, aunque la API responda:

| Página | Sección hardcodeada | Fuente |
|---|---|---|
| `/plantilla` | Hero, positionFilters, ticker | `playersMock`, `homeMock` |
| `/jugadores/{slug}` | Ticker | `homeMock` |
| `/patrocinadores` | Hero, valueProps, leadForm | `sponsorsMock` |
| `/directiva` | Hero, bloque de transparencia | `boardMock` |
| `/fanfest` | Evento completo si API falla | `FALLBACK_EVENT` (inline en componente) |
| `/expedicion-india` | Viajes si API falla; email de contacto siempre | `FALLBACK_TRIPS` + `fallbackSettings.contact_email` |
| `/tienda` | Hero, membershipBanner | `productsMock` |
| `/boletos` | Partido, zonas, success ticket | `ticketsMock` |
| `/fanclub` | Imágenes, badges, ally images | `fanClubMock` |

---

## Links de navegación pendientes

Estado en `publicNavigation.js` al cierre de esta sesión:

- `/fanfest` y `/expedicion-india` **agregados** en esta sesión (dropdown "El Club" + footer).
- `/fanclub` solo aparece como CTA (`publicPrimaryCta`), no en los listados de header ni footer.
- `/carrito`, `/calendario`, `/estadio` tienen ruta y vista pero no se auditaron en esta sesión.
- Links legales (`Aviso legal`, `Privacidad`) marcados como `pending: true` (CMS pendiente).
- `Historia del club` en footer marcado como `pending: true` (CMS pendiente).

---

## Riesgos

1. **SEO description hardcodeada en `/boletos`** — `Tickets.jsx:35` contiene el texto `"Compra visual mock de boletos"`, visible en producción.
2. **Email de contacto hardcodeado** — `Expedition.jsx:198` usa `fallbackSettings.contact_email` directamente en el JSX, sin pasar por API ni CMS.
3. **Hero y banners estáticos** — `/patrocinadores`, `/tienda` y `/directiva` tienen secciones visuales que no reflejan cambios de contenido sin modificar el código.
4. **`TicketSuccessMock` como componente de producción** — `Tickets.jsx` renderiza `TicketSuccessMock` con datos de `ticketsMock.successTicket` siempre, no solo en desarrollo.

---

## Próximos pasos

- [ ] Corregir SEO description en `Tickets.jsx:35`.
- [ ] Mover email de contacto de `Expedition.jsx` a `siteService` o variable de entorno.
- [ ] Conectar hero/banners de `/patrocinadores`, `/tienda` y `/directiva` a CMS o API.
- [ ] Evaluar si `TicketSuccessMock` debe condicional a entorno dev o reemplazarse.
- [ ] Auditar páginas restantes: `/calendario`, `/estadio`, `/carrito`, `/fuerzas-basicas`, `/pruebas`.
- [ ] Resolver links CMS pendientes: Historia del club, Aviso legal, Privacidad.
