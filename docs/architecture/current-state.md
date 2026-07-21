# Estado actual auditado

Auditoría ejecutada el 2026-07-20/21 en Windows 11 + WAMP local. La aplicación
operativa es un Laravel 13.12 en la raíz con React/Vite, Composer y npm. La base
principal configurada es `weveraguas` en MySQL local; el respaldo de Fase 1 encontró
20 bases no sistémicas y 3 bases del servidor excluidas del inventario lógico.

Baseline local: Intel i5-9300H (4 cores/8 logical), carga aproximada 36%, 11.8 GiB
RAM visible con 0.83 GiB libres al inspeccionar, pagefile de 20,402 MiB y discos C:
con 61.8 GB libres / G: con 5.5 GB libres. Es una referencia de desarrollo, no un
baseline productivo.

La aplicación contiene Web/CMS/administración, Store, Ticketing, Memberships y
Payments dentro del mismo runtime y base. Las rutas se organizan en `routes/web.php`,
`routes/api.php`, `routes/auth.php`, `routes/admin.php` y `routes/console.php`; la
entrada de webhook existente es `/api/webhooks/paypal`. La cola configurada por
defecto es `database` y existen dos tareas semanales de importación de plantilla.

El entorno local usa Apache WAMP y PHP CLI/Apache; PHP-FPM, systemd, Supervisor,
Redis CLI, firewall Linux, Google Cloud Ops Agent, service accounts, buckets y
snapshots GCP no están disponibles en este equipo. No se afirma estado productivo
para esos componentes.

El VirtualHost local observado es únicamente `localhost` en puerto 80, con acceso
restringido a `Require local`; no se observaron dominios ni certificados productivos.
`storage/logs` no tenía logs de aplicación activos durante la inspección.

La integración de pagos implementada hoy es PayPal legado. TiloPay queda como
integración objetivo y pendiente de diseño/credenciales; no se activó ningún módulo
nuevo en esta fase.
