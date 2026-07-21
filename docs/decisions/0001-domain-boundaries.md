# ADR 0001: límites por dominio

- Estado: aceptada para la reconstrucción.
- Decisión: Web, Store, Ticketing y Payments son propietarios exclusivos de sus
  bases y se integran por APIs versionadas y eventos idempotentes.
- Consecuencia: no hay consultas, modelos, migraciones ni claves foráneas entre
  bases. `shared/` solo contiene UI, contratos y configuración técnica.
