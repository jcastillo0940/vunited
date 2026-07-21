# Runbook de backup y restauración

## Alcance

Antes de la Fase 1 se debe demostrar que la base actual y los archivos subidos pueden
restaurarse. No se guardarán contraseñas ni dumps dentro del repositorio.

## Backup

1. Registrar fecha, commit desplegado, hostname y versión de MySQL.
2. Crear dumps separados de todas las bases con rutinas, triggers y eventos.
3. Comprimir y cifrar el resultado.
4. Calcular SHA-256 y registrar tamaño y checksum.
5. Copiarlo a una ubicación externa a la VM.
6. Crear un snapshot o archivo inmutable del código y medios actuales.
7. Copiar la configuración activa de Apache y PHP-FPM conservando permisos.
8. Registrar unidades systemd, Supervisor, timers y cronjobs sin modificarlos.
9. Registrar dominios, rutas y certificados; registrar solo los nombres de variables
   requeridas, nunca sus valores secretos.
10. No escribir la contraseña en el historial del shell; usar un archivo de opciones
   temporal con permisos restringidos o el gestor de secretos disponible.

## Restauración de prueba

1. Crear una base temporal vacía sin sobrescribir producción.
2. Restaurar el dump.
3. Ejecutar `CHECK TABLE` sobre las tablas de órdenes, pagos, productos y entradas.
4. Comparar conteos por tabla con los registrados al crear el backup.
5. Probar consultas de una orden Store, una orden Ticketing, un pago y una entrada.
6. Verificar que los archivos referenciados existan en el respaldo de medios.
7. Documentar duración, errores y resultado.
8. Validar una configuración respaldada de Apache con `apachectl -t -f` en un entorno
   aislado o comparar su checksum después de restaurarla en una ruta temporal.
9. Validar una configuración PHP-FPM restaurada en una ruta temporal con la opción de
   prueba de configuración correspondiente a la versión instalada.
10. Eliminar la base temporal solo después de confirmar y registrar el resultado.

## Evidencia mínima

- Identificador del backup y ubicación externa.
- SHA-256 verificado.
- Conteos antes y después.
- Tiempo de backup y restauración.
- Nombre del responsable.
- Resultado: aprobado o rechazado.
- Inventario de Apache, PHP-FPM, systemd, Supervisor, cron, dominios y certificados.

Un snapshot de disco complementa este proceso, pero no sustituye el dump lógico ni la
prueba de restauración.
