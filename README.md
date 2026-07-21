# Veraguas United

Repositorio base para la reconstrucción de la plataforma Veraguas United por
dominios: Web, Store, Ticketing y Payments.

La aplicación Laravel existente en la raíz continúa siendo la referencia operativa
hasta completar cada migración con respaldo, conciliación y rollback probados. No se
deben activar servicios nuevos ni editar una release productiva manualmente.

## Estado

- Fase 1: respaldo, auditoría y plan ejecutable cerrada localmente.
- Fase 2: no iniciada.
- Backup verificable: documentado en `docs/architecture/backup-inventory.md`.

## Desarrollo local

```powershell
composer install
npm install
php artisan test
```

El contrato OpenAPI se valida con `npm run lint:contracts`. Los respaldos se crean
fuera del repositorio mediante `infrastructure/scripts/phase1-backup-audit.ps1`.

## Arquitectura y operación

Consulta `docs/architecture/` para el estado actual, el estado objetivo, límites de
dominio, integraciones, datos y decisiones pendientes. Los procedimientos de
recuperación están en `docs/runbooks/` y `docs/operations/`.
