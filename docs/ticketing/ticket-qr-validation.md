# Ticket QR + Validation — Phase 4F

**Status:** DONE — 2026-05-28  
**Tests:** 268/268 passing (850 assertions) — suite completo sin regresiones  
**Build:** `npm run build` limpio (Vite 2.44 s)  
**migrate:fresh --seed:** falla en MySQL local — ver sección al final

---

## Overview

Digital tickets are issued automatically when a `TicketOrder` transitions to `paid` status via the PayPal webhook `PAYMENT.CAPTURE.COMPLETED`. Each seat in the order gets its own `IssuedTicket` record with a unique 40-character hex token used for QR-based validation at the stadium.

Tests use SQLite (`DB_CONNECTION=sqlite`), no afectados por el problema MySQL local.

---

## Archivos creados (Phase 4F)

### Infraestructura de dominio

| Archivo | Descripción |
|---|---|
| `database/migrations/2026_05_28_000600_create_issued_tickets_table.php` | Tabla `issued_tickets` |
| `app/Domain/Ticketing/Enums/IssuedTicketStatus.php` | Enum `issued / used / voided` |
| `app/Domain/Ticketing/Exceptions/TicketIssuingException.php` | Excepción tipada de emisión |
| `app/Domain/Ticketing/Models/IssuedTicket.php` | Model Eloquent con casts y relaciones |
| `database/factories/IssuedTicketFactory.php` | Factory con states `used()` y `voided()` |

### Servicios

| Archivo | Descripción |
|---|---|
| `app/Domain/Ticketing/Services/TicketIssuingService.php` | Emite tickets; guard de status; idempotente |
| `app/Domain/Ticketing/Services/TicketValidationService.php` | Valida token; marca `used`; bloquea reuso |

### Controllers

| Archivo | Descripción |
|---|---|
| `app/Http/Controllers/Api/IssuedTicketController.php` | `forOrder()` — lista tickets de una orden (público) |
| `app/Http/Controllers/Admin/IssuedTicketController.php` | `index`, `show`, `validateTicket` — admin |

### Vistas admin

| Archivo | Descripción |
|---|---|
| `resources/views/admin/issued-tickets/index.blade.php` | Lista con filtros status/search |
| `resources/views/admin/issued-tickets/show.blade.php` | Detalle con token completo y QR payload |

### Tests

| Archivo | Tests | Cobertura |
|---|---|---|
| `tests/Feature/Ticketing/TicketIssuingTest.php` | 9 | Emisión, idempotencia, webhook auto-emission, denied no emite |
| `tests/Feature/Ticketing/TicketValidationTest.php` | 9 | Valid→used, not_found, already_used, voided, reuse bloqueado, API |
| `tests/Feature/Admin/Ticketing/IssuedTicketAdminTest.php` | 10 | Auth guards, permisos 403, CRUD vistas, validate endpoint |

---

## Archivos modificados (Phase 4F)

| Archivo | Cambio |
|---|---|
| `app/Domain/Ticketing/Models/TicketOrder.php` | Relación `issuedTickets(): HasMany` |
| `app/Domain/Ticketing/Models/TicketOrderItem.php` | Relación `issuedTickets(): HasMany` |
| `app/Domain/Payments/Services/PayPalWebhookProcessor.php` | Inyecta `TicketIssuingService`; llama `issueForOrder()` post-captura |
| `app/Http/Controllers/Admin/TicketOrderController.php` | Carga eager `issuedTickets` en `show()` |
| `routes/api.php` | `GET /api/ticketing/orders/{orderNumber}/tickets` |
| `routes/admin.php` | 3 rutas nuevas (ver abajo) |
| `resources/views/admin/ticket-orders/show.blade.php` | Sección "Issued Tickets" con tabla embebida |
| `resources/views/layouts/admin/app.blade.php` | Nav link "Issued Tickets" con permiso guard |
| `database/seeders/AccessControlSeeder.php` | Permisos `issued_tickets.view` y `issued_tickets.validate` |

---

## Rutas creadas

### API (pública)

| Método | URL | Nombre | Descripción |
|---|---|---|---|
| `GET` | `/api/ticketing/orders/{orderNumber}/tickets` | `ticketing.orders.tickets` | Lista tickets emitidos de una orden |

### Admin (requiere `auth:admin` + permiso)

| Método | URL | Nombre | Permiso | Descripción |
|---|---|---|---|---|
| `GET` | `/admin/issued-tickets` | `admin.issued-tickets.index` | `issued_tickets.view` | Lista todos los tickets con filtros |
| `GET` | `/admin/issued-tickets/{issuedTicket}` | `admin.issued-tickets.show` | `issued_tickets.view` | Detalle de un ticket |
| `POST` | `/admin/tickets/validate` | `admin.tickets.validate` | `issued_tickets.validate` | Valida un token → JSON |

---

## Permisos (AccessControlSeeder)

| Nombre | Label | Asignado a `superadmin` |
|---|---|---|
| `issued_tickets.view` | View issued tickets | Sí |
| `issued_tickets.validate` | Validate issued tickets | Sí |

---

## Domain Objects

### `IssuedTicket` model (`App\Domain\Ticketing\Models\IssuedTicket`)

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `ticket_order_id` | FK → `ticket_orders` | Parent order (cascade delete) |
| `ticket_order_item_id` | FK → `ticket_order_items` nullable | Source line item (null on delete) |
| `token` | `char(40)` unique | 40-char hex token (`bin2hex(random_bytes(20))`) |
| `qr_payload` | text | Contenido del QR (= token) |
| `zone_name` | string | Snapshot del nombre de zona |
| `seat_label` | string nullable | e.g. "Preferencia Norte #2" |
| `status` | `issued` / `used` / `voided` | Estado actual |
| `issued_at` | timestamp | Cuándo fue emitido |
| `used_at` | timestamp nullable | Cuándo fue escaneado en la entrada |
| `voided_at` | timestamp nullable | Cuándo fue anulado manualmente |
| `metadata` | json nullable | Reservado para uso futuro |

### `IssuedTicketStatus` enum
- `Issued` — válido, no escaneado aún
- `Used` — ya fue escaneado (terminal)
- `Voided` — anulado manualmente (terminal)

---

## Services

### `TicketIssuingService`

```php
$service->issueForOrder(TicketOrder $order): Collection<IssuedTicket>
```

- **Guard**: lanza `TicketIssuingException` si `$order->status !== Paid`.
- **Idempotente**: si ya existen tickets para la orden los devuelve sin crear duplicados.
- Crea un `IssuedTicket` por asiento (itera `TicketOrderItem.quantity`).
- Corre dentro de una transacción DB.

### `TicketValidationService`

```php
$service->validate(string $token): array
```

Retorna:
```json
// Éxito
{ "valid": true, "ticket": { "id": 1, "seat_label": "...", "status": "used", ... } }

// Fallo
{ "valid": false, "reason": "not_found|already_used|voided", "error": "..." }
```

- Marca `status = used` y `used_at = now()` en éxito.
- Un ticket ya usado no puede ser re-validado.

---

## Webhook Integration

`PayPalWebhookProcessor::markTicketOrderPaid()` inyecta `TicketIssuingService` vía constructor y llama `issueForOrder($order->refresh())` inmediatamente después del `DB::transaction` que marca la orden como pagada. La emisión es **sincrónica**, en el mismo request del webhook.

---

## API — Respuesta tickets de orden

`GET /api/ticketing/orders/{orderNumber}/tickets`

```json
{
  "order_number": "TICKET-2026-0001",
  "status": "paid",
  "match": {
    "home_team": "Veraguas United",
    "away_team": "CAI",
    "match_date": "2026-06-15T20:00:00.000000Z",
    "stadium": "Estadio Agustín Muquita Sánchez"
  },
  "tickets": [
    {
      "id": 1,
      "token": "a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2",
      "qr_payload": "a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2",
      "zone_name": "General Norte",
      "seat_label": "General Norte #1",
      "status": "issued",
      "issued_at": "2026-05-28T15:00:00.000000Z"
    }
  ]
}
```

---

## Admin — Endpoint de validación

`POST /admin/tickets/validate`  
Requiere: `auth:admin` (sesión) + permiso `issued_tickets.validate`

**Request:**
```json
{ "token": "a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2" }
```

**Response válido (HTTP 200):**
```json
{
  "valid": true,
  "ticket": {
    "id": 1,
    "seat_label": "General Norte #1",
    "zone_name": "General Norte",
    "status": "used",
    "issued_at": "2026-05-28T15:00:00Z",
    "used_at": "2026-05-28T20:05:12Z",
    "order_number": "TICKET-2026-0001",
    "customer": "Luis Tribu",
    "match": "Veraguas United vs CAI",
    "match_date": "2026-06-15T20:00:00Z"
  }
}
```

**Response inválido (HTTP 422):**
```json
{ "valid": false, "reason": "already_used", "error": "Boleto ya fue utilizado." }
```

---

## Business Rules

| Regla | Enforcement |
|---|---|
| Solo órdenes `paid` reciben tickets | `TicketIssuingService` guard + `TicketIssuingException` |
| Sin duplicados | Check idempotente antes de emitir |
| Webhook `captured` dispara emisión | `PayPalWebhookProcessor::markTicketOrderPaid` |
| Token incorrecto falla | `TicketValidationService` → `not_found` |
| Ticket usado no puede reutilizarse | Status check → `already_used` |
| Ticket anulado es rechazado | Status check → `voided` |
| Validación requiere auth admin | `auth:admin` + permiso `issued_tickets.validate` |

---

## Problema: `php artisan migrate:fresh --seed` falla en MySQL local

### Error exacto

```
SQLSTATE[42000]: Syntax error or access violation: 1071
Specified key was too long; max key length is 1000 bytes
SQL: alter table `users` add unique `users_email_unique`(`email`)
Migración: 0001_01_01_000000_create_users_table.php
```

### Causa raíz

El servidor MySQL local tiene configurado `default_storage_engine = MyISAM`.

| Variable | Valor observado |
|---|---|
| MySQL version | 9.1.0 |
| `default_storage_engine` | **MyISAM** |
| `character_set_database` | `utf8mb4` |
| `collation_database` | `utf8mb4_unicode_ci` |

- MyISAM tiene un límite de **1000 bytes** por clave de índice.
- Con `utf8mb4`, cada carácter ocupa hasta 4 bytes.
- `VARCHAR(255)` × 4 bytes = **1020 bytes** → excede el límite de 1000 bytes.
- InnoDB (el engine correcto para Laravel) soporta claves de hasta **3072 bytes** en MySQL 8+/9+.

### Por qué los tests no son afectados

Los tests usan `DB_CONNECTION=sqlite` (configurado en `phpunit.xml`). SQLite no tiene este problema.

### Propuesta de corrección (cambio mínimo en código — no requiere tocar el servidor)

**Opción A — Forzar InnoDB en `config/database.php`** *(recomendada)*

Cambiar en la conexión `mysql`:
```php
// config/database.php, línea 60
'engine' => null,
// →
'engine' => 'InnoDB',
```

Este cambio hace que todas las migraciones de Laravel especifiquen explícitamente `ENGINE=InnoDB` en el DDL generado, ignorando el default del servidor. Una línea, no requiere reiniciar MySQL ni tocar migraciones existentes.

**Opción B — Server-side** *(requiere acceso a my.ini)*

En `C:\ProgramData\MySQL\MySQL Server 9.1\my.ini` o equivalente:
```ini
[mysqld]
default-storage-engine=InnoDB
```
Requiere reiniciar el servicio MySQL. Afecta a todos los proyectos en este servidor.

**Opción C — AppServiceProvider** *(workaround, no recomendada)*

```php
// AppServiceProvider::boot()
Schema::defaultStringLength(191);
```
Limita todos los `string` a 191 chars. Funciona pero es un parche — oculta el problema real.

### Recomendación

Aplicar **Opción A** (`'engine' => 'InnoDB'` en `config/database.php`) antes de correr `migrate:fresh --seed` en MySQL. Es el cambio más pequeño, más correcto semánticamente, y no depende de la configuración del servidor local.
