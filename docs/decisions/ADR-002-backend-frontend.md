# ADR-002: backend y frontend separados

- Estado: aceptada.
- Decisión: cada frontend consume APIs y no accede a MySQL; cada backend posee su
  runtime, configuración, migraciones, pruebas y logs.
