# Runbook: Incidente de Redis

## Diagnóstico rápido

```
sudo systemctl status redis-server
sudo ss -tlnp | grep redis   # debe seguir solo en 127.0.0.1/::1, nunca publico
redis-cli --user admin -a '<admin-pass>' --no-auth-warning PING
redis-cli --user admin -a '<admin-pass>' --no-auth-warning INFO memory
```

## Un servicio no puede autenticar

1. Confirmar que su usuario ACL sigue existiendo:
   ```
   redis-cli --user admin -a '<admin-pass>' --no-auth-warning ACL LIST
   ```
2. Debe verse `user veraguas_<servicio> on ... ~veraguas:<servicio>:* ...`.
   Si falta o está `off`, algo tocó `/etc/redis/users.acl` sin pasar por
   este runbook — restaurar desde
   `/etc/redis/redis.conf.orig-*`/control de versiones del archivo ACL y
   `sudo systemctl reload redis-server` (recarga ACL sin perder datos).

## Sospecha de acceso cruzado entre servicios

No debe pasar. Verificar:
```
redis-cli --user veraguas_<A> -a '<pass-A>' --no-auth-warning GET veraguas:<B>:cualquier-clave
```
Debe devolver `NOPERM`. Si no, es un incidente de seguridad: corregir el
patron `~veraguas:<A>:*` de ese usuario en `/etc/redis/users.acl` de
inmediato.

## Memoria llena / desalojos inesperados

```
redis-cli --user admin -a '<admin-pass>' --no-auth-warning INFO memory | grep -E "used_memory_human|maxmemory_human|evicted_keys"
```
`maxmemory-policy` está en `volatile-lru`: solo se desalojan claves con TTL.
Si `evicted_keys` crece rápido, revisar qué servicio está escribiendo sin
TTL en claves grandes (colas no deberían tener TTL; cache sí).

## Redis caído

```
sudo systemctl restart redis-server
```
Con `appendonly yes`, el reinicio recupera el estado desde el AOF. Afecta a
los 4 servicios nuevos (colas y cache); producción actual no usa este Redis.
