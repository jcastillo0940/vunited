# Phase 2 Frontend Implementation Plan

## Goal

Turn the Stitch HTML/Tailwind reference into a modular React/Tailwind public frontend foundation with high visual fidelity, while consuming the existing Laravel Phase 1 API where available and using mocks where backend modules do not exist yet.

## Phase 2A Deliverables

- visual analysis of the Stitch TXT
- screen inventory
- token extraction
- component map
- API-real vs mock plan
- implementation risks

## Phase 2B Deliverables

- Tailwind theme aligned to Stitch tokens
- global font loading for:
  - Oswald
  - Inter
  - Material Symbols Outlined
- public component structure under `resources/js/components`
- API service layer under `resources/js/services`
- mock data layer under `resources/js/mocks`
- reusable public shell:
  - `TopTicker`
  - `MainNavbar`
  - `Footer`
  - `AppLayout`
- reusable primitives:
  - CTA button
  - section title
  - hero section
  - cards
  - form input
  - file upload box
  - loading/error/empty states
- temporary public style guide page for visual QA

## Routing Strategy

- keep Laravel backend untouched except where frontend bootstrap already depends on it
- reuse the existing public Inertia root page for the temporary style guide
- do not add new backend modules or admin changes

## Theme Strategy

Normalize the Stitch design into one shared token layer:

- brand colors
- spacing aliases
- radius scale
- font families
- shadow presets
- shell widths

Add a component variant system where Stitch clearly diverges:

- navbar: `light` vs `solid`
- CTA: `primary`, `secondary`, `outline`
- cards: `default`, `featured`, `compact`

## Data Strategy

### Services wired to real API

- `siteService`
- `menuService`
- `pageService`
- `newsService`

### Mock-only for now

- home supplementary content
- players
- sponsors
- products
- tickets
- memberships

## Visual QA Strategy

1. run frontend locally with Vite
2. open public style guide in browser
3. verify:
   - fonts
   - shell spacing
   - CTA treatments
   - navbar variants
   - cards
   - form states
   - mobile stacking
4. adjust until the style guide clearly reflects Stitch language

## Known Constraints

- no backend expansion in Phase 2A/2B
- no pixel-perfect final Home yet
- no real commerce, ticketing, memberships, buses, FanFest, or payments
- some future surfaces depend on mocks until backend exists

## Recommended Next Step After Phase 2B

Once this base is approved, build `Phase 2C` around the public Home and news entry experience using:

- real site settings
- real header/footer menus
- real news API
- mock spotlights only where the backend domain still does not exist
