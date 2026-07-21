# Runbook: Backup

## Automático

4 timers systemd corren `backup-database.sh <servicio>` diariamente:

```
systemctl list-timers 'veraguas-backup@*'
```

Cada corrida: `mysqldump --single-transaction` -> cifrado
`openssl enc -aes-256-cbc -pbkdf2` con la llave en
`shared/.backup-key` -> checksum `sha256sum` -> copia externa best-effort a
GCS (si `VERAGUAS_BACKUP_BUCKET` está configurada y hay permisos) ->
retención de 14 días -> log en `shared/logs/backup.log`.

## Manual

```
sudo -u veraguas-<servicio> infrastructure/scripts/backup-database.sh <servicio> [dias-retencion]
```

## Verificar que un backup quedó bien

```
ls -la /var/www/veraguas-<servicio>/backups/
sha256sum -c <archivo>.sql.enc.sha256   # comparar contra el .sha256 asociado
tail -5 /var/www/veraguas-<servicio>/shared/logs/backup.log
```

Un `status=FAIL` en `backup.log`, o la ausencia de una entrada nueva tras la
hora programada del timer, es la señal de alerta (ver
`infrastructure/observability/alert-policies/`).

## Gap conocido

La copia externa a GCS está bloqueada por permisos IAM de la cuenta de
servicio de la VM (`storage.buckets.list` denegado). El backup local sigue
funcionando; ver `docs/security/secrets-management.md` para el mismo tipo de
bloqueo y su plan de resolución.
