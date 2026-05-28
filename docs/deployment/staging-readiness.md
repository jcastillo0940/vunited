# Staging Readiness — Veraguas United FC

**Fecha:** 2026-05-28  
**Estado local:** migrate:fresh --seed ✓ · 277 tests ✓ · build ✓  
**Alcance:** Phases 3A–4H completas

---

## Resumen ejecutivo

El sistema está listo para staging con las siguientes condiciones:

| Área | Estado | Notas |
|---|---|---|
| Backend Laravel | ✅ Listo | 277 tests, sin errores |
| Frontend React/Vite | ✅ Listo | Build limpio |
| MySQL con InnoDB | ✅ Listo | `engine = 'InnoDB'` en `config/database.php` |
| PayPal sandbox | ⚠️ Requiere credenciales reales | Ver sección PayPal |
| APP_DEBUG | 🔴 Debe ser `false` | Actualmente `true` en local |
| Webhooks públicos | ⚠️ Requiere URL pública | ngrok o hostname staging |
| Pagos live | 🔴 No implementados | Solo sandbox en esta fase |

---

## Requisitos del servidor

### Runtime
- PHP 8.3+ con extensiones: `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- MySQL 8.0+ (o 9.x) con `default_storage_engine = InnoDB` — o usar `'engine' => 'InnoDB'` en `config/database.php` (ya aplicado)
- Composer 2.x
- Node.js 20+ / npm 10+

### Comandos de despliegue
```bash
# 1. Instalar dependencias
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# 2. Configurar entorno (ver .env abajo)
cp .env.example .env
php artisan key:generate

# 3. Base de datos
php artisan migrate --force
php artisan db:seed --force

# 4. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Variables de entorno críticas para staging

```dotenv
APP_ENV=staging
APP_DEBUG=false                    # ← OBLIGATORIO cambiar de true
APP_URL=https://staging.veraguasunited.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=weveraguas_staging
DB_USERNAME=weveraguas_user
DB_PASSWORD=<contraseña segura>
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

# PayPal sandbox (obtener desde developer.paypal.com)
PAYPAL_CLIENT_ID=<sandbox_client_id>
PAYPAL_CLIENT_SECRET=<sandbox_client_secret>
PAYPAL_WEBHOOK_ID=<webhook_id_de_sandbox>
PAYPAL_MODE=sandbox

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true        # ← HTTPS requerido

# Cache
CACHE_DRIVER=database

# Queue (para jobs futuros)
QUEUE_CONNECTION=sync             # cambiar a redis en producción
```

> **Nota de seguridad:** `APP_DEBUG=false` es obligatorio. Con `true`, Laravel expone stack traces completos con variables de entorno en respuestas de error HTTP, lo que incluiría credenciales en caso de excepción.

---

## PayPal sandbox — configuración

1. Crear cuenta en [developer.paypal.com](https://developer.paypal.com)
2. Crear una App Sandbox → obtener `Client ID` y `Client Secret`
3. En el Admin: `/admin/payment-settings` → guardar credenciales
4. Configurar webhook en PayPal apuntando a `https://<staging-url>/api/webhooks/paypal`
5. Eventos a suscribir:
   - `CHECKOUT.ORDER.APPROVED`
   - `PAYMENT.CAPTURE.COMPLETED`
   - `PAYMENT.CAPTURE.DENIED`
   - `PAYMENT.CAPTURE.REFUNDED`
6. Copiar el **Webhook ID** generado → guardar en `/admin/payment-settings`

### Webhooks en entorno local
Para probar webhooks localmente usar [ngrok](https://ngrok.com):
```bash
ngrok http 8000
# Copiar URL HTTPS generada → configurar como webhook URL en PayPal
```

---

## Seguridad verificada

| Item | Mecanismo | Verificado |
|---|---|---|
| `client_secret` PayPal | Cast `encrypted` en DB + `type="password"` en UI + mascarado `***` en audit logs | ✅ |
| Datos de tarjeta | Nunca se reciben; test explícito en `TicketOrderPaymentTest` | ✅ |
| CVV | Filtrado en `sanitize()` en todos los controllers admin con metadata | ✅ |
| `provider_payload` en admin | Solo se muestra `$safeProviderPayload` (resultado de `sanitize()`) | ✅ |
| Webhook validation | `PayPalWebhookVerificationService` verifica firma; eventos sin verificar tienen `Skipped` status | ✅ |
| Rutas admin | Todas protegidas con `auth:admin` + permiso granular | ✅ |
| Validación token boletos | `POST /admin/tickets/validate` requiere `auth:admin` + `issued_tickets.validate` | ✅ |

---

## Rutas públicas (139 rutas total)

### Frontend público (Inertia/React)
```
/                          Home
/boletos                   Catálogo de boletos
/orden-boletos-confirmada  Confirmación + boletos digitales
/tienda                    Tienda
/carrito                   Carrito
/orden-tienda-confirmada   Confirmación de tienda
/registro-tribu            Formulario de membresía
/registro-confirmado       Confirmación de membresía
/noticias, /noticias/{slug}
/pagina/{slug}
/calendario, /estadio, /plantilla, /jugadores/{slug}
/fuerzas-basicas, /pruebas, /directiva, /fanclub
```

### API pública
```
GET  /api/ticketing/matches/featured
GET  /api/ticketing/matches/{code}
GET  /api/ticketing/matches/{code}/zones
POST /api/ticketing/orders
GET  /api/ticketing/orders/{orderNumber}
GET  /api/ticketing/orders/{orderNumber}/tickets    ← Phase 4F/4G
POST /api/store/orders
GET  /api/store/orders/{orderNumber}
POST /api/membership-orders
GET  /api/membership-orders/{orderNumber}
GET  /api/membership-plans/active
POST /api/webhooks/paypal                          ← PayPal webhook
```

### Admin (requiere `auth:admin`)
```
/admin/payment-settings     Credenciales PayPal
/admin/payments             Monitor de pagos
/admin/payment-events       Eventos de webhook
/admin/membership-orders    Órdenes de membresía
/admin/store-orders         Órdenes de tienda
/admin/ticket-orders        Órdenes de boletos
/admin/issued-tickets       Boletos emitidos
POST /admin/tickets/validate  Validación en entrada (JSON)
```

---

## Funcionalidad implementada vs. pendiente

### ✅ Implementado (Phases 3A–4H)
- Auth admin con roles y permisos granulares
- CRUD: páginas, noticias, menús, configuración, productos, partidos, zonas
- Membresías: planes, órdenes, flujo PayPal sandbox
- Tienda: productos, carrito, órdenes, descuento de stock
- Boletos: partidos, zonas, órdenes, descuento de capacidad
- **Boletos digitales QR**: emisión automática post-pago, validación en entrada
- Webhook PayPal: verificación de firma, procesamiento de eventos, idempotencia
- Audit log en cambios críticos
- Monitor de pagos y eventos admin

### 🔴 No implementado (fases futuras)
- PayPal **live** (solo sandbox activo)
- Email de confirmación al cliente
- Reembolsos desde admin
- Fulfillment de tienda (envíos, tracking)
- Activación física de membresías (cards, beneficios)
- Scanner QR con cámara real para entrada de estadio
- Internacionalización (i18n)
- Multi-tenancy

---

## Checklist antes de staging

- [ ] Cambiar `APP_DEBUG=false` en `.env`
- [ ] Configurar `APP_URL` al dominio staging real
- [ ] Crear base de datos staging con usuario dedicado
- [ ] Configurar credenciales PayPal sandbox reales
- [ ] Registrar webhook en PayPal → copiar Webhook ID → guardarlo en admin
- [ ] Activar PayPal en `/admin/payment-settings` → toggle `is_enabled`
- [ ] Verificar que `SESSION_SECURE_COOKIE=true` (HTTPS)
- [ ] Ejecutar `php artisan config:cache && php artisan route:cache`
- [ ] Crear superadmin con `php artisan db:seed --class=AdminUserSeeder`
- [ ] Hacer un pago de prueba end-to-end con sandbox buyer account
