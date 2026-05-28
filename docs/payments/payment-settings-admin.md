# Payment Settings Admin — Phase 3A

Fecha: 2026-05-27

## Status

DONE — Phase 3A implementada y verificada.

---

## Qué se implementó

### Migración

- `database/migrations/2026_05_27_000800_create_payment_settings_table.php`
- Tabla `payment_settings` con campos: `id`, `provider` (unique), `mode`, `client_id`, `client_secret`, `webhook_id`, `currency`, `is_enabled`, `metadata`, `timestamps`

### Modelo de dominio

- `app/Domain/Payments/Models/PaymentSetting.php`
- Cast `encrypted` en `client_secret` — Laravel cifra/descifra automáticamente con `APP_KEY`
- Cast `boolean` en `is_enabled`
- Cast `array` en `metadata`
- Singleton por proveedor vía constraint `unique('provider')`

### Request de validación

- `app/Http/Requests/Admin/Payments/UpdatePaymentSettingRequest.php`
- Autorización: requiere `payment_settings.update` en guard `admin`
- `mode`: requerido, `in:sandbox,live`
- `client_id`: nullable string max 255
- `client_secret`: nullable string max 500
- `webhook_id`: nullable string max 255
- `currency`: requerido, string, exactamente 3 caracteres (ISO 4217)
- `is_enabled`: opcional, boolean

### Controlador admin

- `app/Http/Controllers/Admin/PaymentSettingController.php`
- `edit()`: devuelve vista con singleton de paypal (crea si no existe)
- `update()`: valida, omite `client_secret` si viene vacío (preserva el actual), actualiza, registra audit con secret enmascarado

### Vista Blade

- `resources/views/admin/payment-settings/edit.blade.php`
- Formulario: selector de modo, client_id, client_secret, webhook_id, currency, is_enabled
- Nota de seguridad visible en pantalla
- Si ya existe un secret: muestra "Secret configurado" + input vacío para reemplazo opcional
- Nunca muestra el valor completo del secret

### Permisos

- `AccessControlSeeder` actualizado con:
  - `payment_settings.view` — "View payment settings"
  - `payment_settings.update` — "Update payment settings"
- El superadmin recibe automáticamente ambos permisos por la lógica de `$superadmin->permissions()->sync(...)`

### Rutas admin

- `GET  /admin/payment-settings` → `admin.payment-settings.edit` (requiere `payment_settings.view`)
- `PUT  /admin/payment-settings` → `admin.payment-settings.update` (requiere `payment_settings.update`)
- Ambas dentro del grupo `auth:admin`

### Sidebar

- `resources/views/layouts/admin/app.blade.php` actualizado
- Enlace "Payment Settings" visible solo para admins con `payment_settings.view`

### Seeder

- `database/seeders/PaymentSettingSeeder.php` — crea registro inicial:
  - `provider = paypal`, `mode = sandbox`, `currency = USD`, `is_enabled = false`
  - Todos los campos de credenciales en null
- `DatabaseSeeder` actualizado para llamar `PaymentSettingSeeder`

### Auditoría

- Cada `PUT` a `/admin/payment-settings` genera un `AuditLog` con:
  - `module = payment_settings`
  - `action = updated`
  - `old_values` y `new_values` con `client_secret` enmascarado como `***`
  - El valor real del secret nunca toca la tabla `audit_logs`

### Tests

- `tests/Feature/Admin/Payments/PaymentSettingTest.php`
- 10 tests — ver sección "Tests incluidos"

---

## Qué NO se implementó

- No existe `PaymentProvider` interface
- No existe `PayPalPaymentProvider`
- No existe tabla `payments`
- No existe tabla `payment_events`
- No existen endpoints públicos de pago (`/payment/create-order`, `/payment/capture-order`)
- No existe `PayPalWebhookController`
- No se llama a ninguna API de PayPal
- No se conectó ningún módulo de comercio (boletos, tienda, membresías)
- No hay checkout real de ningún tipo

---

## Cómo configurar PayPal en backoffice

1. Iniciar sesión en `/admin/login` con credenciales de administrador
2. Navegar a "Payment Settings" en el sidebar (visible solo con permiso `payment_settings.view`)
3. Seleccionar **Modo**: `Sandbox` para pruebas, `Live` para producción
4. Ingresar **Client ID** de tu aplicación PayPal
5. Ingresar **Client Secret** — el campo no mostrará el valor guardado, solo indica si existe
6. Ingresar **Webhook ID** (cuando se implemente el receptor de webhooks)
7. Confirmar **Moneda** (default: USD)
8. **No activar** el toggle "Activar PayPal" hasta que Payment Foundation esté completo
9. Guardar

**Importante**: dejar `is_enabled = false` hasta que las fases 3B–3E estén implementadas y probadas en sandbox.

---

## Seguridad del client_secret

- Se almacena con `encrypted` cast de Laravel (cifrado simétrico con `APP_KEY`)
- Nunca se incluye en respuestas API ni en props de Inertia
- Nunca se muestra completo en formularios admin
- Nunca se registra completo en `audit_logs` — se reemplaza por `***`
- Si `APP_KEY` rota, el secret existente no podrá descifrarse — hacer backup del `APP_KEY` antes de rotarlo

---

## Decisión aceptada: Almacenamiento de credenciales PayPal

Fecha: 2026-05-27

**Se usará almacenamiento en DB cifrado con `encrypted` cast de Laravel.**

### Razón de la elección

El administrador debe poder configurar PayPal desde el backoffice sin tocar archivos `.env` ni requerir acceso al servidor. Por tanto, las credenciales se guardan en la tabla `payment_settings`, con cifrado transparente gestionado por Laravel.

### Qué significa esto en la práctica

- Las credenciales (`client_id`, `client_secret`, `webhook_id`) viven en `payment_settings`.
- No se usa `.env` como fuente principal para estas credenciales en runtime.
- El frontend nunca recibirá `client_secret` bajo ninguna circunstancia.
- La rotación de credenciales se hace desde `/admin/payment-settings`, no por deploy.
- Si se sospecha filtración, se generan nuevas credenciales desde el panel PayPal y se reemplazan en el backoffice.

### Condiciones que deben mantenerse siempre

1. `client_secret` nunca se muestra completo en pantalla.
2. `client_secret` nunca se envía al frontend público.
3. `client_secret` nunca se guarda plano en `audit_logs` — siempre `***`.
4. Solo admins con permiso `payment_settings.update` pueden modificar credenciales.
5. `APP_KEY` debe mantenerse estable en producción.
6. PayPal sigue desactivado por defecto (`is_enabled = false`).
7. La existencia de esta configuración NO significa que ya existan pagos reales.

### Riesgo operativo — APP_KEY

Si `APP_KEY` cambia, los valores cifrados con el key anterior quedan ilegibles. Mitigación obligatoria antes de producción: mantener backup del `APP_KEY` y documentar el procedimiento de rotación.

---

## Tests incluidos

| Test | Descripción |
|---|---|
| `test_admin_with_payment_settings_view_can_view_page` | Permiso view permite acceder |
| `test_admin_without_payment_settings_view_cannot_view_page` | Sin permiso → 403 |
| `test_admin_with_payment_settings_update_can_update` | Permiso update persiste cambios |
| `test_admin_without_payment_settings_update_cannot_update` | Sin permiso → 403 |
| `test_client_secret_is_not_shown_in_view` | El valor del secret no aparece en el HTML |
| `test_client_secret_is_not_recorded_in_audit_log` | Audit log muestra `***`, no el valor real |
| `test_setting_is_created_automatically_if_not_exists` | `firstOrCreate` crea el singleton |
| `test_invalid_mode_is_rejected` | `mode` solo acepta `sandbox` o `live` |
| `test_invalid_currency_is_rejected` | `currency` debe ser exactamente 3 caracteres |
| `test_client_secret_is_preserved_when_empty_string_submitted` | Secret vacío no sobreescribe el existente |
| `test_no_public_payment_endpoints_exist` | Las rutas de pago futuras no existen aún |

---

## Próximos pasos

### Phase 3B — PaymentProvider abstraction
- Interface `PaymentProvider` en `app/Domain/Payments/Contracts/`
- `PayPalPaymentProvider` que implementa la interface contra sandbox
- `PaymentService` que resuelve el proveedor activo desde `PaymentSetting`
- Sin persistencia de pagos aún

### Phase 3C — Payments table y lifecycle
- Migración `payments` con relación polimórfica
- Migración `payment_events`
- Modelos `Payment` + `PaymentEvent`
- Enum `PaymentStatus` con transiciones controladas

### Phase 3D — PayPal create/capture
- Controlador público `PaymentController` (requiere `auth:web`)
- Crear orden PayPal → persistir `Payment` en `pending`
- Capturar orden → actualizar a `captured` con verificación

### Phase 3E — Webhooks
- `PayPalWebhookController` en `POST /webhooks/paypal`
- Validación de firma `PAYPAL-TRANSMISSION-SIG`
- Persistir todos los eventos como `PaymentEvent`

---

## Referencias

- `docs/payments/paypal-future-integration.md` — decisión original
- `docs/payments/payment-foundation-technical-review.md` — revisión técnica completa
- `docs/frontend/commercial-mock-flow-qa.md` — estado de pantallas mock
