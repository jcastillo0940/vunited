# Contratos API compartidos

Aquí se versionarán especificaciones OpenAPI y esquemas de eventos. Compartir un
contrato no autoriza a un backend a leer la base de datos de otro.

Convenciones iniciales:

- APIs públicas bajo `/api/v1`.
- APIs entre backends bajo `/internal/v1`.
- `Idempotency-Key` obligatorio al crear órdenes o confirmar pagos.
- `X-Correlation-ID` propagado entre servicios y logs.
- Fechas en ISO 8601 UTC y dinero en unidades mínimas enteras con código ISO de moneda.
- Cambios incompatibles requieren una nueva versión.

Contratos disponibles:

- `store-v1.openapi.yaml`: catálogo Store de solo lectura durante la extracción gradual.

Las operaciones de órdenes y pagos no se incorporarán hasta tener idempotencia
persistente, autenticación entre servicios y conciliación probadas.
