# Inventario visual — Fase 3

Extraído directamente de `tailwind.config.js`, `resources/css/app.css` y
`resources/js/Components/{layout,common,cards,forms}` (frontend Inertia
actual, producción). Esta es la fuente de verdad que `shared/ui` reproduce
**sin rediseñar**.

## Tipografías

- Display (`font-display`): **Oswald** 400/600/700 — títulos de sección,
  marca, uppercase, `tracking-tight`.
- Cuerpo (`font-sans` / `font-body`): **Inter** 400/500/600/700.
- Íconos: **Material Symbols Outlined** (fuente, `FILL 0, wght 400, GRAD 0,
  opsz 24`).
- Tracking especial: `athletic` = `0.24em` (kickers/eyebrows uppercase).

## Colores

| Token | Valor | Uso |
| --- | --- | --- |
| `primary` | `#1D428A` | marca, headers, footer, headings |
| `accent` / `secondary` | `#5BC2E7` | acentos, kickers, hover |
| `background` | `#FFFFFF` | fondo base |
| `surface` | `#F4F6F9` | fondos secundarios |
| `surface-container-low` | `#F8FAFC` | paneles suaves |
| `surface-container-high` | `#E2E8F0` | paneles/skeleton |
| `text-main` | `#2B2B2B` | texto de cuerpo |
| `on-primary` / `on-accent` | `#FFFFFF` | texto sobre color |
| `outline` | `#E2E8F0` | bordes |

## Logo

SVG inline en `resources/js/Components/ApplicationLogo.jsx` (viewBox 316×316,
figura de escudo geométrico). Se reutiliza el mismo `<path>` exacto en
`shared/ui/Logo` — no se redibuja.

## Iconos

Material Symbols Outlined vía clase `.material-symbols-outlined` + nombre de
ícono como texto del `<span>` (p. ej. `shield`, `facebook`,
`photo_camera`). Se conserva el mismo mecanismo en `shared/ui/Icon`.

## Header / Menú (`MainNavbar.jsx`)

Fijo (`fixed top-10` cuando hay `TopTicker` encima), variantes `light`
(blanco/backdrop-blur, texto `primary`) y `dark` (fondo `primary`, texto
blanco). Logo + nombre de marca partido en 2 líneas
(`font-display uppercase`). Links desktop en `lg:flex`, menú móvil
colapsable con botón hamburguesa. CTA (`CTAButton`) a la derecha.

## Ticker superior (`TopTicker.jsx`)

Barra fija de 40px sobre el header, fondo `primary`, marca a la izquierda,
"próximo partido" con efecto shimmer cuando hay datos, CTA "Comprar
entradas" a la derecha. Antes de anuncio de partido: mensaje de fallback.

## Footer (`Footer.jsx`)

Fondo `primary`, texto blanco. Logo + marca, descripción corta, redes
sociales, columnas de menú (`footerMenu`), franja legal
(`legalMenu`) al pie.

## Banners / Hero

`HeroSection.jsx` (común) + `VideoBackground.jsx` — hero de página con video
de fondo, usado en Home y páginas de dominio (Calendario, FanClub, según
commits recientes).

## Layout / Grid / Contenedor

- Contenedor: `.page-shell` = `mx-auto max-w-shell(80rem) px-4 md:px-8`.
- Ritmo vertical de sección: `.section-space` = `py-16 md:py-24`.
- Grid: utilidades Tailwind estándar (`grid`, `lg:grid-cols-*`), sin sistema
  de grid propio adicional.

## Cards

`surface-card` (`rounded-lg border shadow-card`) y `surface-panel`
(`rounded-xl shadow-panel`) como base. Cards de dominio construidas encima:
`NewsCard`, `ProductCard`, `PlayerCard`, `TicketCard`, `MembershipCard` — no
se replican como tales en `shared/ui` (tienen datos de negocio), pero sí su
base (`Card`).

## Botones

`CTAButton.jsx` — variantes (`primary`/`ghost` observadas), tamaños
(`sm`/otros), soporta `as="a"` o `as="button"`, estado `pending`.

## Inputs / Formularios

`FormInput.jsx`, `FileUploadBox.jsx`. Usa el plugin `@tailwindcss/forms`
para el reset base de inputs.

## Estados

`LoadingState.jsx`, `ErrorState.jsx`, `EmptyState.jsx` — patrón de 3 estados
estándar ya presente en el código actual, replicado tal cual en
`shared/ui`.

## Modales / Tablas

No existían componentes de Modal ni Table genéricos en el frontend público
actual (es mayormente institucional/CMS, sin panel administrativo previo).
`shared/ui` los añade como primitivos nuevos **siguiendo el mismo lenguaje
visual** (radios, sombras, colores ya definidos) — esto es una extensión
necesaria para Store/Ticketing/Admin, no un rediseño de algo existente.

## Breakpoints

Los de Tailwind por defecto (`sm 640px`, `md 768px`, `lg 1024px`, `xl
1280px`, `2xl 1536px`) — no hay breakpoints custom en el config actual. Se
mantienen igual en `shared/ui`.

## Animaciones

- `shimmer` (2.8s ease-in-out infinite) — barrido de brillo en el ticker.
- `scroll` (40s linear infinite, en `app.css`) — marquee, franja de
  patrocinadores.
- `animate-ping` nativo de Tailwind — indicador "en vivo" del ticker.

## Responsive

Mobile-first, con colapso a menú hamburguesa `<lg`, grids que pasan de 1
columna a `lg:grid-cols-N`. Mismo patrón se mantiene en los 3 frontends
nuevos.

## Radios / Sombras / Espaciado / Z-index

Ver `shared/ui/src/tokens/*.ts` — valores copiados 1:1 de
`tailwind.config.js` (radii `md/lg/xl/full`, shadows
`ticker/card/panel/float`, spacing `margin-mobile(16px)/margin-desktop(32px)/
gutter(16px)/unit(4px)`). Z-index no estaba tokenizado en el config
original (usaba literales `z-40`/`z-50` inline) — se tokeniza en
`shared/ui` (`sticky: 40, fixed: 50, modal: 100, toast: 110`) preservando
los mismos valores donde ya se usaban (ticker `z-50`, navbar `z-40`).

## Matriz página actual → ruta nueva → componentes → paridad

| Página actual (Inertia) | Dominio | Ruta nueva (SPA) | Componentes shared/ui | Paridad Fase 3 | Diferencias justificadas |
| --- | --- | --- | --- | --- | --- |
| `Pages/Public/Home.jsx` | Web | `/` | Layout, Header, Nav, MobileMenu, Footer, Card | Visual completa | Ninguna — replicado |
| `Pages/Public/NewsIndex.jsx` / `NewsShow.jsx` | Web | `/noticias`, `/noticias/:slug` | Layout "news" | Layout replicado, datos mock | Contenido real pendiente de API |
| `Pages/Public/CmsPage.jsx`, `Board.jsx`, `Sponsors.jsx`, `Stadium.jsx`, `Squad.jsx`, `PlayerProfile.jsx`, `FanFest.jsx`, `Expedition.jsx`, `Academy.jsx` | Web | `/institucional/*` | Layout "institucional" | Layout genérico replicado | Cada página específica no se migró 1:1 (fuera de alcance: la Fase 3 pide el *layout*, no cada página) |
| Estado 404/500 (Laravel/Inertia por defecto) | Web | `*`, error boundary | ErrorState | Nuevo, con identidad de marca | Antes usaba la página de error genérica de Laravel; ahora tiene marca — mejora, no rediseño de marca |
| `Pages/Public/Store.jsx`, `Cart.jsx`, `StoreOrderConfirmed.jsx` | Store | `store/frontend`: `/`, `/categoria/:slug`, `/producto/:slug`, `/carrito`, `/checkout`, `/confirmacion`, `/orden/:id`, `/pago/error`, `/pago/pendiente` | Layout, Card, Button, Table | Shells funcionales (sin backend real aún) | Fase 3 pide shells, no el flujo completo de compra |
| `Pages/Public/Tickets.jsx`, `TicketOrderConfirmed.jsx` | Ticketing | `ticketing/frontend`: `/eventos`, `/eventos/:id`, `/zona`, `/cantidad`, `/resumen`, `/checkout`, `/confirmacion`, `/wallet`, `/ticket/:id`, `/escaner`, `/escaner/resultado` | Layout, Card, Button, Badge | Shells funcionales (sin backend real aún) | Igual que Store: shells, no flujo completo |
| (nuevo, no existía) | Admin | `web/frontend/admin/*` | Sidebar, Header, Table, FormField, Badge | Shell nuevo | No hay panel admin previo que reproducir; usa la misma identidad visual |
