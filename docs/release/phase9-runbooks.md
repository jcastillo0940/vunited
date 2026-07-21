# Fase 9 — runbooks de certificación

- **Deploy:** validar checksum, ejecutar migraciones por servicio, health checks y smoke tests.
- **Rollback:** detener workers, revertir release inmutable, ejecutar migración down sólo con aprobación y restaurar snapshot.
- **Backup/restore:** verificar backup, checksum, restaurar en base aislada y comparar conteos antes de promover.
- **Payments/TiloPay:** mantener sandbox si faltan credenciales; revisar intents pendientes, webhooks e idempotency keys.
- **Stock/sobreventa:** detener checkout, revisar reservas y movimientos; no ajustar cantidades sin auditoría.
- **Ticketing/scanner:** detener emisión/escaneo, revisar capacidad y eventos de validación; reanudar sólo tras conciliación.
- **Seguridad:** revocar secretos expuestos, rotar tokens, revisar logs y preservar correlation IDs.
- **Redis/workers:** reiniciar supervisor, comprobar cola y backoff; no borrar colas sin snapshot.
