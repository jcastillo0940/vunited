# Runbook: Deploy

Aplica a los servicios aislados de Fase 2 (`web`, `store`, `ticketing`,
`payments`) bajo `/var/www/veraguas-<servicio>/`.

## Pasos

1. Preparar el código fuente ya construido/checkeado en un directorio local
   (por ejemplo, un `git worktree` o checkout temporal).
2. Ejecutar como root:
   ```
   infrastructure/scripts/deploy.sh <servicio> <directorio-fuente>
   ```
3. El script hace, en orden: copia a una release nueva con timestamp,
   `chown` al usuario del servicio, enlaza `storage/` y `.env` desde
   `shared/`, corre `build.sh` y `test.sh` como ese usuario, activa la
   release (`activate-release.sh`), reinicia worker+scheduler
   (`restart-workers.sh`) y verifica salud (`verify-release.sh`).
4. Si cualquier paso desde "activar" en adelante falla, el propio script
   revierte automáticamente a la release anterior y termina con código 1.
   No hay que hacer rollback manual en ese caso — sí hay que investigar por
   qué falló antes de reintentar.
5. Confirmar manualmente:
   ```
   infrastructure/scripts/health-check.sh <servicio>
   ```

## Notas

- Nunca editar archivos directamente dentro de `releases/<timestamp>/`; el
  siguiente deploy los sobreescribe.
- `shared/.env` y `shared/storage/` sobreviven entre releases; todo lo demás
  es reemplazable.
- El deploy de producción real (`united.wp-pa.com`) sigue fuera de este
  flujo hasta la Fase 4 del plan de extracción
  (`docs/architecture/target-monorepo.md`).
