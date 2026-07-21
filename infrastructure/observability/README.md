# Observabilidad

## Estado (Fase 2)

- **Logs JSON**: los 6 vhosts nuevos de nginx (`infrastructure` §6) usan
  `log_format veraguas_json` (definido en `/etc/nginx/nginx.conf`), con
  `correlation_id` = `$request_id` de nginx, propagado al backend PHP-FPM vía
  `fastcgi_param HTTP_X_CORRELATION_ID`. El vhost de producción
  (`united.wp-pa.com`) no fue tocado.
- **Google Cloud Ops Agent**: ya estaba instalado y activo en el host antes
  de esta fase. Se añadieron pipelines de logging en
  `/etc/google-cloud-ops-agent/config.yaml` para leer
  `shared/logs/{nginx-*,php-fpm-error,php-fpm-slow,worker*,scheduler*}.log`
  de los 4 servicios nuevos, con `record_log_file_path: true` (el campo
  `agent.googleapis.com/log_file_path` resultante contiene
  `veraguas-<servicio>`, que sirve como etiqueta por servicio sin necesitar
  un procesador Lua no soportado oficialmente por Ops Agent). Validado con
  `google_cloud_ops_agent_engine -in config.yaml`: sintaxis correcta,
  `fluent-bit` y `opentelemetry-collector` activos sin errores de parseo.
- **Bloqueo real de permisos (documentado, no resuelto en esta fase)**: la
  cuenta de servicio de la VM
  (`973902177550-compute@developer.gserviceaccount.com`) no tiene
  `roles/logging.logWriter` ni `roles/monitoring.metricWriter`, y tampoco
  scope para leer `monitoring.googleapis.com` vía `gcloud` (mismo problema
  que Secret Manager, ver `docs/security/secrets-management.md`). Esto
  significa que el pipeline local de recolección funciona, pero el envío
  real a Cloud Logging/Monitoring está bloqueado hasta que se otorguen esos
  roles o se cambie el scope de acceso de la VM (requiere reinicio de la
  VM). Las políticas de alerta de `alert-policies/` están listas para
  aplicarse con `gcloud alpha monitoring policies create
  --policy-from-file=<archivo>` en cuanto el acceso se habilite.
- **X-Correlation-ID**: presente en todas las respuestas de los 6 vhosts
  nuevos (cabecera de respuesta + variable de entorno FastCGI hacia PHP-FPM).

## Alertas iniciales preparadas (`alert-policies/`)

| Archivo | Cubre |
| --- | --- |
| `cpu.yaml` | CPU > 80% sostenido 10 min |
| `ram.yaml` | Memoria > 85% sostenido 10 min |
| `disk.yaml` | Disco > 85% en `/` — **ya en 90% al momento de esta fase, ver nota abajo** |
| `service-down.yaml` | nginx/php-fpm/mysql/redis reportados `FAIL` por `health-check.sh` |
| `workers.yaml` | Un worker systemd entra en crash-loop (reinicios repetidos) |

> **Nota operativa real detectada en esta fase**: `df -h /` mostró 90% de uso
> (1011M libres) antes de empezar. La política `disk.yaml`, si estuviera
> activa, ya estaría en alerta. Revisar espacio en disco pronto,
> independientemente de esta fase de infraestructura.

## Pendiente para una fase posterior

- Habilitar `roles/logging.logWriter` + `roles/monitoring.metricWriter` (o
  el scope equivalente) en la cuenta de servicio de la VM.
- Aplicar las políticas de `alert-policies/` una vez habilitado el acceso.
- Dashboards en Cloud Monitoring (no se pudo crear ninguno: mismo bloqueo de
  permisos).
- Métricas de negocio por dominio (pedidos, entradas emitidas, pagos) cuando
  cada backend real exista.
