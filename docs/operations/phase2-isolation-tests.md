# Fase 2 — Evidencia de pruebas de aislamiento

Generado durante la ejecución de Fase 2 (infraestructura, bases y aislamiento real).
Este documento registra la salida real de las pruebas obligatorias, no una simulación.

## 1. Aislamiento de usuarios Linux y directorios

```
$ sudo -u veraguas-store bash -c 'echo pwned > /var/www/veraguas-web/shared/storage/logs/intrusion.txt'
bash: line 1: /var/www/veraguas-web/shared/storage/logs/intrusion.txt: Permission denied
[OK esperado] store no puede escribir en web

$ sudo -u veraguas-web bash -c 'ls /var/www/veraguas-payments/backups'
ls: cannot access '/var/www/veraguas-payments/backups': Permission denied
[OK esperado] web no puede listar backups de payments

$ sudo -u veraguas-ticketing bash -c 'touch /var/www/veraguas-store/tmp/probe'
touch: cannot touch '/var/www/veraguas-store/tmp/probe': Permission denied
[OK esperado] ticketing no puede escribir en tmp de store

$ sudo -u veraguas-payments bash -c 'ls /var/www/veraguas-ticketing/releases'
ls: cannot access '/var/www/veraguas-ticketing/releases': Permission denied
[OK esperado] payments no puede listar releases de ticketing
```

`www-data` (nginx) solo tiene ACL `--x` (tránsito, sin lectura de listado) sobre la
raíz de cada servicio y `releases/`, más lectura/tránsito explícita sobre
`sockets/` y `builds/` — no puede leer `shared/`, `tmp/` ni `backups/` de ningún
servicio.

## 2. Pools PHP-FPM y sockets

Los 4 pools (`veraguas-web`, `veraguas-store`, `veraguas-ticketing`,
`veraguas-payments`) están activos, cada uno con su propio socket en
`/var/www/veraguas-<servicio>/sockets/php-fpm.sock` (modo `0660`,
owner `veraguas-<servicio>:www-data`). `php-fpm8.3 -t` pasó sin errores y el pool
`www` de producción (`/run/php/php8.3-fpm.sock`) no fue modificado.

Ping FastCGI (`ping.path=/fpm-ping`) verificado como `www-data` sobre los 4
sockets: respuesta `pong` en los 4 casos.

Aislamiento de socket:

```
$ sudo -u veraguas-store cgi-fcgi -bind -connect /var/www/veraguas-web/sockets/php-fpm.sock
Could not connect to /var/www/veraguas-web/sockets/php-fpm.sock
[OK esperado] store no puede usar el socket de web
```

## 3. Bases de datos — pruebas positivas

```
[OK] veraguas_web entra a su propia base
[OK] veraguas_store entra a su propia base
[OK] veraguas_ticketing entra a su propia base
[OK] veraguas_payments entra a su propia base
```

## 4. Bases de datos — 12 pruebas negativas cruzadas

```
[OK] web NO entra a store (access denied)
[OK] web NO entra a ticketing (access denied)
[OK] web NO entra a payments (access denied)
[OK] store NO entra a web (access denied)
[OK] store NO entra a ticketing (access denied)
[OK] store NO entra a payments (access denied)
[OK] ticketing NO entra a web (access denied)
[OK] ticketing NO entra a store (access denied)
[OK] ticketing NO entra a payments (access denied)
[OK] payments NO entra a web (access denied)
[OK] payments NO entra a store (access denied)
[OK] payments NO entra a ticketing (access denied)

Total fallos criticos: 0
```

`SHOW GRANTS` confirma que cada usuario MySQL solo tiene `USAGE` global +
`ALL PRIVILEGES` sobre su propia base. MariaDB permanece en `bind-address =
127.0.0.1` (sin exposición pública), verificado con `ss -tlnp`.

## 5. Redis — ACL y aislamiento por servicio

Redis 7.0.15 instalado, `bind 127.0.0.1 -::1` (confirmado con `ss -tlnp`, sin
puerto público), usuario `default` deshabilitado (`user default off
resetchannels -@all`), un usuario `admin` con contraseña para operación, y un
usuario ACL por servicio restringido a su propio espacio de claves y canales:

```
user veraguas_web       ~veraguas:web:*       &veraguas:web:*       +@all -@admin -@dangerous
user veraguas_store     ~veraguas:store:*     &veraguas:store:*     +@all -@admin -@dangerous
user veraguas_ticketing ~veraguas:ticketing:* &veraguas:ticketing:* +@all -@admin -@dangerous
user veraguas_payments  ~veraguas:payments:*  &veraguas:payments:*  +@all -@admin -@dangerous
```

Pruebas positivas: los 4 usuarios pudieron `SET`/`GET` bajo su propio prefijo
`veraguas:<servicio>:*`.

Pruebas negativas cruzadas (12 combinaciones): las 12 fallaron con `NOPERM` al
intentar leer el prefijo de otro servicio. 0 fallos críticos.

Comandos administrativos bloqueados para usuarios de servicio:

```
FLUSHALL como veraguas_web: NOPERM this user has no permissions to run the 'flushall' command
CONFIG GET como veraguas_web: NOPERM this user has no permissions to run the 'config|get' command
```

Persistencia: AOF (`appendonly yes`, `appendfsync everysec`) + snapshots RDB
(`save 900 1 / 300 10 / 60 10000`). Memoria: `maxmemory 256mb`,
`maxmemory-policy volatile-lru` (las claves con TTL se desalojan primero; las
colas sin TTL no se pierden bajo presión de memoria). Logs en
`/var/log/redis/redis-server.log`.

## 6. Gateway nginx por servicio (adaptado de Apache)

Producción sigue en `nginx` (no Apache); decisión tomada explícitamente con el
usuario dado el conflicto real entre `ADR-006-apache-fpm-react.md` (que exige
Apache) y el estado real del servidor (nginx + Certbot sirviendo
`united.wp-pa.com` en 80/443). Se documenta la desviación aquí; el ADR no fue
modificado en esta fase.

Se crearon 6 server blocks nuevos, todos en `/etc/nginx/sites-available/` +
`sites-enabled/`, **escuchando únicamente en `127.0.0.1:8081-8086`** (no en
`0.0.0.0`), por lo que no hay ningún corte de tráfico público ni riesgo para
`united.wp-pa.com`:

| Vhost | Puerto (loopback) | server_name | Backend |
| --- | --- | --- | --- |
| Website (nuevo, aislado) | 8081 | web.veraguas.internal | pool veraguas-web |
| Store | 8082 | tienda.veraguas.internal | pool veraguas-store |
| Ticketing | 8083 | boletos.veraguas.internal | pool veraguas-ticketing |
| Administración | 8084 | admin.veraguas.internal | pool veraguas-web (restringido a loopback) |
| APIs (`/api/v1/<dominio>/...`) | 8085 | api.veraguas.internal | enruta por prefijo a cada pool; `/internal/` bloqueado |
| Webhook Payments | 8086 | webhooks-payments.veraguas.internal | pool veraguas-payments (restringido a loopback) |

TLS: certificado autofirmado interno (`/etc/nginx/ssl/veraguas-internal/`,
SAN cubre los 6 hostnames) — **temporal**, hasta que se asignen dominios
públicos reales y Certbot pueda emitir certificados válidos. Por eso HSTS
queda deliberadamente comentado en `snippets/veraguas-security-headers.conf`
(“HSTS cuando TLS esté correcto”, tal como pide el checklist).

Aplicado en todos los vhosts vía snippets compartidos: `gzip`, cache headers
(`Cache-Control: public, max-age=31536000, immutable` en `/builds/` para los
assets con hash de Vite), `X-Content-Type-Options: nosniff`,
`Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`,
`Content-Security-Policy` inicial, y `X-Correlation-ID` (usa `$request_id`
nativo de nginx, propagado al backend vía `fastcgi_param
HTTP_X_CORRELATION_ID`).

Bug real encontrado y corregido durante las pruebas: `try_files
/healthz.php` intentaba que nginx (`www-data`) abriera el `.php` directamente
para verificar su existencia, y como el archivo pertenece al usuario del
servicio (no a `www-data`), fallaba con `13: Permission denied` — la
aislación de permisos funcionando correctamente, pero rompía el health check.
Se corrigió pasando `fastcgi_pass` directo con `SCRIPT_FILENAME` explícito
(sin verificación de existencia por parte de nginx). El vhost de API tenía
además `location` anidados dentro de otro `location`, que nginx no vuelve a
matchear tras el internal redirect de `try_files`; se reescribió con
`location` planos por prefijo.

Pruebas:

```
web      /healthz -> {"service":"web","status":"ok"}        HTTP 200
store    /healthz -> {"service":"store","status":"ok"}      HTTP 200
ticketing/healthz -> {"service":"ticketing","status":"ok"}  HTTP 200
payments /healthz -> {"service":"payments","status":"ok"}   HTTP 200
api/v1/web/healthz, api/v1/store/healthz, api/v1/ticketing/healthz -> 503 (maintenance, index.php placeholder)
api /internal/v1/payments/intents -> 404 (bloqueado por diseño)
admin.veraguas.internal:8084 -> 503 (accesible solo desde loopback)
producción https://united.wp-pa.com/ -> 200 (sin cambios) antes y después de cada reload
```

`ss -tlnp` confirma que 8081-8086 solo escuchan en `127.0.0.1`, nunca en
`0.0.0.0` ni `::`.

## 7. systemd — workers y scheduler

8 unidades creadas: `veraguas-<servicio>-worker.service` (Type=simple,
Restart=on-failure, RestartSec=5, StartLimitBurst=5/300s, MemoryMax=256M,
TasksMax=64, NoNewPrivileges, ProtectSystem=strict, corre como
`veraguas-<servicio>`) y `veraguas-<servicio>-scheduler.service` (Type=oneshot)
+ `.timer` (`OnCalendar=*-*-* *:*:00`, cada minuto) por cada uno de los 4
servicios.

Como aún no existe backend real para ningún dominio nuevo (solo el bootstrap
de staging de la sección 6), cada `ExecStart` apunta a un stub
(`bin/worker-stub.php` / `bin/scheduler-stub.php`) dentro del release
bootstrap, marcado con `TODO` explícito para cambiar a `php artisan
queue:work` / `php artisan schedule:run` cuando cada backend se implemente.
Esto permitió probar el ciclo de vida real de las unidades en lugar de solo
verificar sintaxis.

Pruebas:

```
systemctl daemon-reload            -> sin errores
4x systemctl enable --now *-worker.service     -> active (running)
4x systemctl enable --now *-scheduler.timer    -> active (waiting)
systemctl restart veraguas-store-worker.service -> active en <1s
kill -9 al PID de veraguas-payments-worker      -> systemd lo reinicia solo
  (NRestarts=1, active (running) 3s despues, respetando RestartSec=5)
systemctl start veraguas-web-scheduler.service (disparo manual)
  -> code=exited status=0/SUCCESS, log con tick registrado
systemctl list-timers veraguas-web-scheduler.timer -> proximo disparo en <60s
```

Logs van a `shared/logs/worker.log`, `worker-error.log`, `scheduler.log`,
`scheduler-error.log` de cada servicio (y a journald vía
`SyslogIdentifier=veraguas-<servicio>-worker`).

## 10. Scripts de release (`infrastructure/scripts/`)

Se crearon `lib/common.sh` (validación de servicio, rutas, logging de
eventos de release) y los 8 scripts pedidos: `build.sh`, `test.sh`,
`deploy.sh`, `activate-release.sh`, `health-check.sh`, `restart-workers.sh`,
`rollback-release.sh`, `verify-release.sh`. Todos con `set -euo pipefail`,
validan sus parámetros, no imprimen secretos (leen `.env` solo para pasar
credenciales a `mysql`/`redis-cli`, nunca las hacen `echo`), fallan con
mensajes claros por `stderr`, y registran cada evento en
`shared/logs/releases.log`.

**Dos fallos reales encontrados y corregidos probando el pipeline
end-to-end contra el servicio `store`** (no simulados — se dejaron
reproducir a propósito):

1. `deploy.sh` original: si `restart-workers.sh` fallaba después de activar
   una release, `set -e` cortaba el script antes de llegar a la lógica de
   rollback automático, dejando `current` apuntando a una release rota con
   el worker en `failed` (excedió `StartLimitBurst`). Corregido encadenando
   `activate && restart-workers && verify` bajo `set +e` y decidiendo
   éxito/rollback por código de salida explícito.
2. `rollback-release.sh` original: sin argumento, elegía la release
   "anterior" por orden alfabético de directorio en `releases/` — lo que
   podía seleccionar un intento de deploy fallido que nunca llegó a estar
   realmente activo. Corregido para derivar el objetivo del historial real
   de activaciones en `releases.log` (colapsando repeticiones) en vez del
   listado crudo de directorios.

Pruebas reales ejecutadas contra `store` (evidencia, no simulación):

```
deploy.sh store <release-sin-bin/worker-stub.php>
  -> activar OK, restart-workers FALLA (worker no arranca)
  -> auto-rollback a la release anterior, worker vuelve a "active"
  -> exit code 1 (fallo reportado correctamente)

deploy.sh store <release-completa-con-stubs>
  -> build, test (sin suite, no-op), activar, restart-workers, verify-release
  -> health-check: 7/7 OK (release, socket, vhost, worker, scheduler, DB, Redis)
  -> exit code 0, contenido nuevo servido realmente (HTTP 503 con el mensaje
     de la release de prueba)

rollback-release.sh store (sin argumento)
  -> selecciona la ultima release realmente activada antes de la actual
     (usando releases.log), no la ultima creada
  -> activar + restart-workers + verify -> 7/7 OK, exit code 0
```

Los otros 3 servicios (web, ticketing, payments) no se tocaron durante estas
pruebas y siguieron `active` todo el tiempo; producción
(`https://united.wp-pa.com/`) se verificó en HTTP 200 antes, durante y
después de cada prueba.

## 11. Backups automatizados por base de datos

`infrastructure/scripts/backup-database.sh` y `restore-database.sh`: dump
con `mysqldump --single-transaction`, cifrado `openssl enc -aes-256-cbc
-pbkdf2` con una llave de 48 bytes por servicio
(`shared/.backup-key`, `600`, nunca en git), checksum `sha256sum` junto al
archivo cifrado, retención configurable (`find -mtime +N -delete`, default
14 días), log estructurado en `shared/logs/backup.log`, y copia externa
best-effort a un bucket GCS (`gsutil cp`, variable `VERAGUAS_BACKUP_BUCKET`)
que queda en `external=skipped` mientras la cuenta de servicio no tenga
`storage.buckets.list`/`storage.objects.create` — mismo bloqueo de IAM que
Secret Manager y Monitoring (`gsutil ls` confirmó `403
AccessDeniedException`).

4 timers systemd (`veraguas-backup@<servicio>.timer`, plantilla
`veraguas-backup@.service`) corren el backup diario a las 03:10/20/30/40 UTC
(escalonado), como el usuario propio de cada servicio.

**Prueba real de backup + restore (no simulada)**, contra `veraguas_store`:

```
1. Se crea una tabla de prueba con un valor conocido ("antes-del-backup").
2. backup-database.sh store -> dump + cifrado + checksum OK
   (store-20260721032851.sql.enc, 2128 bytes)
3. Se sobreescribe el valor en la base ("DATO PERDIDO"), simulando perdida.
4. restore-database.sh store <archivo> --yes
   -> verifica checksum, descifra, restaura sobre veraguas_store
5. SELECT confirma que el valor original volvio: "antes-del-backup"
```

Prueba del disparo automático vía systemd: se ejecutó manualmente
`veraguas-backup@ticketing.service` (equivalente a que lo dispare su
`.timer`) y generó un backup cifrado + checksum reales en
`/var/www/veraguas-ticketing/backups/`.

## 13. Pruebas finales obligatorias (barrido de cierre)

```
health-check.sh web/store/ticketing/payments -> 4/4 PASS (7/7 checks cada uno)
nginx -t                    -> syntax ok / test successful
php-fpm8.3 -t                -> test successful
systemctl daemon-reload      -> sin errores
ss -tlnp (puertos nuevos: 8081-8086, 6379) -> TODOS en 127.0.0.1 / [::1] unicamente,
                                               ninguno en 0.0.0.0 ni [::]
mysqld (3306)                -> sigue en 127.0.0.1 unicamente (bind-address confirmado)
https://united.wp-pa.com/    -> HTTP 200 (verificado antes, durante y despues de
                                 cada cambio de esta fase; nunca se interrumpio)
```

## Criterios de salida — estado

- [x] 4 usuarios Linux (`veraguas-web/store/ticketing/payments`), aislamiento probado.
- [x] 4 pools PHP-FPM, sockets aislados, ping/health verificado.
- [x] 4 bases de datos, 4 usuarios MySQL, grants solo sobre su propia base.
- [x] Aislamiento probado: 12/12 pruebas cruzadas de BD fallan; Redis 12/12
      cruces fallan con `NOPERM`; sistema de archivos con ACL `--x` para
      `www-data` y sin acceso entre usuarios de servicio.
- [x] Redis aislado (ACL por servicio, loopback, sin admin cruzado).
- [x] "Apache" preparado — adaptado a nginx por decisión explícita del
      usuario (ver sección 6), loopback-only, sin cortar producción.
- [x] systemd preparado (8 unidades worker/scheduler + 4 timers de backup),
      restart/backoff probado con un `kill -9` real.
- [x] Secretos separados (`shared/.env` 600, un archivo por servicio, fuera
      del repo; gap de Secret Manager documentado con plan de migración).
- [x] Logs y alertas: logs JSON con correlation id, pipelines de Ops Agent
      configurados, políticas de alerta preparadas (bloqueadas por IAM,
      documentado).
- [x] Deploy reproducible: probado con release rota (auto-rollback) y
      release completa (éxito), 2 bugs reales encontrados y corregidos.
- [x] Rollback probado: `rollback-release.sh` corregido y verificado contra
      el historial real de activaciones.
- [x] Backup probado: dump + cifrado + checksum + restore real contra
      `veraguas_store`, con pérdida de datos simulada y recuperada.
- [ ] Existe commit — pendiente, se crea al cerrar esta fase (ver mensaje
      de cierre de la conversación).

## Gaps conocidos (no bloquean el criterio de salida, quedan documentados)

1. **IAM de la VM incompleto**: sin `secretmanager.googleapis.com` scope,
   sin `roles/logging.logWriter`, sin `roles/monitoring.metricWriter`, sin
   `storage.buckets.list`. Bloquea Secret Manager real, envío de
   logs/métricas a Cloud Monitoring, dashboards, alertas activas y copia
   externa de backups. Requiere cambiar el scope de acceso de la VM (implica
   detenerla) o migrar a Workload Identity — decisión y ventana que le
   corresponden al equipo, no se tomó en esta fase.
2. **Disco al 90%** desde antes de esta fase — ver `server-incident.md`.
3. **TLS de los 6 vhosts nuevos es autofirmado** (staging interno); falta
   asignar dominios públicos reales para Certbot.
4. **Backends reales de Store/Ticketing/Payments/Web-aislado no existen
   todavía** — todo lo de esta fase es infraestructura sobre releases
   "bootstrap" de mantenimiento (503 con JSON). Los `ExecStart` de systemd y
   los `ExecStart` de FPM apuntan a stubs marcados con `TODO`, listos para
   sustituirse cuando cada dominio implemente su backend (Fases 1-4 de
   `docs/architecture/target-monorepo.md`).
