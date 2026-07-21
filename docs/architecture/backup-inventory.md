# Inventario de respaldo de Fase 1

Artefacto verificado: `C:\Users\JEREMY CASTILLO\Documents\VeragasUnited-backups\phase1-20260721T021822Z`.

- 20 dumps individuales no sistémicos y `all-databases.sql`.
- Código copiado sin `.env`, `vendor`, `node_modules` ni builds generados.
- Medios de `storage/app/public` copiados; el origen local no tenía archivos de
  medios útiles (`MEDIA_FILES=1` corresponde al directorio de respaldo).
- Apache WAMP (`httpd.conf`, vhosts) y `php.ini` copiados.
- Inventarios de procesos, servicios, puertos, firewall, certificados, Git y claves
  requeridas sin valores.
- Inventarios de servicios y puertos capturados antes y después del backup.
- Checksums SHA-256 en `checksums.sha256`: 928 archivos verificados sin diferencias.
- Restauración temporal de `weveraguas`: 49 tablas verificadas; la base temporal fue
  eliminada (`TEMP_DATABASES_REMAIN=0`).
- Configuración restaurada en `restore-test`; `httpd.exe -t` y PHP con `php.ini`
  restaurado terminaron con código 0.

El backup está fuera del repositorio y fuera de cualquier carpeta pública. No se
elimina durante el proyecto.
