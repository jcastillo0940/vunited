# Runbook: Incidente de base de datos

## Diagnóstico rápido

```
sudo systemctl status mariadb
sudo mysql -e "SHOW PROCESSLIST;"
sudo mysql -e "SHOW STATUS LIKE 'Threads_connected';"
sudo ss -tlnp | grep mariadb   # debe seguir en 127.0.0.1:3306 unicamente
```

## Un servicio no puede conectar a su base

1. Confirmar credenciales y conectividad exactamente como lo hace la app:
   ```
   infrastructure/scripts/health-check.sh <servicio>
   ```
2. Confirmar que el usuario MySQL sigue existiendo y con los grants
   correctos (solo su propia base):
   ```
   sudo mysql -e "SHOW GRANTS FOR 'veraguas_<servicio>'@'127.0.0.1';"
   ```
3. Si los grants se perdieron o cambiaron, ver
   `docs/operations/phase2-isolation-tests.md` sección 3 para el patrón
   correcto de creación de usuario/grant.

## Sospecha de acceso cruzado entre servicios

Nunca debe pasar. Verificar de inmediato:
```
mysql -h 127.0.0.1 -u veraguas_<A> -p'<pass>' veraguas_<B> -e "SELECT 1"
```
Debe devolver `Access denied`. Si no lo hace, es un incidente de seguridad:
revocar el grant cruzado inmediatamente (`REVOKE ALL PRIVILEGES ON
veraguas_<B>.* FROM 'veraguas_<A>'@'127.0.0.1';`) y documentar cómo se
otorgó.

## Corrupción o pérdida de datos

Ir a `restore.md`. No intentar reparar tablas a mano sin antes tener un
backup reciente confirmado.

## MariaDB caído

```
sudo systemctl status mariadb
sudo journalctl -u mariadb -n 50 --no-pager
sudo systemctl restart mariadb
```
Afecta a **todos** los servicios (producción incluida, `weveraguas`) porque
comparten la misma instancia de MariaDB con bases separadas. Priorizar el
restart y confirmar `united.wp-pa.com` antes de diagnosticar la causa.
