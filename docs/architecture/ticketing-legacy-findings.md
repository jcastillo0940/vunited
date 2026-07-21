# Hallazgos del legado de boletería — antes de Fase 7

Investigación de `app/Domain/Ticketing` y `app/Domain/Payments` del monolito
actual, hecha antes de escribir código nuevo, para no repetir bugs reales ni
tirar trabajo aprovechable.

## Datos existentes (se migran)

`weveraguas` tiene 2 partidos (`match_events`) y 3 zonas
(`ticket_zones`: General/Preferencial/VIP Indio, con `capacity` y
`available_quantity` reales) — **cero órdenes y cero tickets emitidos**
(`ticket_orders`, `ticket_order_items`, `issued_tickets` están vacías). La
migración de la Fase 7 §14 es simple: solo eventos + zonas semilla, sin
datos de clientes reales que proteger.

## Bugs reales confirmados en la implementación actual (NO se migra el código)

1. **Sobreventa real posible.** `TicketOrderService::createOrder()` verifica
   `available_quantity < quantity` con un `SELECT` simple, sin bloqueo, y
   **nunca reserva capacidad en ese momento**. El único lugar que toca
   `available_quantity` es `PayPalWebhookProcessor::decrement('available_quantity', ...)`,
   que corre recién cuando el pago se confirma — y lo hace sin cláusula
   `WHERE available_quantity >= cantidad`, por lo que puede llevar el
   contador a **negativo** si varias órdenes concurrentes llegan a pagarse
   para el mismo cupo agotado. Es exactamente el escenario que la Fase 7
   prohíbe ("nunca permitir capacidad negativa o sobreventa").
2. **Doble escaneo no está protegido atómicamente.**
   `TicketValidationService::validate()` hace `SELECT` del estado,
   compara en PHP, y recién después hace `UPDATE` a `Used`. Dos escaneos
   simultáneos del mismo boleto pueden leer ambos `Issued` antes de que
   cualquiera escriba `Used`, y ambos devolver `valid: true`. Es
   exactamente la prueba obligatoria "doble escaneo simultáneo" de la
   Fase 7.
3. **Número de orden no es concurrency-safe.**
   `generateOrderNumber()` usa `COUNT(*) + 1` por año; bajo concurrencia
   puede colisionar (la `UNIQUE KEY` en `order_number` evita duplicar
   datos, pero el segundo request recibe un error 500 sin manejar).
4. **QR sin firma.** `qr_payload` es igual al `token` aleatorio de 20 bytes
   — sin PII (cumple esa parte), pero sin firma verificable: la única forma
   de validarlo es una consulta a la base, lo que hace imposible una
   pre-validación real sin conexión en el escáner (relevante para la
   contingencia de la Fase 7 §12).
5. **Proveedor de pago es PayPal**, no TiloPay, y vive embebido dentro del
   propio dominio de Ticketing (`TicketOrderService` llama directamente a
   `PayPalPaymentProvider`) — contradice la regla de la Fase 7 ("Ticketing
   no puede: Procesar TiloPay... Ticketing debe: solicitar payment intent"
   a través de Payments, no hablar con el proveedor directamente).

## Qué se aprovecha igual

- El **modelo de datos de zonas** (zona por partido, precio, capacidad,
  `is_active`) es razonable y se adapta a `veraguas_ticketing` con los
  ajustes atómicos necesarios.
- El **contrato de `TicketValidationService`** (respuestas
  `not_found`/`already_used`/`voided`/`valid`) es un buen punto de partida
  para las respuestas de la Fase 7 §8, solo que reimplementado con
  bloqueo atómico real.
- El **payload sin PII del QR** confirma que "solo un token opaco, sin
  nombre/correo/teléfono/precio" ya era la intención correcta — se firma
  además, no se cambia el principio.

## Credenciales de wallets — no disponibles todavía

Sin certificado Apple Pass Type ID (`.p12`) en el servidor y sin acceso a
Secret Manager (mismo bloqueo de IAM documentado en
`docs/security/secrets-management.md`). Se construye el código de emisión
de Google Wallet y Apple Wallet contra una interfaz correcta, con las
credenciales reales pendientes de configurar — ver
`docs/architecture/wallets.md`.
