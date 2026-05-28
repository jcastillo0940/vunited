# Local PayPal Sandbox Testing

Fecha: 2026-05-28

## Objetivo

Esta guia explica como probar localmente el flujo real de membresias con PayPal sandbox sin exponer secretos en frontend.

## Requisitos

- Laravel corriendo en local
- credenciales sandbox configuradas en `/admin/payment-settings`
- un tunel HTTPS publico
  - ngrok
  - o Cloudflare Tunnel

## 1. Levantar Laravel local

Ejemplo:

```text
php artisan serve --host=127.0.0.1 --port=8000
```

## 2. Abrir un tunel HTTPS

### Opcion A: ngrok

```text
ngrok http 8000
```

### Opcion B: Cloudflare Tunnel

```text
cloudflared tunnel --url http://127.0.0.1:8000
```

## 3. Variables recomendadas

Usar la URL publica del tunel:

```env
APP_URL=https://TU-TUNNEL.ngrok-free.app
PAYPAL_RETURN_URL=https://TU-TUNNEL.ngrok-free.app/registro-confirmado
PAYPAL_CANCEL_URL=https://TU-TUNNEL.ngrok-free.app/registro-tribu
PAYPAL_WEBHOOK_URL=https://TU-TUNNEL.ngrok-free.app/api/webhooks/paypal
```

Notas:

- `MembershipOrderService` ya construye `return_url` con `?order=TRIBU-XXXX`
- `config/payments.php` queda como fallback para modulos futuros
- `PAYPAL_WEBHOOK_URL` es documental en esta fase; la URL real tambien debe registrarse en PayPal Developer

## 4. Configurar PayPal Developer

En la app sandbox:

- copiar `client_id`
- copiar `client_secret`
- crear webhook con:

```text
https://TU-TUNNEL.ngrok-free.app/api/webhooks/paypal
```

- copiar `webhook_id`

Eventos a seleccionar:

- `CHECKOUT.ORDER.APPROVED`
- `PAYMENT.CAPTURE.COMPLETED`
- `PAYMENT.CAPTURE.DENIED`
- `PAYMENT.CAPTURE.REFUNDED`

## 5. Configurar backoffice

Ir a:

```text
/admin/payment-settings
```

Configurar:

- `mode = sandbox`
- `client_id = {sandbox client id}`
- `client_secret = {sandbox client secret}`
- `webhook_id = {paypal webhook id}`
- `currency = USD`
- `is_enabled = true`

## 6. Probar /registro-tribu

1. Abrir `/registro-tribu`
2. Completar el formulario
3. Hacer clic en `PAGAR CON PAYPAL`
4. Confirmar redireccion a PayPal sandbox
5. Aprobar el pago con comprador sandbox
6. Volver a `/registro-confirmado?order=TRIBU-XXXX`

## 7. Verificaciones esperadas

### membership_orders

- se crea al hacer POST `/api/membership-orders`
- queda `pending_payment` inicialmente
- pasa a `paid` cuando llega `PAYMENT.CAPTURE.COMPLETED`

### payments

- se crea en `pending`
- pasa a `provider_created`
- luego `approved`
- luego `captured` si el webhook llega bien

### payment_events

- registra cada webhook recibido
- guarda `provider_event_id`
- guarda `verification_status`
- guarda `processing_status`

## 8. Limitaciones locales

Sin tunel HTTPS:

- PayPal no puede llamar `/api/webhooks/paypal` en localhost
- la orden puede quedar en `pending_payment`
- el usuario puede volver por `return_url`, pero eso no confirma el pago por si solo

## 9. Confirmaciones de seguridad

- `client_secret` nunca va al frontend
- no se guardan tarjeta ni CVV
- el frontend no procesa pagos directos
- Laravel valida el estado final con PayPal y con webhooks

## 10. Recomendacion operativa

Antes de avanzar a Phase 3G, hacer una prueba manual real con tunel HTTPS y confirmar:

- redirect correcto a PayPal sandbox
- regreso correcto a `/registro-confirmado?order=TRIBU-XXXX`
- webhook real recibido
- `membership_orders` actualizado a `paid`
- `payments` actualizado a `captured`
- `payment_events` persistido correctamente
