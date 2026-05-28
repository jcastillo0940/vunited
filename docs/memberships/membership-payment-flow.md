# Membership Payment Flow - Phase 4A

Fecha: 2026-05-28

## Status

DONE y actualizado con Membership Plans Admin.

## Flujo actual

```text
1. Usuario abre /registro-tribu
2. Frontend consulta GET /api/membership-plans/active
3. Frontend envia POST /api/membership-orders
4. Backend busca MembershipPlan activo por code
5. Backend crea MembershipOrder con status pending_payment
6. Backend guarda snapshot del plan en metadata
7. Backend crea Payment con amount/currency del plan activo
8. Backend crea orden PayPal via PayPalPaymentProvider
9. Backend devuelve approve_url
10. Frontend redirige a PayPal sandbox
11. PayPal devuelve al usuario a /registro-confirmado?order=TRIBU-XXXX
12. Webhook real actualiza payment y membership_order cuando llega
13. Frontend consulta GET /api/membership-orders/{orderNumber}
```

## Estado esperado por escenario

### Si el webhook llega

- `payments.status = captured`
- `membership_orders.status = paid`
- `paid_at`, `starts_at` y `expires_at` quedan poblados

### Si PayPal falla

- `payments.status = failed`
- `membership_orders.status = failed`

### Si el pago se devuelve o cancela

- `payments.status = refunded` o `cancelled`
- `membership_orders.status = cancelled`

### Si no llega webhook

- `membership_orders.status` puede seguir en `pending_payment`
- esto es esperable en local sin HTTPS publico

## Return URL y cancel URL

La implementacion actual de membresias usa URLs dinamicas por metadata:

- `return_url = /registro-confirmado?order=TRIBU-XXXX`
- `cancel_url = /registro-tribu?cancelled=1`

Eso permite que la pantalla final consulte el numero real de orden.

## Fuente de precio actual

El precio ya no sale de un hardcode en servicio.

Ahora sale del plan activo en `membership_plans`.

Campos usados:

- `code`
- `price`
- `currency`
- `duration_months`
- `benefits`
- `kit_items`
- `partner_discounts`

## Si el plan cambia despues

Las ordenes existentes no deben reescribirse retroactivamente.

Cada `membership_order` guarda:

- `membership_plan`
- `membership_price`
- `currency`
- `metadata.plan_snapshot`

## Seguridad confirmada

- frontend no envia tarjeta
- frontend no envia CVV
- backend no guarda tarjeta
- backend no guarda CVV
- `client_secret` no llega al frontend
- PayPal maneja la captura real fuera del frontend

## Limitacion local

Sin tunel HTTPS:

- el usuario puede completar la aprobacion en PayPal sandbox
- el navegador puede volver por `return_url`
- pero PayPal no podra enviar webhook real a `localhost`

En ese caso la orden puede quedarse en `pending_payment`.

## Prueba real recomendada

Usar ngrok o Cloudflare Tunnel y configurar:

```env
APP_URL=https://TU-TUNNEL.ngrok-free.app
PAYPAL_RETURN_URL=https://TU-TUNNEL.ngrok-free.app/registro-confirmado
PAYPAL_CANCEL_URL=https://TU-TUNNEL.ngrok-free.app/registro-tribu
PAYPAL_WEBHOOK_URL=https://TU-TUNNEL.ngrok-free.app/api/webhooks/paypal
```

Registrar en PayPal Developer:

```text
https://TU-TUNNEL.ngrok-free.app/api/webhooks/paypal
```

## Como validar en DB

- `membership_orders`
- `payments`
- `payment_events`

## Referencias

- `docs/memberships/membership-plans-admin.md`
- `docs/payments/local-paypal-sandbox-testing.md`
- `docs/payments/paypal-webhooks.md`
