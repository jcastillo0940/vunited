# Admin Payment Monitoring

Fecha: 2026-05-28

## Objetivo

Estas pantallas de backoffice permiten monitorear el flujo real de membresias y PayPal en staging sin agregar acciones manuales de cobro.

## Rutas admin

- `GET /admin/membership-orders`
- `GET /admin/membership-orders/{membershipOrder}`
- `GET /admin/payments`
- `GET /admin/payments/{payment}`
- `GET /admin/payment-events`
- `GET /admin/payment-events/{paymentEvent}`

## Permisos

- `membership_orders.view`
- `payments.view`
- `payment_events.view`

## Que se puede monitorear

### Membership Orders

- `order_number`
- socio
- email
- status
- precio
- moneda
- fechas clave
- payment asociado

### Payments

- provider
- status
- amount/currency
- `provider_order_id`
- `provider_capture_id`
- `payable_type`
- payload seguro
- metadata segura
- eventos relacionados

### Payment Events

- `provider_event_id`
- `event_type`
- `verification_status`
- `processing_status`
- `provider_order_id`
- `provider_capture_id`
- headers seguros
- payload seguro
- `error_message`

## Que NO se puede hacer todavia

- no capturar pagos manualmente
- no reembolsar manualmente
- no cambiar estados manualmente
- no reintentar webhooks desde UI
- no crear ordenes desde admin

## Seguridad

- no se expone `client_secret`
- no se muestran tarjeta ni CVV
- payloads y metadata se renderizan en forma sanitizada

## Uso recomendado en staging

1. Configurar PayPal sandbox en `/admin/payment-settings`
2. Crear orden desde `/registro-tribu`
3. Monitorear `membership_orders`
4. Verificar `payments`
5. Confirmar recepcion y procesamiento en `payment_events`

## Advertencia

Estas pantallas son de monitoreo. No reemplazan una consola de operaciones completa y no incluyen acciones manuales de capturar o reembolsar.
