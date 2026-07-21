# Estado objetivo

La plataforma se separará en cuatro backends con bases propietarias:

| Dominio | Backend | Base |
| --- | --- | --- |
| Web | `web/backend` | `veraguas_web` |
| Store | `store/backend` | `veraguas_store` |
| Ticketing | `ticketing/backend` | `veraguas_ticketing` |
| Payments | `payments/backend` | `veraguas_payments` |

Cada frontend consumirá únicamente APIs públicas versionadas mediante Apache. Las
llamadas internas usarán `/internal/v1`, autenticación de servicio, timeouts,
reintentos, outbox/inbox, `Idempotency-Key` y `X-Correlation-ID`. No habrá JOIN,
foreign keys, modelos o migraciones entre bases.

El sistema objetivo usará TiloPay como proveedor de Payments, pero ninguna
credencial ni webhook se configurará hasta contar con acceso oficial y contrato
verificable.
