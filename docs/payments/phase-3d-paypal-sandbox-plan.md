# Phase 3D — PayPal Sandbox Provider: Plan Técnico

Fecha: 2026-05-27

Estado: DONE — implementado y verificado el 2026-05-27. Ver `docs/payments/paypal-sandbox-provider.md` para el cierre.

---

## Objetivo

Implementar `PayPalPaymentProvider` que satisfaga la interface `PaymentProvider` usando la API de PayPal Sandbox. Lee las credenciales desde `PaymentSetting` (ya implementado en Phase 3A). Usa `PaymentLifecycleService` para actualizar estados (ya implementado en Phase 3B+3C).

Esta fase NO incluye endpoints públicos, NO conecta módulos de comercio, NO implementa webhooks.

---

## 1. Archivos a crear

### Proveedor principal

```
app/Domain/Payments/Providers/PayPalPaymentProvider.php
```

Implementa `App\Domain\Payments\Contracts\PaymentProvider`.

Responsabilidades:
- Cargar `PaymentSetting` para el proveedor `paypal`
- Validar que esté habilitado y con credenciales completas
- Obtener `access_token` de PayPal via OAuth client credentials
- Ejecutar `createOrder` y `captureOrder` contra la API de PayPal
- Convertir respuesta a `PaymentProviderResult`
- Nunca loguear `client_secret`
- Nunca incluir `client_secret` en `provider_payload`

### Excepción de configuración

```
app/Domain/Payments/Exceptions/PaymentProviderException.php
```

Excepción específica para errores de configuración del proveedor (no habilitado, credenciales faltantes).
Distinguible de errores de red/API para que el llamador pueda manejarlos por separado.

### Tests

```
tests/Feature/Payments/PayPalPaymentProviderTest.php
```

Usa `Http::fake()`. Cero llamadas reales a PayPal.

---

## 2. Archivos a modificar

Ninguno es modificación destructiva:

| Archivo | Cambio |
|---|---|
| `database/seeders/AccessControlSeeder.php` | No se modifica — los permisos de pagos ya existen |
| `routes/admin.php` | No se modifica en Phase 3D |

Los archivos de la interface y DTO ya existen — no se tocan.

---

## 3. Dependencias necesarias

### Composer (ya presente en Laravel)

- `illuminate/http` — `Http` facade para HTTP client con soporte de `Http::fake()`

No se requiere SDK de PayPal. Se usa la API REST directamente con el HTTP client de Laravel.

### Razón para no usar SDK PayPal oficial

El SDK de PayPal para PHP tiene dependencias pesadas y puede entrar en conflicto con otras dependencias. La API REST de PayPal es estable y bien documentada. El HTTP client de Laravel con `Http::fake()` permite tests controlados sin dependencias externas.

---

## 4. Configuración del HTTP client

### URLs base

```php
private function baseUrl(): string
{
    return $this->setting->mode === 'live'
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';
}
```

### Obtención de access_token (OAuth client credentials)

```
POST {baseUrl}/v1/oauth2/token
Authorization: Basic base64(client_id:client_secret)
Content-Type: application/x-www-form-urlencoded
Body: grant_type=client_credentials
```

Respuesta exitosa:
```json
{
  "access_token": "A21AAF...",
  "token_type": "Bearer",
  "expires_in": 32400
}
```

**Consideración de caché del token:**
En Phase 3D el token se obtiene por cada operación (sin caché). El TTL es ~9 horas — en producción futura se puede cachear con `Cache::put('paypal_token', $token, $expiresIn - 60)`. Documentar como mejora pero no implementar en Phase 3D.

**Protección del client_secret:**
- Solo usar en `withBasicAuth($clientId, $clientSecret)` del HTTP client
- Nunca incluir en mensajes de excepción
- Nunca loguear el valor completo
- Nunca almacenar en `provider_payload` del Payment

---

## 5. Flujo createOrder

### Qué se envía a PayPal

```json
POST /v2/checkout/orders
Authorization: Bearer {access_token}
Content-Type: application/json

{
  "intent": "CAPTURE",
  "purchase_units": [
    {
      "reference_id": "payment-{payment.id}",
      "amount": {
        "currency_code": "{payment.currency}",
        "value": "{payment.amount}"
      },
      "description": "{payment.description}"
    }
  ],
  "application_context": {
    "brand_name": "Veraguas United FC",
    "locale": "es-PA",
    "user_action": "PAY_NOW",
    "return_url": "{config return_url}",
    "cancel_url": "{config cancel_url}"
  }
}
```

Nota: `payment.amount` ya viene formateado como string `"50.00"` gracias al cast `decimal:2`.

### Respuesta exitosa de PayPal (HTTP 201)

```json
{
  "id": "PAYID-ABC123XYZ",
  "status": "CREATED",
  "links": [
    {"rel": "self", "href": "...", "method": "GET"},
    {"rel": "approve", "href": "https://www.sandbox.paypal.com/checkoutnow?token=...", "method": "GET"},
    {"rel": "update", "href": "...", "method": "PATCH"},
    {"rel": "capture", "href": "...", "method": "POST"}
  ]
}
```

### Conversión a PaymentProviderResult

```php
// Extraer el link con rel=approve
$approveLink = collect($response['links'])->firstWhere('rel', 'approve');

return PaymentProviderResult::success(
    providerOrderId: $response['id'],
    redirectUrl: $approveLink['href'] ?? null,
    rawPayload: $response,
    status: $response['status'],
);
```

### Respuesta de error de PayPal

```json
{
  "name": "UNPROCESSABLE_ENTITY",
  "message": "The requested action could not be performed...",
  "details": [{"issue": "...", "description": "..."}]
}
```

Conversión:
```php
return PaymentProviderResult::failure(
    message: $response['message'] ?? 'PayPal createOrder failed.',
    rawPayload: $response,
);
```

---

## 6. Flujo captureOrder

### Qué se envía a PayPal

```
POST /v2/checkout/orders/{provider_order_id}/capture
Authorization: Bearer {access_token}
Content-Type: application/json
Body: {} (vacío o con payment_source si se usa JS SDK)
```

La captura toma el ID de orden almacenado en `Payment::provider_order_id`.

### Respuesta exitosa de PayPal (HTTP 201)

```json
{
  "id": "PAYID-ABC123XYZ",
  "status": "COMPLETED",
  "purchase_units": [
    {
      "payments": {
        "captures": [
          {
            "id": "CAP-XYZ789",
            "status": "COMPLETED",
            "amount": {"currency_code": "USD", "value": "50.00"},
            "final_capture": true
          }
        ]
      }
    }
  ]
}
```

### Conversión a PaymentProviderResult

```php
$captureId = data_get($response, 'purchase_units.0.payments.captures.0.id');

return PaymentProviderResult::success(
    providerOrderId: $response['id'],
    providerCaptureId: $captureId,
    rawPayload: $response,
    status: $response['status'],
);
```

---

## 7. Manejo de errores

| Escenario | Respuesta PayPal | Acción |
|---|---|---|
| Credenciales inválidas | HTTP 401 `AUTHENTICATION_FAILURE` | `PaymentProviderException` — error de config |
| Orden no encontrada | HTTP 404 | `PaymentProviderResult::failure` |
| Orden ya capturada (idempotencia) | HTTP 422 `ORDER_ALREADY_CAPTURED` | Detectar y retornar success con datos existentes |
| Instrumento declinado | HTTP 422 `INSTRUMENT_DECLINED` | `PaymentProviderResult::failure` con detalle |
| Timeout de red | `ConnectionException` | Capturar, retornar `PaymentProviderResult::failure` con mensaje genérico |
| PayPal no habilitado | — (local) | Lanzar `PaymentProviderException` antes de cualquier llamada HTTP |
| Credenciales vacías | — (local) | Lanzar `PaymentProviderException` antes de cualquier llamada HTTP |

**Regla de oro:** Los errores de configuración (proveedor no habilitado, credenciales faltantes) lanzan excepción. Los errores del proveedor (PayPal rechazó la operación) retornan `PaymentProviderResult::failure`.

---

## 8. Qué datos del Payment se envían a PayPal

| Campo del Payment | Donde va en PayPal | Notas |
|---|---|---|
| `amount` | `purchase_units[0].amount.value` | Formato string "50.00" |
| `currency` | `purchase_units[0].amount.currency_code` | "USD" |
| `description` | `purchase_units[0].description` | Nullable, omitir si null |
| `id` (local) | `purchase_units[0].reference_id` | Como "payment-{id}" para trazar |
| `provider_order_id` | URL del capture: `orders/{id}/capture` | Solo en captureOrder |

---

## 9. Qué NO se debe enviar a PayPal

| Dato | Razón |
|---|---|
| `client_secret` (el de PaymentSetting) | Solo va en Basic Auth del token endpoint — nunca en payloads |
| `APP_KEY` | Nunca sale del servidor |
| IDs de admin, roles, permisos | No tienen relación con el pago |
| `metadata` interno del Payment | Datos internos de la app, no del proveedor |
| Tokens de sesión web (`auth:web`) | No deben exponerse |
| Datos de tarjeta | No se procesan tarjetas directamente |

---

## 10. Validaciones antes de llamar PayPal

El proveedor debe verificar localmente antes de hacer cualquier llamada HTTP:

```
1. ¿Existe PaymentSetting para 'paypal'?         → si no, PaymentProviderException
2. ¿is_enabled = true?                            → si no, PaymentProviderException
3. ¿client_id no está vacío?                      → si no, PaymentProviderException
4. ¿client_secret no está vacío?                  → si no, PaymentProviderException
5. ¿Payment.amount > 0?                           → si no, PaymentProviderException (captureOrder también)
6. ¿Payment.provider_order_id existe?             → necesario para captureOrder
```

---

## 11. Sandbox vs Live

```php
// Determinar URL base según mode en PaymentSetting
$baseUrl = match($this->setting->mode) {
    'live'    => 'https://api-m.paypal.com',
    default   => 'https://api-m.sandbox.paypal.com',
};
```

**Regla:** En Phase 3D solo se prueba con sandbox. El switch a `live` solo se activa cuando el checklist de Phase 3D esté completo y auditado.

Las credenciales sandbox (`client_id` + `client_secret` de la cuenta sandbox de PayPal Developer) se configuran desde `/admin/payment-settings` con `mode = sandbox`. No se hardcodean.

---

## 12. Tests con Http::fake() — sin llamadas reales

### Estructura básica

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    '*/v1/oauth2/token' => Http::response([
        'access_token' => 'fake-access-token',
        'token_type'   => 'Bearer',
        'expires_in'   => 32400,
    ], 200),

    '*/v2/checkout/orders' => Http::response([
        'id'     => 'PAYID-TESTORDER123',
        'status' => 'CREATED',
        'links'  => [
            ['rel' => 'approve', 'href' => 'https://sandbox.paypal.com/checkoutnow?token=TEST123'],
        ],
    ], 201),

    '*/v2/checkout/orders/*/capture' => Http::response([
        'id'     => 'PAYID-TESTORDER123',
        'status' => 'COMPLETED',
        'purchase_units' => [[
            'payments' => ['captures' => [['id' => 'CAP-TESTCAPTURE456']]]
        ]],
    ], 201),
]);
```

### Tests mínimos a implementar

| Test | Verifica |
|---|---|
| `test_create_order_returns_success_result` | Result success, providerOrderId, redirectUrl |
| `test_create_order_stores_raw_payload` | rawPayload contiene respuesta PayPal |
| `test_create_order_fails_on_paypal_error` | HTTP 422 → PaymentProviderResult::failure |
| `test_capture_order_returns_success_with_capture_id` | Result success, providerCaptureId |
| `test_capture_order_fails_on_already_captured` | HTTP 422 ORDER_ALREADY_CAPTURED detectado |
| `test_provider_throws_if_not_enabled` | `is_enabled = false` → PaymentProviderException |
| `test_provider_throws_if_credentials_missing` | client_secret null → PaymentProviderException |
| `test_client_secret_not_in_provider_payload` | rawPayload no contiene el secret |
| `test_auth_token_request_uses_basic_auth` | `Http::assertSent()` verifica Basic Auth correcto |
| `test_sandbox_url_used_when_mode_is_sandbox` | URL contiene `api-m.sandbox.paypal.com` |
| `test_live_url_used_when_mode_is_live` | URL contiene `api-m.paypal.com` |
| `test_network_error_returns_failure_result` | `Http::fake()` simula `ConnectionException` |

**Importante:** `Http::assertSent()` permite verificar que el header `Authorization: Basic ...` está presente, pero NO debe comparar con el valor real del secret. Verificar solo la presencia del header, no el contenido.

---

## 13. return_url y cancel_url

En Phase 3D, estas URLs son configurables o placeholders. Las rutas frontend aún no existen.

Opciones:
- **Opción A:** Leer desde `PaymentSetting::metadata['return_url']` y `['cancel_url']` (ya existe el campo `metadata` json nullable)
- **Opción B:** Definir en config (`config/payments.php`) como variables de entorno
- **Opción C:** Recibir como parámetros en `createOrder`

**Recomendación para Phase 3D:** Opción B (config/env) para sandbox. Cuando en Phase 3F se conecten membresías, las URLs vendrán del dominio correspondiente.

Decidir antes de implementar.

---

## 14. Lo que queda fuera de Phase 3D

| Capacidad | Fase |
|---|---|
| Webhooks y `payment_events` | Phase 3E |
| `PaymentController` público | Segunda parte de Phase 3D o inicio de 3F |
| Conexión con módulos comerciales | Phase 3F, 3G, 3H |
| Frontend de checkout | Phase 3F+ |
| Reembolsos via PayPal | Posterior a Phase 3E |
| Caché de access_token | Mejora futura |

---

## 15. Checklist antes de implementar Phase 3D

- [ ] Confirmar cuenta PayPal Business / Developer activa
- [ ] Crear app sandbox en developer.paypal.com y obtener `client_id` + `client_secret` sandbox
- [ ] Configurar credenciales sandbox desde `/admin/payment-settings` con `mode = sandbox`, `is_enabled = false`
- [ ] Decidir `return_url` y `cancel_url` para sandbox (ver sección 13)
- [ ] Confirmar que `Http` facade es compatible con el entorno (debería serlo — viene con Laravel)
- [ ] Revisar si se necesita `composer require guzzlehttp/guzzle` explícitamente o si ya viene con Laravel (normalmente ya viene)

---

## 16. Riesgos y decisiones pendientes

| Riesgo / Decisión | Mitigación |
|---|---|
| Token PayPal expira durante una operación larga | En Phase 3D re-obtener por cada request. Agregar caché en fase posterior |
| PayPal rechaza por payer info faltante | Verificar si `customer_email` es requerido en purchase_units según el flujo elegido |
| `ORDER_ALREADY_CAPTURED` en reintentos | Detectar el error específico y retornar success con datos existentes — idempotencia |
| URLs de return/cancel no existen en frontend aún | Usar placeholder URLs en sandbox hasta Phase 3F |
| `Http::fake()` no cubre todos los paths de error PayPal | Añadir tests de error específicos por cada código PayPal conocido |
| `ConnectionException` silenciosa | Siempre retornar `PaymentProviderResult::failure` con mensaje — nunca propagar la excepción cruda al llamador |

---

## Referencias

- `docs/payments/payment-settings-admin.md` — Phase 3A (PaymentSetting ya disponible)
- `docs/payments/payment-lifecycle-base.md` — Phase 3B+3C (Payment + lifecycle ya disponibles)
- `docs/payments/payment-foundation-technical-review.md` — revisión técnica global
- `docs/payments/paypal-future-integration.md` — decisión original
- API PayPal REST: `https://developer.paypal.com/docs/api/orders/v2/`
- OAuth PayPal: `https://developer.paypal.com/api/rest/authentication/`
