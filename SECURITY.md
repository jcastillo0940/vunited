# Seguridad

No guardar secretos, dumps, `.env`, PAN, CVV, tokens ni credenciales en Git. Los
backups se almacenan fuera del repositorio, cifrados cuando salgan del equipo, y se
verifican mediante checksum antes de restaurar.

Los reportes de vulnerabilidad deben entregarse al responsable del proyecto por un
canal privado. No abrir incidencias públicas con credenciales o datos personales.

Las operaciones de pagos, webhooks, órdenes, inventario y tickets deben fallar
cerradas ante firma, autorización, idempotencia o configuración faltante.
