# Restaurar el sistema legado

1. Detener el despliegue o tráfico que pueda escribir durante la recuperación.
2. Seleccionar un artefacto de `VeragasUnited-backups` y verificar
   `checksums.sha256`.
3. Restaurar primero en una base y ruta temporales; comparar tablas, conteos y
   archivos antes de tocar producción.
4. Aplicar la configuración validada de Apache/PHP-FPM según el sistema operativo.
5. Restaurar el código de la release anterior y ejecutar health check y pruebas de
   humo.
6. Activar mediante enlace/release versionada, reiniciar workers solo después de
   validar y registrar el resultado.

Nunca se sobrescribe producción para probar un backup. El runbook completo de datos
está en `docs/operations/backup-restore-runbook.md`.
