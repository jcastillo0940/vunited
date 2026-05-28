# Payment Foundation Technical Review

Fecha: 2026-05-28

## Status

DONE_WITH_CONCERNS

Phases revisadas:

- 3A Payment Settings Admin
- 3B Payment Provider Abstraction
- 3C Payments Lifecycle Base
- 3D PayPal Sandbox Provider
- 3E PayPal Webhooks
- 3F Membership Orders with PayPal
- 4A Membership Plans Admin + Public API
- 4B Store Catalog Admin + Public API
- 4C Ticketing Catalog Admin + Public API
- 4D Store Orders + PayPal

## Confirmaciones de arquitectura

- `PaymentSetting` existe y administra configuracion PayPal desde backoffice
- `client_secret` usa almacenamiento cifrado
- `PaymentProvider` y `PaymentProviderResult` ya existen
- `PaymentLifecycleService` centraliza estados locales
- `PayPalPaymentProvider` usa backend Laravel para crear/capturar ordenes
- `PayPalWebhookController` existe en `POST /api/webhooks/paypal`
- `MembershipOrderService` ya crea orden real y redirige a PayPal
- `MembershipPlan` ahora define precio, moneda y beneficios desde backoffice
- `ProductCategory` y `Product` ahora definen el catalogo real de tienda desde backoffice
- `StoreOrder` y `StoreOrderItem` ahora permiten pago real de tienda usando PayPal
- `MatchEvent` y `TicketZone` ahora definen el catalogo real de boletos desde backoffice

## Hallazgos de la revision

### Correcto

- `return_url` de membresia ya incluye `?order=TRIBU-XXXX`
- `cancel_url` vuelve a `/registro-tribu?cancelled=1`
- frontend de membresia no envia tarjeta ni CVV
- no existen endpoints reales de boletos
- webhook `captured` sincroniza `membership_orders.status = paid`
- `failed` sincroniza `membership_orders.status = failed`
- `refunded/cancelled` sincroniza `membership_orders.status = cancelled`
- `/fanclub` y `/registro-tribu` pueden consumir el plan real activo
- `/tienda` ya puede consumir categorias, productos y producto destacado reales
- `/carrito` ya puede crear `store_orders` reales y redirigir a PayPal
- `/orden-tienda-confirmada` ya consulta el estado real de una orden de tienda
- `/boletos` ya puede consumir partido destacado y zonas reales

### Corregido en esta revision

- se elimino un componente frontend legacy no usado con UI de tarjeta/CVV
- se alinearon los fallbacks de `config/payments.php` con rutas reales del flujo de membresia
- se reforzo documentacion local para pruebas con tunel HTTPS
- se elimino el hardcode del precio de membresia en `MembershipOrderService`
- se elimino el hardcode principal del catalogo de tienda en el frontend publico
- se elimino el hardcode principal del catalogo de boletos en el frontend publico
- se conecto la tienda a `store_orders` reales reutilizando la Payment Foundation existente

### Riesgo principal pendiente

En local sin HTTPS publico, PayPal no puede enviar webhooks reales.

Eso significa:

- la orden puede crearse
- la redireccion puede volver a `/registro-confirmado?order=...`
- pero el estado puede quedarse en `pending_payment` hasta recibir webhook real

## Rutas reales confirmadas

- `GET /api/store/products`
- `GET /api/store/products/{slug}`
- `GET /api/store/categories`
- `GET /api/store/featured-product`
- `POST /api/store/orders`
- `GET /api/store/orders/{orderNumber}`
- `GET /api/ticketing/matches`
- `GET /api/ticketing/matches/featured`
- `GET /api/ticketing/matches/{code}`
- `GET /api/ticketing/matches/{code}/zones`
- `GET /api/membership-plans/active`
- `POST /api/membership-orders`
- `GET /api/membership-orders/{orderNumber}`
- `POST /api/webhooks/paypal`
- `GET /admin/payment-settings`
- `PUT /admin/payment-settings`
- `GET /admin/membership-plans`
- `POST /admin/membership-plans`
- `PUT /admin/membership-plans/{membershipPlan}`
- `DELETE /admin/membership-plans/{membershipPlan}`
- `GET /admin/store-orders`
- `GET /admin/store-orders/{storeOrder}`

## Rutas que NO deben existir y se confirmaron ausentes

- endpoints reales de boletos
- endpoints genericos de checkout no documentados
- endpoints que acepten tarjeta o CVV

Nota:

- el catalogo publico de tienda si existe
- el catalogo publico de boletos si existe
- `ticket_orders`, QR real, validacion de entradas y PayPal para boletos siguen ausentes por diseno en estas fases
- tienda ya tiene `store_orders` reales y pago PayPal especifico de dominio

## Seguridad confirmada

- `client_secret` no va al frontend
- `client_secret` no aparece en `provider_payload`
- `client_secret` no aparece plano en audit logs
- no se guarda tarjeta
- no se guarda CVV
- no se envian tarjeta ni CVV al backend

## Recomendacion

Antes de tocar checkout real de tienda o boletos reales, conviene hacer al menos una prueba manual real con ngrok o Cloudflare Tunnel:

- crear orden desde `/registro-tribu`
- crear orden desde `/carrito`
- aprobar en PayPal sandbox
- confirmar webhook real en `/api/webhooks/paypal`
- verificar `membership_orders`, `store_orders`, `payments` y `payment_events`

Luego de eso, la siguiente extension sana para comercio seria:

- `Ticket Orders + PayPal`

## Referencias

- `docs/memberships/membership-plans-admin.md`
- `docs/store/store-catalog-admin.md`
- `docs/store/store-orders-payment-flow.md`
- `docs/ticketing/ticketing-catalog-admin.md`
- `docs/payments/local-paypal-sandbox-testing.md`
- `docs/payments/paypal-webhooks.md`
- `docs/memberships/membership-payment-flow.md`
