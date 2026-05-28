# PayPal Sandbox Provider - Phase 3D

Fecha: 2026-05-28

## Status

DONE y revisado en Phase 3F Review.

## Componentes implementados

- `config/payments.php`
- `app/Domain/Payments/Services/PayPalAccessTokenService.php`
- `app/Domain/Payments/Providers/PayPalPaymentProvider.php`
- `app/Domain/Payments/Exceptions/PaymentProviderException.php`
- tests con `Http::fake()`

## Que hace el provider

- obtiene `access_token` con OAuth client credentials
- crea orden PayPal con `POST /v2/checkout/orders`
- captura orden PayPal con `POST /v2/checkout/orders/{id}/capture`
- devuelve `PaymentProviderResult`
- nunca expone `client_secret` al frontend

## Configuracion

`client_id`, `client_secret`, `webhook_id`, `currency`, `mode` e `is_enabled`
se administran desde:

```text
/admin/payment-settings
```

El `client_secret` se guarda cifrado en DB.

## URLs de retorno y cancelacion

En el flujo real de membresias:

- `return_url` ideal: `/registro-confirmado?order=TRIBU-XXXX`
- `cancel_url` ideal: `/registro-tribu?cancelled=1`

La implementacion actual usa:

1. URLs dinamicas desde `Payment.metadata` cuando el modulo las define
2. `config/payments.php` solo como fallback para otros modulos futuros

Defaults de fallback recomendados:

- `PAYPAL_RETURN_URL` -> `${APP_URL}/registro-confirmado`
- `PAYPAL_CANCEL_URL` -> `${APP_URL}/registro-tribu`

## Seguridad

- `client_secret` solo se usa en Basic Auth contra PayPal
- `client_secret` no aparece en `rawPayload`
- `client_secret` no aparece en `provider_payload`
- `client_secret` no se expone en frontend
- no se procesan tarjetas ni CVV en este sitio

## Limitacion local

Aunque la redireccion a PayPal sandbox puede funcionar con un tunel HTTPS, los webhooks reales no funcionaran hacia `localhost`.

Sin tunel HTTPS:

- el usuario puede volver por `return_url`
- la orden puede quedar en `pending_payment`
- el webhook real no llegara desde PayPal

## Referencias

- `docs/payments/local-paypal-sandbox-testing.md`
- `docs/payments/paypal-webhooks.md`
- `docs/memberships/membership-payment-flow.md`
