# Phase 2 Component Map

## Global Layout Components

- `AppLayout`
  - owns page shell
  - accepts navbar variant
  - renders `TopTicker`, `MainNavbar`, main content, `Footer`
- `TopTicker`
  - club label
  - match/promo/live state
  - CTA area
- `MainNavbar`
  - logo
  - brand lockup
  - nav links
  - primary CTA
  - mobile trigger placeholder
  - variants:
    - `light`
    - `solid`
- `Footer`
  - branding
  - sitemap columns
  - newsletter teaser
  - social icons

## Common UI Components

- `HeroSection`
  - title
  - subtitle
  - actions
  - background/image overlay
- `SectionTitle`
  - display heading
  - optional eyebrow/meta
  - optional CTA link
- `CTAButton`
  - variants:
    - `primary`
    - `secondary`
    - `outline`
    - `ghost`
- `LoadingState`
- `ErrorState`
- `EmptyState`

## Card Components

- `NewsCard`
  - featured
  - compact
- `ProductCard`
  - image
  - price
  - CTA
- `PlayerCard`
  - portrait
  - dorsal
  - name
  - role
- `TicketCard`
  - zone
  - price
  - CTA
- `MembershipCard`
  - tier
  - price or badge
  - benefits
  - CTA

## Form Components

- `FormInput`
  - text
  - email
  - tel
  - number
  - date
  - select
  - textarea
- `FileUploadBox`
  - drag/drop visual shell
  - helper text
  - icon

## Services

- `apiClient`
  - axios wrapper
  - base JSON behavior
- `siteService`
  - site settings read
- `menuService`
  - header/footer read
- `pageService`
  - page by slug
- `newsService`
  - list/detail

## Mocks

- `homeMock`
- `playersMock`
- `sponsorsMock`
- `productsMock`
- `ticketsMock`
- `membershipsMock`

## Component Mapping by Screen

### Inicio

- `AppLayout`
- `TopTicker`
- `MainNavbar` variant `light`
- `HeroSection`
- `SectionTitle`
- `NewsCard`
- `ProductCard`
- `MembershipCard`
- `Footer`

### Registro La Tribu

- `AppLayout`
- `MainNavbar` variant `solid`
- `SectionTitle`
- `FormInput`
- `FileUploadBox`
- `CTAButton`

### Compra de Boletos

- `AppLayout`
- `MainNavbar` variant `light`
- `TicketCard`
- `SectionTitle`
- `CTAButton`

### FanClub / Confirmacion

- `AppLayout`
- `MainNavbar` variant `solid`
- `MembershipCard`
- `CTAButton`

### Plantilla / Perfil / Fuerzas Basicas

- `AppLayout`
- `MainNavbar`
- `SectionTitle`
- `PlayerCard`
- filter pills

### Tienda / Carrito

- `AppLayout`
- `MainNavbar` variant `light`
- `ProductCard`
- `FormInput`
- `CTAButton`

### Directiva / Patrocinadores / FanFest / Buses / Pruebas

- `AppLayout`
- `MainNavbar` variant `solid`
- `SectionTitle`
- specialized cards built later from current primitives

## Data Source Plan

### Real API now

- `AppLayout` settings/logo/contact/footer copy
- `TopTicker` later when backend exists or fallback mock for now
- `MainNavbar` menu links
- `Footer` footer menu + settings/socials
- `NewsCard` when used on home/news
- CMS page surfaces by slug

### Mocks now

- player cards
- products
- ticket cards
- membership cards
- sponsor cards
- home-only placeholder spotlight areas not yet backed by CMS blocks

## Phase 2B Deliverable Boundary

Phase 2B should stop at:

- tokenized theme
- reusable primitives
- service layer
- mock layer
- public shell
- style guide screen

It should not yet build:

- final Home
- final news listing page
- final roster page
- store/cart flows
- ticket flow
- membership flow
- FanFest flow
