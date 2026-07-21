# ADR-006: Apache, PHP-FPM y React estático

- Estado: aceptada para producción.
- Decisión: Apache es gateway TLS; frontends React se sirven como archivos
  estáticos; Laravel corre en PHP-FPM; no se usa `artisan serve` ni Vite dev en
  producción.
