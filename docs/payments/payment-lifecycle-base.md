# Payment Lifecycle Base — Phase 3B + 3C

Fecha: 2026-05-27

## Status

DONE — Phase 3B (PaymentProvider abstraction) + Phase 3C (Payments lifecycle base) implementadas y verificadas.

---

## Qué se implementó

### Enum de estados

**`app/Domain/Payments/Enums/PaymentStatus.php`**

Backed enum PHP 8.1+ con string values:

| Caso | Valor string | Descripción |
|---|---|---|
| `Pending` | `pending` | Pago creado localmente, aún sin involucrar proveedor |
| `ProviderCreated` | `provider_created` | Orden creada en PayPal, esperando aprobación del usuario |
| `Approved` | `approved` | Aprobado por el usuario en PayPal, pendiente de captura |
| `Captured` | `captured` | Dinero capturado. Estado final exitoso |
| `Failed` | `failed` | Falló en algún punto del proceso |
| `Cancelled` | `cancelled` | Cancelado por el usuario o el sistema |
| `Refunded` | `refunded` | Devuelto total o parcialmente |

### Interface del proveedor

**`app/Domain/Payments/Contracts/PaymentProvider.php`**

Contrato que cualquier proveedor de pagos debe implementar:

```php
interface PaymentProvider
{
    public function createOrder(Payment $payment): PaymentProviderResult;
    public function captureOrder(Payment $payment, array $payload = []): PaymentProviderResult;
    public function refund(Payment $payment, array $payload = []): PaymentProviderResult;
}
```

Ninguna implementación concreta existe todavía. `PayPalPaymentProvider` se crea en Phase 3D.

### DTO de resultado

**`app/Domain/Payments/Data/PaymentProviderResult.php`**

Clase `readonly` para normalizar respuestas de cualquier proveedor:

| Propiedad | Tipo | Descripción |
|---|---|---|
| `success` | `bool` | Si la operación fue exitosa |
| `status` | `?string` | Estado reportado por el proveedor |
| `providerOrderId` | `?string` | ID de orden en el proveedor |
| `providerCaptureId` | `?string` | ID de captura en el proveedor |
| `redirectUrl` | `?string` | URL de redirección PayPal si aplica |
| `rawPayload` | `array` | Respuesta cruda del proveedor |
| `message` | `?string` | Mensaje de error si `success = false` |

Factory methods disponibles: `PaymentProviderResult::success(...)` y `PaymentProviderResult::failure(...)`.

### Modelo Payment

**`app/Domain/Payments/Models/Payment.php`**

| Campo | Tipo | Cast | Descripción |
|---|---|---|---|
| `id` | bigint PK | — | |
| `payable_type` | string nullable | — | Polimórfico: clase de la orden asociada |
| `payable_id` | bigint nullable | — | ID de la orden asociada |
| `provider` | string | — | Proveedor (default: `paypal`) |
| `provider_order_id` | string nullable | — | ID de orden en PayPal |
| `provider_capture_id` | string nullable | — | ID de captura en PayPal |
| `status` | string | `PaymentStatus::class` | Estado del pago |
| `currency` | char(3) | — | Moneda ISO 4217 (default: USD) |
| `amount` | decimal(10,2) | `decimal:2` | Monto |
| `description` | text nullable | — | Descripción del pago |
| `customer_email` | string nullable | — | Email del pagador |
| `customer_name` | string nullable | — | Nombre del pagador |
| `metadata` | json nullable | `array` | Datos internos de la app (incluye `failure_reason`) |
| `provider_payload` | json nullable | `array` | Respuesta cruda del proveedor |
| `approved_at` | timestamp nullable | `datetime` | |
| `captured_at` | timestamp nullable | `datetime` | |
| `failed_at` | timestamp nullable | `datetime` | |
| `cancelled_at` | timestamp nullable | `datetime` | |
| `refunded_at` | timestamp nullable | `datetime` | |

Índices: `(provider, provider_order_id)`, `status`, `nullableMorphs('payable')`.

Relación: `payable(): MorphTo` — polimórfica nullable con `membership_orders`, `ticket_orders`, `store_orders` (cuando existan).

### Migración

**`database/migrations/2026_05_27_000900_create_payments_table.php`**

### Factory

**`database/factories/PaymentFactory.php`**

Estados disponibles: `pending()`, `captured()`, `failed()`, `cancelled()`.

### PaymentLifecycleService

**`app/Domain/Payments/Services/PaymentLifecycleService.php`**

Gestiona el ciclo de vida local del pago. No llama a ninguna API externa.

| Método | Acción |
|---|---|
| `createPendingPayment(array $data)` | Crea Payment con `status = pending`. Lanza `InvalidArgumentException` si `amount <= 0`. |
| `markProviderCreated(Payment, PaymentProviderResult)` | Status → `provider_created`. Guarda `provider_order_id` y `provider_payload`. |
| `markApproved(Payment, array)` | Status → `approved`. Registra `approved_at`. Guarda payload opcional. |
| `markCaptured(Payment, PaymentProviderResult\|array)` | Status → `captured`. Registra `captured_at`. Guarda `provider_capture_id`. Guard: `amount > 0`. |
| `markFailed(Payment, ?string, array)` | Status → `failed`. Registra `failed_at`. Mensaje va a `metadata['failure_reason']`. |
| `markCancelled(Payment, array)` | Status → `cancelled`. Registra `cancelled_at`. |
| `markRefunded(Payment, array)` | Status → `refunded`. Registra `refunded_at`. |

### Tests

**`tests/Feature/Payments/PaymentLifecycleTest.php`**

15 tests, 44 assertions.

---

## Qué NO se implementó

- No existe `PayPalPaymentProvider` — la interface define el contrato, pero ninguna implementación llama a PayPal
- No existen endpoints públicos de pago (`/payment/*`, `/webhooks/paypal`)
- No existe `payment_events` table — se crea en Phase 3E junto a los webhooks
- No hay checkout real de ningún tipo
- No hay integración con módulos comerciales (boletos, tienda, membresías)
- No se almacenan datos de tarjeta ni CVV
- No se llama a ninguna API externa

---

## Transiciones de estado esperadas

```
pending
  └─> provider_created   (markProviderCreated — orden creada en PayPal)
        └─> approved      (markApproved — usuario aprobó en PayPal)
              └─> captured  (markCaptured — captura exitosa)
                    └─> refunded  (markRefunded — devolución)

pending / provider_created / approved
  └─> failed    (markFailed — error en cualquier punto)
  └─> cancelled (markCancelled — usuario o sistema cancela)
```

El servicio no valida la secuencia de transiciones en esta fase — se delega al flujo de Phase 3D cuando el proveedor real dicte qué estados son válidos desde dónde.

---

## Próximos pasos

### Phase 3D — PayPalPaymentProvider ⏳ PLAN LISTO

Plan técnico completo disponible en `docs/payments/phase-3d-paypal-sandbox-plan.md`.

Resumen:
- Implementar `PayPalPaymentProvider` que implemente `PaymentProvider`
- `createOrder()`: `POST /v2/checkout/orders` en sandbox
- `captureOrder()`: `POST /v2/checkout/orders/{id}/capture`
- Lee credenciales de `PaymentSetting` (Phase 3A)
- Tests con `Http::fake()` — sin llamadas reales a PayPal
- Conectar `PaymentLifecycleService` al flujo real

### Phase 3E — Webhooks

- Migración `payment_events`
- `PayPalWebhookController` en `POST /webhooks/paypal`
- Validar firma `PAYPAL-TRANSMISSION-SIG`
- Persistir todos los eventos como `PaymentEvent` (inmutable)
- Procesar eventos relevantes: `PAYMENT.CAPTURE.COMPLETED`, etc.

### Phase 3F — Conectar membresías

- Tabla `membership_orders`
- Relacionar `Payment` con `MembershipOrder` vía `payable_type/payable_id`
- `/registro-tribu` deja de ser mock

---

## Referencias

- `docs/payments/paypal-future-integration.md` — decisión original
- `docs/payments/payment-foundation-technical-review.md` — revisión técnica completa
- `docs/payments/payment-settings-admin.md` — Phase 3A
