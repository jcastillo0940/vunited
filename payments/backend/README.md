# Veraguas United Payments

Servicio Laravel independiente para cobros, reembolsos, webhooks y conciliación. Store y Ticketing se integran exclusivamente mediante `/api/internal/v1`; no existe acceso a sus bases de datos.

## Configuración

Usar `.env.example`, base `veraguas_payments`, Redis con prefijo `veraguas_payments_` y un `PAYMENTS_SERVICE_TOKEN` fuera del repositorio. TiloPay queda en `sandbox` hasta disponer de credenciales y documentación oficial. No se almacenan PAN/CVV.

## Contrato

El contrato OpenAPI está en `docs/openapi.yaml`. Toda llamada interna requiere `X-Service-Token`, `X-Service-Audience`, `X-Service-Scopes`, `X-Correlation-ID` e `Idempotency-Key` para operaciones mutables.

```bash
php artisan migrate:fresh --env=testing
php artisan test
```

Los estados financieros sólo cambian después de validar el webhook; eventos sin secreto/firma son rechazados y persistidos para auditoría.
