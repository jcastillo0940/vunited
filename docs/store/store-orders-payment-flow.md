# Store Orders Payment Flow

Fecha: 2026-05-28

## Objetivo

Phase 4D conecta el catalogo real de tienda con un flujo real de `store_orders` pagables mediante PayPal.

En esta fase:

- el carrito sigue siendo local en navegador
- el backend crea `store_orders` reales
- el backend calcula subtotal y total
- el backend crea `payments` asociados usando la Payment Foundation
- PayPal entrega `approve_url`
- el webhook sincroniza el estado final de la orden

## Alcance implementado

Se agregaron:

- `StoreOrder`
- `StoreOrderItem`
- `StoreOrderStatus`
- `StoreOrderService`
- `POST /api/store/orders`
- `GET /api/store/orders/{orderNumber}`
- monitoreo admin readonly en:
  - `GET /admin/store-orders`
  - `GET /admin/store-orders/{storeOrder}`
- pantalla publica:
  - `/orden-tienda-confirmada`

## Flujo tecnico

1. El usuario agrega productos a un carrito local en `/tienda`.
2. En `/carrito`, el frontend envia:
   - `customer_name`
   - `customer_email`
   - `customer_phone`
   - `items[]`
   - `accept_terms`
3. `StoreOrderService`:
   - valida productos activos
   - valida stock si `track_stock = true`
   - calcula subtotal y total en backend
   - crea `store_orders`
   - crea `store_order_items` con snapshot del producto
   - crea `payments` asociados por `payable`
   - llama `PayPalPaymentProvider::createOrder()`
4. La API devuelve:
   - `order_number`
   - `status`
   - `approve_url`
   - `payment_id`
   - `total`
   - `currency`
5. El frontend redirige al usuario a PayPal.
6. PayPal vuelve a:
   - `return_url`: `/orden-tienda-confirmada?order=STORE-XXXX`
   - `cancel_url`: `/carrito?cancelled=1&order=STORE-XXXX`
7. El webhook PayPal actualiza:
   - `paid`
   - `failed`
   - `cancelled`

## Calculo de total

El total nunca se toma del frontend.

El backend usa:

- precio actual del producto
- cantidad enviada
- `discount_total = 0` por ahora
- `tax_total = 0` por ahora

Formula actual:

- `total = subtotal - discount_total + tax_total`

## Stock

Reglas actuales:

- si `track_stock = false`, el producto se puede ordenar sin control de disponibilidad
- si `track_stock = true` y `stock_quantity` es insuficiente, la API rechaza la orden
- al recibir webhook `PAYMENT.CAPTURE.COMPLETED`, se descuenta stock

## Estado de la orden

Estados implementados:

- `draft`
- `pending_payment`
- `paid`
- `cancelled`
- `failed`

Sincronizacion actual:

- `captured` -> `paid`
- `failed` -> `failed`
- `refunded/cancelled` -> `cancelled`

## Seguridad

Confirmado:

- no se guarda tarjeta
- no se guarda CVV
- no se envian tarjeta ni CVV al backend
- el checkout ocurre en PayPal
- la vista admin no expone `client_secret`

## Lo que sigue pendiente

Esta fase NO implementa:

- `ticket_orders`
- pago real de boletos
- QR real
- validacion de entradas
- envio/logistica
- fulfillment
- refunds
- cupones reales
- carrito persistente en backend

## Relacion con otras fases

- boletos siguen sin pago real
- membresias siguen usando su flujo propio
- tienda ahora ya tiene:
  - catalogo real
  - orden real
  - pago PayPal

La siguiente fase natural para tienda seria:

- envios basicos
- fulfillment
- cupones reales
- o una fase posterior de refunds, si negocio lo pide
