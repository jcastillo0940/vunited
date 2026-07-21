# Contribuir

1. Revisar los límites de dominio y ADR antes de cambiar una entidad.
2. No crear consultas, migraciones o modelos que crucen bases de dominios.
3. Ejecutar `php artisan test`, `npm run lint:contracts` y `git diff --check`.
4. No modificar producción manualmente; usar una release versionada y rollback.
5. Cada fase termina en un commit independiente y no inicia automáticamente la
   siguiente.
