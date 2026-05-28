# Commercial Mock Flow QA

Date: 2026-05-27

## Scope

Routes reviewed:

- `/fanclub`
- `/registro-tribu`
- `/registro-confirmado`
- `/tienda`
- `/carrito`
- `/boletos`

## Shell Consistency

All reviewed routes use the same public shell through `AppLayout`:

- `TopTicker`
- `MainNavbar`
- `Footer`
- `siteService` shell loading
- `menuService` shell loading

Primary global CTA remains:

- `UNETE A LA TRIBU` -> `/fanclub`

## Navigation and CTA Review

Confirmed visual flow:

- `/fanclub` -> `/registro-tribu`
- `/registro-tribu` -> local submit state -> `/registro-confirmado`
- `/tienda` -> `/carrito`
- `/carrito` keeps checkout as visual-only
- `/boletos` keeps payment as visual-only

No broken links were kept inside the reviewed commercial routes.

One future CTA was neutralized:

- `Mi Cuenta` on `/registro-confirmado` is now visual-only because `/mi-cuenta` is not built yet.

## Mock Boundaries

### Membership flow

- `/fanclub` is marketing/presentation only
- `/registro-tribu` is visual-only form
- `/registro-confirmado` is visual-only confirmation
- no membership record is created
- no member card is persisted

### Store flow

- `/tienda` now consumes a real public catalog API with fallback mock data if the API is unavailable
- `/carrito` keeps the cart local in browser storage
- `/carrito` now creates real `store_orders`
- store checkout now redirects to PayPal
- `/orden-tienda-confirmada` now reads real store order status
- no cart is persisted in backend
- coupons remain visual only

### Ticketing flow

- `/boletos` now consumes a real public ticketing catalog API with fallback mock data if the API is unavailable
- quantity and checkout state remain local
- no payment is processed
- no QR is generated
- no ticket is persisted
- no ticket order is created

## Payment Foundation Dependency

The following capabilities remain blocked until future `Payment Foundation` work:

- real checkout
- PayPal order creation
- PayPal capture
- payment persistence
- webhook handling
- order creation for memberships
- order creation for tickets
- digital ticket issuance
- membership activation after payment

See also:

- `docs/payments/paypal-future-integration.md`

## Security Confirmation

Confirmed in the reviewed routes:

- no card submission to backend
- no CVV submission to backend
- no card persistence
- no membership persistence
- no cart persistence in backend
- no ticket persistence
- no ticket order creation

## Copy Adjustments Applied

Discrete copy clarifications were added or confirmed in sensitive screens:

- `/registro-tribu`
  - explicit note: visual demo only, no charge and no real registration
- `/carrito`
  - explicit note: coupons are still visual and card data is not processed on-site
- `/boletos`
  - explicit note: mock payment only, no charge, no PayPal, no QR, no real issuance
- `/registro-confirmado`
  - explicit note: no real download, no payment, no persistence

## Remaining Risks

- Final visual fidelity still depends on manual browser review.
- `/tienda` already consumes real catalog data and `/carrito` now creates real store orders, but the cart still depends on browser-local state.
- `/boletos` already consumes real match and zone data, but quantity, success state and payment remain disconnected from backend order domains.
- Ticket state is still local and does not form a real commerce session.
- `Payment Foundation` sigue siendo necesaria para activar conversion real en boletos y para futuras extensiones comerciales compartidas.
