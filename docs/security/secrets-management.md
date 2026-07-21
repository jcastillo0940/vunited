# Gestión de secretos — Fase 2

## Estado actual

Google Secret Manager **no está disponible** para esta VM: la cuenta de
servicio activa (`973902177550-compute@developer.gserviceaccount.com`) no
tiene el scope `secretmanager.googleapis.com` habilitado (`gcloud secrets
list` devuelve `ACCESS_TOKEN_SCOPE_INSUFFICIENT`). Cambiar el scope de acceso
de una VM de Compute Engine requiere detenerla, así que no se hizo como parte
de esta fase de infraestructura (fuera del criterio de "no downtime").

Siguiendo la instrucción de la Fase 2 para este caso ("en caso contrario,
usar archivos protegidos fuera del repositorio"), los secretos de cada
servicio viven en:

```
/var/www/veraguas-<servicio>/shared/.env
```

- Fuera de `/var/www/veraguasunited` (el repositorio git) — nunca se comitea.
- Permisos `600`, propietario único `veraguas-<servicio>`, sin acceso de
  grupo ni de otros. Verificado: ningún otro usuario de servicio ni
  `www-data` puede leer el `.env` de otro servicio.
- Un archivo por servicio — nunca se comparte un `.env` entre aplicaciones.
- PHP-FPM expone la ruta al archivo vía `env[APP_ENV_FILE]` en el pool
  correspondiente (`/etc/php/8.3/fpm/pool.d/veraguas-<servicio>.conf`); el
  código de aplicación decide cómo cargarlo (dotenv custom path) cuando se
  implemente cada backend.
- Los workers/scheduler de systemd cargan el mismo archivo vía
  `EnvironmentFile=` en su unidad.

Contenido por archivo: `APP_KEY` propio, credenciales de la base de datos
propia, credenciales Redis ACL propias (usuario/prefijo restringidos a ese
servicio) y un `INTERNAL_SERVICE_SECRET` de 32 bytes aleatorios para las
llamadas internas servicio-a-servicio descritas en
`docs/architecture/target-monorepo.md` (rotación pendiente de automatizar).

## Reglas que no se rompen

- Nunca en Git (los `.env` están fuera del árbol del repo; `.gitignore` ya
  excluye cualquier `.env` que pudiera crearse dentro del repo).
- Nunca en el frontend (los builds de React no reciben estas variables; solo
  `VITE_*` explícitas se exponen al bundle).
- Nunca impresos: ningún script de esta fase hace `echo`/`cat` de contraseñas
  o claves; las pruebas de aislamiento redactan los valores (`REDACTED`)
  antes de mostrarse.
- Nunca compartidos entre aplicaciones: un archivo por servicio, un usuario
  Linux por archivo.

## Migración futura a Secret Manager

Cuando se habilite el scope en la cuenta de servicio (o se migre a Workload
Identity / una cuenta de servicio dedicada por servicio):

1. Crear un secreto en Secret Manager por variable sensible, con el patrón
   `veraguas-<servicio>-<clave>` (p. ej. `veraguas-store-db-password`).
2. Sustituir la carga de `shared/.env` por una obtención en el arranque del
   proceso (`gcloud secrets versions access` o el cliente PHP de Secret
   Manager) hacia una variable de entorno en memoria, sin tocar disco.
3. Retirar `shared/.env` y el `env[APP_ENV_FILE]` de cada pool una vez
   validado en un servicio piloto (recomendado: Store, ya tiene Fase 1 del
   plan de extracción).
4. Mantener rotación de `INTERNAL_SERVICE_SECRET` en Secret Manager con
   versión activa + anterior durante el corte, para no romper llamadas en
   vuelo.

Este documento debe actualizarse el día que se ejecute la migración.
