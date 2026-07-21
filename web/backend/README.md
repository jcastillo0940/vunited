# Veraguas United Web Backend

Aplicación Laravel independiente para el dominio institucional Web. No comparte conexión ni modelos con Store, Ticketing o Payments.

## Arranque

1. Copiar `.env.example` a `.env` y definir `DB_DATABASE=veraguas_web`, `DB_USERNAME=veraguas_web`, Redis y el secreto del servicio.
2. Ejecutar `composer install`, `php artisan key:generate` y `php artisan migrate --force`.
3. Servir en el puerto Web configurado (por defecto `8010`).

La API pública y administrativa vive bajo `/api/v1/web`. El panel React consume exclusivamente esa API mediante Bearer tokens Sanctum con la audiencia `web`. Los tokens son cortos, revocables y limitados por permisos.

Incluye identidad interna (login, recuperación, cambio de contraseña, bloqueo, sesiones, 2FA TOTP), RBAC, auditoría, CMS, medios con validación de MIME/tamaño, formularios con consentimiento, honeypot y rate limit, cache pública, correlation ID y logs JSON.

## Verificación

```bash
php artisan migrate:fresh --env=testing
php artisan test
php artisan route:list --path=api/v1/web
```

El contenido legacy no se elimina: los artefactos no pertenecientes a Web quedaron en el respaldo externo `web-backend-quarantine-phase4` para la siguiente etapa de migración controlada.
