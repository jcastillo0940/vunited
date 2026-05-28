# Phase 2 Visual Analysis

## Source

- Primary Stitch source reviewed from `D:\Desktop\codigos de diseño.txt`
- Source format: multiple HTML + Tailwind screens concatenated in a single TXT

## Screens Detected

1. `Inicio - Veraguas United FC`
2. `Registro La Tribu`
3. `Compra de Boletos`
4. `FanClub "La Tribu"`
5. `Plantilla`
6. `Confirmacion de Registro`
7. `Tienda Oficial`
8. `Directiva`
9. `FanFest United`
10. `Patrocinadores`
11. `Carrito de Compras`
12. `Mi Cuenta`
13. `Fuerzas Basicas`
14. `Perfil de Jugador`
15. `Inscripcion en Pruebas`

## Visual Direction Summary

The Stitch system is a clean athletic editorial UI with two closely related variants:

- `Light mode public shell`
  - White nav background
  - White content surfaces
  - Strong navy headers and section titles
  - Sky-blue CTAs and accents
- `Classic athletic shell`
  - Navy main navigation bar
  - White content areas with higher structural contrast
  - Larger section framing, stronger card outlines, heavier sidebar blocks

The design is not minimal-tech or startup-generic. It behaves more like a club media system:

- aggressive uppercase display typography
- dense hero moments
- strong editorial sectioning
- sports dashboard fragments
- commerce and membership cards using the same brand grammar

## Core Tokens Extracted

### Colors

- `primary`: `#1D428A`
- `accent`: `#5BC2E7`
- `background`: `#FFFFFF`
- `surface`: `#F4F6F9`
- `text-main`: `#2B2B2B`
- `on-primary`: `#FFFFFF`
- `on-accent`: `#FFFFFF`

Additional support colors used repeatedly in Stitch:

- `outline`: `#E2E8F0`
- `surface-container-low`: `#F8FAFC`
- `surface-container-high`: `#E2E8F0`
- `white/10`, `white/20`, `primary/90`, `accent/5`, `secondary/10`
- neutral borders: `gray-100`, `gray-200`, `gray-300`
- muted text: `gray-400`, `gray-500`, `gray-600`

### Typography

- Display font: `Oswald`
- Body/UI font: `Inter`
- Icon font: `Material Symbols Outlined`

Observed display scale:

- Hero main: `text-5xl` to `md:text-8xl`
- Section majors: `text-4xl` to `text-5xl`
- Section mids: `text-3xl`
- Card titles: `text-xl` to `text-3xl`
- UI labels: `text-[10px]`, `text-xs`, `text-sm`

Observed type behavior:

- Headings are uppercase almost everywhere
- Display weights are bold to black
- Body text is calmer, gray-toned, and more open
- Labels often use uppercase + tracking

### Spacing

Base tokens found directly in Stitch:

- `margin-mobile`: `16px` or `20px` depending on screen variant
- `margin-desktop`: `32px` or `64px` in some classic sections
- `gutter`: `16px` or `24px`
- `unit`: `4px`

Recommended normalization for frontend base:

- keep official token names requested by user
- preserve the smaller light-shell spacing as default
- allow component-level expansion for classic screens

### Radius

- `md`: `0.375rem`
- `lg`: `0.5rem`
- `xl`: `0.75rem`
- `full`: `9999px`

Observed use:

- small CTAs and pills: `rounded-md`
- feature cards and shells: `rounded-lg`
- large content boxes and form areas: `rounded-xl`
- social/icon buttons: `rounded-md` or `rounded-full`

### Shadows

Observed hierarchy:

- subtle nav and chip shadows: `shadow-sm`
- cards and secondary panels: `shadow-md`
- sticky or promo blocks: `shadow-lg`
- hero CTA and major summary modules: `shadow-xl`

### Grid and Layout Patterns

- fixed top ticker + fixed nav stack
- large centered shell with `max-w-7xl`
- split editorial layout: `8/4` or `main/sidebar`
- bento/product pair layout for store
- tall form + sticky summary pattern
- comparison/payment/ticket summary sidebars
- responsive collapse to single column on mobile

## Shared Components Detected

### Global layout

- Top match ticker
- Main navigation
- Footer
- page max-width container
- section paddings and offsets

### Reusable content modules

- Hero blocks
- Section title rows with optional CTA
- News cards
- Product cards
- Player cards
- Membership cards
- Ticket summary cards
- Stat cards
- Sponsor logo bars / carousel rows
- Sidebar summary blocks
- Timeline / schedule segments
- CTA banners

### Forms

- text inputs
- select inputs
- file upload drop area
- segmented payment selectors
- summary sidebars
- primary CTA submit buttons

## Visual Variations to Preserve

### Header white vs header blue

Two main nav variants exist:

- `White glass nav`
  - `bg-white/95`
  - `backdrop-blur-md`
  - navy text
  - accent CTA
  - used strongly in home, store/cart style surfaces
- `Blue nav`
  - `bg-primary`
  - white text
  - accent active states
  - used in membership, forms, sponsorship, event-like screens

Recommendation:

- implement `MainNavbar` with `variant="light"` and `variant="solid"`
- keep `TopTicker` always navy unless future screens prove otherwise

### Primary / accent / secondary naming drift

Stitch alternates between `accent` and `secondary` for the same celeste role.

Recommendation:

- standardize frontend theme on:
  - `primary`
  - `accent`
- allow `secondary` alias only if needed for imported legacy class parity

### Desktop/mobile spacing drift

The classic screens sometimes widen margins to `64px` and gutters to `24px`.

Recommendation:

- base system uses requested tokens
- individual components expose `dense` / `classic` / `spacious` variants where needed

## API Real vs Mock Strategy

### Real API-backed surfaces in Phase 2

- site settings
- header menu
- footer menu
- CMS pages by slug
- news list
- news detail

### Mock-backed surfaces for now

- players / plantilla detail
- sponsors tiers
- store products
- tickets flows
- memberships
- buses / expeditions
- FanFest
- directiva
- fuerzas basicas
- trials registration
- account/cart states

## Fidelity Risks

1. The TXT contains multiple shells with slight token drift; without a formal variant system, components may become visually inconsistent.
2. Several future screens depend on modules not yet implemented in backend, so mocks must be clearly isolated from real services.
3. The white-nav and blue-nav modes are both first-class; collapsing them into one navbar would reduce Stitch fidelity.
4. Hero sections rely on large editorial type and image overlays; if spacing or font loading is off, the result will feel generic immediately.
5. Material Symbols are part of the language of the design, not a placeholder; missing icon font setup would degrade fidelity fast.

## Recommendation for Phase 2B

Build the frontend base as a small public design system first:

- one reusable app shell
- one tokenized Tailwind theme
- light and solid navbar variants
- editorial cards + form primitives
- service layer for real Laravel content
- mocks for unimplemented business domains
- a public style guide page to validate fidelity before the Home build
