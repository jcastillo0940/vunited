# Runbook: Incidente de servidor (CPU / RAM / disco)

## Diagnóstico rápido

```
uptime
free -h
df -h /
ss -tlnp
sudo systemctl --failed
```

## Disco lleno o casi lleno

**Ya detectado como riesgo real durante la Fase 2**: `/` estaba al 90% de
uso (1011M libres) antes de esta fase. Revisar primero:

```
sudo du -sh /var/www/veraguas-*/backups /var/www/veraguas-*/releases \
    /var/log/nginx /var/log/redis 2>/dev/null | sort -h
```

Candidatos típicos a liberar: releases viejas (`find
/var/www/veraguas-*/releases -maxdepth 1 -mtime +30`, revisar antes de
borrar — nunca borrar la release apuntada por `current`), backups fuera de
retención que no se limpiaron, logs sin rotar.

## CPU/RAM sostenida alta

1. `top` / `htop` para identificar el proceso.
2. Si es un worker de un servicio específico:
   `systemctl status veraguas-<servicio>-worker.service`.
3. Si es PHP-FPM de un pool específico: revisar
   `pm.max_children` en `/etc/php/8.3/fpm/pool.d/veraguas-<servicio>.conf` —
   estos pools están limitados (`pm=ondemand`, `max_children=6`) para no
   poder saturar el host completo.
4. Producción (`united.wp-pa.com`, pool `www`) es prioridad: confirmar que
   sigue respondiendo (`curl -o /dev/null -w '%{http_code}'
   https://united.wp-pa.com/`) antes de investigar los servicios nuevos.

## Escalar

Si el servidor completo está degradado y afecta a producción, priorizar
restaurar `united.wp-pa.com` (parar/limitar servicios nuevos si compiten por
recursos) antes de diagnosticar la causa raíz.
