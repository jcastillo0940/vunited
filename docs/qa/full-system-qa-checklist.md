# Full System QA Checklist — Veraguas United FC

**Versión:** Phase 4H  
**Fecha:** 2026-05-28  
**Tests automatizados:** 277/277 passing  
**Scope:** Flujo completo de membresías, tienda, boletos, PayPal sandbox, admin

Instrucciones: marcar cada ítem con ✅ (OK), ❌ (falla), o ⚠️ (observación).

---

## 0. Precondiciones

- [ ] `php artisan migrate:fresh --seed` completado sin errores
- [ ] `npm run build` completado sin errores
- [ ] Servidor levantado: `php artisan serve`
- [ ] PayPal sandbox habilitado en `/admin/payment-settings`
- [ ] Cuenta PayPal buyer de sandbox disponible

---

## 1. Rutas públicas — carga básica

| Ruta | Esperado | Estado |
|---|---|---|
| `/` | Home carga, ticker funciona | |
| `/boletos` | Lista partidos y zonas | |
| `/tienda` | Lista productos | |
| `/carrito` | Carrito vacío o con ítems | |
| `/registro-tribu` | Formulario de membresía | |
| `/registro-confirmado` | Página de confirmación | |
| `/noticias` | Lista de noticias | |
| `/calendario` | Calendario de partidos | |
| `/estadio` | Información del estadio | |
| `/plantilla` | Plantel del equipo | |

---

## 2. Flujo de membresía

### 2A. Registro exitoso (sandbox)
- [ ] Ir a `/registro-tribu`
- [ ] Llenar formulario con datos válidos
- [ ] Seleccionar un plan disponible
- [ ] Hacer clic en "Continuar con PayPal"
- [ ] Verificar redirect a PayPal sandbox
- [ ] Completar pago con cuenta buyer sandbox
- [ ] Verificar redirect a `/registro-confirmado?order=MEMBER-XXXX`
- [ ] Verificar que la página muestra estado `pending_payment` inicialmente
- [ ] Esperar webhook `PAYMENT.CAPTURE.COMPLETED` (o simular con curl)
- [ ] Verificar que el estado cambia a `paid`

### 2B. Admin — orden de membresía
- [ ] Ir a `/admin/membership-orders`
- [ ] Verificar que la orden aparece
- [ ] Abrir el detalle `/admin/membership-orders/{id}`
- [ ] Verificar: `status = paid`, `paid_at` no null, `starts_at` y `expires_at` presentes

### 2C. Casos de error
- [ ] Cancelar en PayPal → orden queda `pending_payment` (no falla)
- [ ] Plan inactivo → API retorna 422 con mensaje claro
- [ ] Email inválido → validación frontend o backend

---

## 3. Flujo de tienda

### 3A. Carrito y checkout
- [ ] Ir a `/tienda`
- [ ] Agregar un producto al carrito
- [ ] Ir a `/carrito` → verificar ítems
- [ ] Hacer clic en "Comprar" → redirect a PayPal sandbox
- [ ] Completar pago con buyer sandbox
- [ ] Verificar redirect a `/orden-tienda-confirmada?order=STORE-XXXX`
- [ ] Verificar estado `pending_payment` → luego `paid` post-webhook

### 3B. Stock
- [ ] Producto con `track_stock = true`: verificar que `stock_quantity` decrementa tras webhook `PAYMENT.CAPTURE.COMPLETED`
- [ ] Producto sin stock (`stock_quantity = 0`): verificar que el sistema lo maneja

### 3C. Admin — orden de tienda
- [ ] Ir a `/admin/store-orders`
- [ ] Verificar orden aparece con status correcto
- [ ] Abrir detalle: items, subtotal, total, payment link

---

## 4. Flujo de boletos

### 4A. Compra de boletos
- [ ] Ir a `/boletos`
- [ ] Seleccionar partido activo
- [ ] Seleccionar zona y cantidad
- [ ] Completar datos del comprador
- [ ] Hacer clic en "Pagar con PayPal" → redirect a sandbox
- [ ] Completar pago
- [ ] Verificar redirect a `/orden-boletos-confirmada?order=TICKET-XXXX`

### 4B. Estado `pending_payment`
- [ ] La página muestra mensaje: "Los boletos se emiten automáticamente cuando PayPal confirme el pago"
- [ ] **No se muestran boletos digitales**
- [ ] **No se muestra QR ni código**

### 4C. Estado `paid` — boletos emitidos
- [ ] Post-webhook, recargar `/orden-boletos-confirmada?order=TICKET-XXXX`
- [ ] La página muestra sección "Boletos Digitales"
- [ ] Se muestra un card por cada asiento (zona × cantidad)
- [ ] Cada card muestra: seat label, zone name, status badge "Válido"
- [ ] El QR placeholder muestra el token de 40 chars en monospace
- [ ] `seat_label` tiene formato "Zona #N" (ej: "General Norte #1")

### 4D. Estado `failed` / `cancelled`
- [ ] La página muestra aviso rojo: "No se generaron boletos digitales"
- [ ] **No se muestra sección de boletos**

### 4E. Decremento de capacidad
- [ ] Zona con `available_quantity` limitada: verificar que decrementa tras webhook

---

## 5. Validación de boletos en entrada (admin)

### 5A. Ticket válido
- [ ] Ir a `/admin/issued-tickets`
- [ ] Buscar un ticket con status `issued`
- [ ] Copiar el token completo (40 chars)
- [ ] Ejecutar:
  ```bash
  curl -X POST /admin/tickets/validate \
    -H "Content-Type: application/json" \
    -d '{"token":"<token>"}'
  ```
  (con sesión admin activa)
- [ ] Respuesta: `{"valid": true, "ticket": {..., "status": "used"}}`
- [ ] Verificar en DB que `status = used` y `used_at` no null

### 5B. Ticket ya usado
- [ ] Repetir el mismo token del paso 5A
- [ ] Respuesta: `{"valid": false, "reason": "already_used"}`

### 5C. Token inválido
- [ ] Enviar token de 40 chars que no existe en DB
- [ ] Respuesta: `{"valid": false, "reason": "not_found"}`

### 5D. Sin autenticación admin
- [ ] Llamar al endpoint sin sesión admin
- [ ] Respuesta: 401 Unauthorized

---

## 6. Admin — monitoreo de pagos

### 6A. Payment monitor
- [ ] Ir a `/admin/payments`
- [ ] Verificar pagos listados con status correcto
- [ ] Abrir un pago: `/admin/payments/{id}`
- [ ] Verificar que `safeProviderPayload` NO muestra `client_secret` ni datos de tarjeta
- [ ] Verificar que `safeMetadata` oculta keys sensibles con `***`

### 6B. Payment events
- [ ] Ir a `/admin/payment-events`
- [ ] Verificar eventos del webhook listados
- [ ] Abrir evento: verificar `verification_status` y `processing_status`
- [ ] Evento procesado: `processing_status = processed`
- [ ] Evento desconocido: `processing_status = ignored`

### 6C. Payment settings
- [ ] Ir a `/admin/payment-settings`
- [ ] Si hay `client_secret` configurado: verificar que el campo muestra "Secret configurado" y NO muestra el valor real
- [ ] El input type es `password` con `value=""`
- [ ] El hint de seguridad está visible

---

## 7. Admin — issued tickets

### 7A. Lista
- [ ] Ir a `/admin/issued-tickets`
- [ ] Verificar tickets listados con status badge de color
- [ ] Filtrar por `status=used` → solo muestra usados
- [ ] Buscar por `order_number` → filtra correctamente

### 7B. Detalle
- [ ] Abrir un ticket: `/admin/issued-tickets/{id}`
- [ ] Verificar: token completo, seat_label, zone_name, status, issued_at
- [ ] Link al ticket_order funciona
- [ ] Información del partido visible

### 7C. Desde ticket order
- [ ] Abrir `/admin/ticket-orders/{id}` de una orden pagada
- [ ] Verificar sección "Issued Tickets" con tabla de boletos
- [ ] Link "View" lleva al detalle del ticket

---

## 8. Seguridad — exposición de datos sensibles

### 8A. client_secret
- [ ] `GET /api/site-settings` → NO contiene `client_secret`
- [ ] `GET /api/ticketing/orders/{n}` → NO contiene `client_secret`
- [ ] `GET /api/ticketing/orders/{n}/tickets` → NO contiene `client_secret`
- [ ] Admin payment settings blade → campo `value=""` (nunca el secret real)
- [ ] Audit log entry para payment_settings → `client_secret` aparece como `***`

### 8B. Datos de tarjeta
- [ ] `POST /api/ticketing/orders` con `card_number` y `card_cvv` en body → no aparecen en DB
- [ ] `POST /api/store/orders` con `card_number` → no aparece en `payments.metadata`
- [ ] Admin payments show → payload no contiene `card_number` ni `cvv` visibles

### 8C. Rutas admin sin auth
- [ ] `GET /admin/payment-settings` sin sesión → redirect a `/admin/login`
- [ ] `GET /admin/issued-tickets` sin sesión → redirect a `/admin/login`
- [ ] `POST /admin/tickets/validate` sin sesión (JSON request) → 401

### 8D. Permisos granulares
- [ ] Admin sin `issued_tickets.view` → 403 en `/admin/issued-tickets`
- [ ] Admin sin `issued_tickets.validate` → 403 en `POST /admin/tickets/validate`
- [ ] Admin sin `ticket_orders.view` → 403 en `/admin/ticket-orders`
- [ ] Admin sin `payment_settings.view` → 403 en `/admin/payment-settings`

---

## 9. Webhook PayPal — simulación

### 9A. PAYMENT.CAPTURE.COMPLETED
```bash
curl -X POST http://localhost:8000/api/webhooks/paypal \
  -H "Content-Type: application/json" \
  -d '{
    "id": "WH-TEST-QA-001",
    "event_type": "PAYMENT.CAPTURE.COMPLETED",
    "resource_type": "capture",
    "resource": {
      "id": "CAP-QA-001",
      "status": "COMPLETED",
      "supplementary_data": {
        "related_ids": {"order_id": "<provider_order_id>"}
      }
    }
  }'
```
- [ ] Respuesta: `{"received": true}` HTTP 200
- [ ] Orden asociada: `status = paid`
- [ ] Boletos emitidos si es TicketOrder
- [ ] Stock decrementado si es StoreOrder

### 9B. Idempotencia
- [ ] Reenviar mismo webhook con mismo `id` → `{"message": "Event already received."}`
- [ ] Sin doble procesamiento

### 9C. Evento desconocido
```bash
"event_type": "SOME.UNKNOWN.EVENT"
```
- [ ] Respuesta: HTTP 200 `{"received": true}`
- [ ] `payment_events.processing_status = ignored`

---

## 10. Performance y errores esperados

### 10A. Orden inexistente
- [ ] `GET /api/ticketing/orders/TICKET-9999-9999` → HTTP 404
- [ ] `GET /api/ticketing/orders/TICKET-9999-9999/tickets` → HTTP 404
- [ ] Página `/orden-boletos-confirmada?order=TICKET-9999-9999` → mensaje de orden no encontrada

### 10B. Sin parámetro order
- [ ] `/orden-boletos-confirmada` (sin `?order=`) → mensaje de orden no encontrada (sin crash)

### 10C. Partido inactivo
- [ ] `POST /api/ticketing/orders` con `match_event_code` de partido inactivo → HTTP 422

### 10D. Sin capacidad
- [ ] `POST /api/ticketing/orders` con `quantity` mayor a `available_quantity` → HTTP 422 con mensaje claro

---

## 11. Compatibilidad de base de datos

- [ ] `migrate:fresh --seed` con MySQL 9.x → ✅ (ya verificado con `engine=InnoDB`)
- [ ] Todas las FK constraints funcionan
- [ ] `issued_tickets` tabla creada correctamente con índices `ticket_order_id` y `status`

---

## Resumen de hallazgos Phase 4H

| # | Tipo | Descripción | Severidad | Estado |
|---|---|---|---|---|
| 1 | Config | `APP_DEBUG=true` en local | Media — normal para dev | Solo aplica staging+ |
| 2 | Config | `APP_URL=http://localhost` | Baja — normal para dev | Cambiar en staging |
| 3 | Seguridad | `client_secret` cifrado en DB, enmascarado en audit, oculto en UI | — | ✅ Protegido |
| 4 | Seguridad | CVV/tarjeta filtrados en todos los admin controllers | — | ✅ Protegido |
| 5 | Seguridad | `provider_payload` solo mostrado via `sanitize()` | — | ✅ Protegido |
| 6 | Features | PayPal live no implementado | Esperado | Fase futura |
| 7 | Features | Scanner QR real no implementado | Esperado | Fase futura |
| 8 | Coverage | 277 tests automatizados, 881 assertions | — | ✅ |
