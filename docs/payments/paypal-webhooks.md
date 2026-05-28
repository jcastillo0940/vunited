# PayPal Webhooks - Phase 3E

Fecha: 2026-05-28

## Status

DONE y revisado en Phase 3F Review.

## Endpoint

```text
POST /api/webhooks/paypal
```

- Va en `routes/api.php`
- No usa login publico
- No usa CSRF del grupo `web`
- Debe responder `200` cuando el evento fue recibido, incluso si luego se ignora
- Solo responde `400` si el payload es invalido o falta `event_type`

## Seguridad

- El webhook se registra primero en `payment_events`
- Luego se verifica con PayPal si existe `webhook_id`
- Si la verificacion falla, el evento queda guardado pero no altera el `Payment`
- Si `webhook_id` no esta configurado, la verificacion puede quedar `skipped`
  Esto permite pruebas locales controladas, pero no reemplaza una prueba real con HTTPS publico

## Eventos soportados

- `CHECKOUT.ORDER.APPROVED`
- `PAYMENT.CAPTURE.COMPLETED`
- `PAYMENT.CAPTURE.DENIED`
- `PAYMENT.CAPTURE.REFUNDED`

## Sincronizacion esperada

- `CHECKOUT.ORDER.APPROVED` -> `payments.status = approved`
- `PAYMENT.CAPTURE.COMPLETED` -> `payments.status = captured`
- `PAYMENT.CAPTURE.DENIED` -> `payments.status = failed`
- `PAYMENT.CAPTURE.REFUNDED` -> `payments.status = refunded`

Si el `Payment` pertenece a `MembershipOrder`:

- `captured` -> `membership_orders.status = paid`
- `failed` -> `membership_orders.status = failed`
- `cancelled` o `refunded` -> `membership_orders.status = cancelled`

## Limitacion local importante

PayPal no puede enviar webhooks reales a `localhost`.

En local sin tunel HTTPS:

- la orden puede quedar en `pending_payment`
- `return_url` puede devolver al usuario a `/registro-confirmado?order=...`
- pero el estado no cambiara a `paid` hasta que llegue un webhook real o se procese manualmente en otro entorno

## Prueba real recomendada

Usar un tunel HTTPS publico, por ejemplo ngrok o Cloudflare Tunnel.

Ejemplo:

```env
APP_URL=https://TU-TUNNEL.ngrok-free.app
PAYPAL_RETURN_URL=https://TU-TUNNEL.ngrok-free.app/registro-confirmado
PAYPAL_CANCEL_URL=https://TU-TUNNEL.ngrok-free.app/registro-tribu
PAYPAL_WEBHOOK_URL=https://TU-TUNNEL.ngrok-free.app/api/webhooks/paypal
```

En PayPal Developer se debe registrar:

```text
https://TU-TUNNEL.ngrok-free.app/api/webhooks/paypal
```

## Webhook ID en backoffice

El `webhook_id` configurado en PayPal Developer debe copiarse en:

```text
/admin/payment-settings
```

Campos relevantes:

- `mode`
- `client_id`
- `client_secret`
- `webhook_id`
- `currency`
- `is_enabled`

## Confirmaciones de seguridad

- `client_secret` no va al frontend
- `client_secret` no se guarda en `provider_payload`
- `client_secret` no se guarda plano en audit logs
- no existen endpoints publicos para tarjeta o CVV
- no existen endpoints reales de tienda o boletos en esta fase

## Referencias

- `docs/payments/local-paypal-sandbox-testing.md`
- `docs/payments/paypal-sandbox-provider.md`
- `docs/memberships/membership-payment-flow.md`
