# Runbook: Incidente de worker / scheduler

## Diagnóstico rápido

```
systemctl status veraguas-<servicio>-worker.service
systemctl status veraguas-<servicio>-scheduler.timer
journalctl -u veraguas-<servicio>-worker.service -n 50 --no-pager
tail -50 /var/www/veraguas-<servicio>/shared/logs/worker-error.log
```

## Worker en crash-loop (`Start request repeated too quickly`)

El servicio alcanzó `StartLimitBurst` (5 reinicios en 300s) y systemd dejó
de reintentar. Pasos:

1. Leer el error real en `worker-error.log` / `journalctl` — no reiniciar a
   ciegas.
2. Si la causa fue una release rota (falta un archivo, error de sintaxis),
   usar `rollback-release.md` en vez de solo reiniciar el worker.
3. Una vez corregida la causa:
   ```
   sudo systemctl reset-failed veraguas-<servicio>-worker.service
   infrastructure/scripts/restart-workers.sh <servicio>
   ```

## Scheduler no dispara

```
systemctl list-timers veraguas-<servicio>-scheduler.timer
```
Si `NEXT` está vacío o muy en el futuro, el timer no está activo:
```
sudo systemctl enable --now veraguas-<servicio>-scheduler.timer
```

## Confirmar recuperación

```
infrastructure/scripts/health-check.sh <servicio>
```
Debe mostrar `[OK] worker systemd activo` y `[OK] scheduler timer systemd
activo`.
