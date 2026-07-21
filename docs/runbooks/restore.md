# Runbook: Restore

**Operación destructiva**: sobreescribe el contenido actual de la base de
datos del servicio. Confirmar antes con quien reportó el incidente.

## Pasos

1. Ubicar el backup a restaurar:
   ```
   ls -la /var/www/veraguas-<servicio>/backups/
   ```
2. Confirmar su checksum (el script lo hace también, pero conviene
   verificar a mano si el archivo se movió/copió):
   ```
   sha256sum -c /var/www/veraguas-<servicio>/backups/<archivo>.sql.enc.sha256
   ```
3. Restaurar (requiere `--yes` explícito):
   ```
   sudo -u veraguas-<servicio> infrastructure/scripts/restore-database.sh \
       <servicio> /var/www/veraguas-<servicio>/backups/<archivo>.sql.enc --yes
   ```
4. Verificar:
   ```
   infrastructure/scripts/health-check.sh <servicio>
   ```
5. Si el restore fue para recuperar de un incidente en curso, seguir
   también `database-incident.md`.

## Notas

- El script descifra a un archivo temporal en `tmp/` con permisos `600` y lo
  borra automáticamente al terminar (`trap ... EXIT`), incluso si falla a
  mitad de camino.
- Nunca se imprime la contraseña de la base ni la llave de cifrado en la
  salida del script.
