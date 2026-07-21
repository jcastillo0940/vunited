# Runbook: Rollback de release

## Cuándo usarlo

Un deploy quedó activo pero se detectó un problema *después* de que
`deploy.sh` terminara en éxito (si falla durante el deploy, ya revierte
solo — ver `deploy.md`).

## Pasos

1. Confirmar el problema:
   ```
   infrastructure/scripts/health-check.sh <servicio>
   ```
2. Revertir a la última release que estuvo realmente activa antes de la
   actual (se calcula desde `shared/logs/releases.log`, no por orden de
   directorio):
   ```
   infrastructure/scripts/rollback-release.sh <servicio>
   ```
   O a una release específica:
   ```
   infrastructure/scripts/rollback-release.sh <servicio> <nombre-de-release>
   ```
3. El script activa la release destino, reinicia worker+scheduler y corre
   `verify-release.sh`. Si la verificación falla, **no** intenta un segundo
   rollback automático — termina con error y hay que intervenir a mano
   (revisar `shared/logs/worker-error.log`, `nginx-*-error.log`,
   `php-fpm-error.log`).
4. Registrar en el canal de incidentes qué release quedó activa y por qué
   se revirtió.

## Verificación posterior

```
infrastructure/scripts/health-check.sh <servicio>
systemctl status veraguas-<servicio>-worker.service
```
