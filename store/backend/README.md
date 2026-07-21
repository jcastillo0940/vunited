# Store Backend

Backend objetivo para categorías, productos, inventario, órdenes y preparación de
pedidos.

Base de datos propia: `veraguas_store`.

Solicitará pagos a Payments Backend por API interna y recibirá confirmaciones
idempotentes. No consultará tablas de otros backends.

## Estado de implementación

Fase 1 iniciada dentro del monolito mediante el patrón de estrangulación:

- Contrato público de solo lectura disponible en `/api/v1/store`.
- Rutas antiguas `/api/store` conservadas para no romper el frontend actual.
- Contrato OpenAPI en `shared/api-contracts/store-v1.openapi.yaml`.
- Conexión MySQL `store` preparada mediante variables `STORE_DB_*`.
- `X-Correlation-ID` generado o propagado en todas las respuestas API.

Las rutas transaccionales no forman parte del contrato v1 en esta fase. Todavía no
debe apuntarse el dominio Store a la base separada. Antes se crearán las
migraciones exclusivas, el proceso idempotente de copia, la conciliación y el adaptador
HTTP hacia Payments.

## Compatibilidad temporal

En esta fase el código ejecutable continúa en `app/Domain/Store`. Esta carpeta representa
el destino del servicio y recibirá el runtime independiente cuando el catálogo pueda
leer de `veraguas_store` sin diferencias respecto a producción.
