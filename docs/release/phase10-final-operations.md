# Fase 10 — activación y estabilización

La activación productiva se mantiene en **dry-run** hasta disponer de VM, DNS, Apache/PHP-FPM, MySQL, Redis, workers, certificados, snapshot final y credenciales rotadas. El script `tools/phase10/preflight.ps1` no cambia tráfico ni datos y emite `GO/NO-GO`.

Procedimiento autorizado cuando el precheck sea GO:

1. Activar mantenimiento y bloquear escrituras.
2. Crear backup y checksum.
3. Desplegar cada dominio con `infrastructure/scripts/deploy.sh` y activar symlink con `activate-release.sh`.
4. Ejecutar `health-check.sh` por dominio y smoke tests.
5. Abrir tráfico; observar 5xx, pagos, stock, capacidad y colas.
6. Ante criterio de rollback, usar `rollback-release.sh` y restaurar datos sólo desde snapshot verificado.
7. Archivar servicios/configuraciones antiguas en solo lectura; no eliminar evidencias.
