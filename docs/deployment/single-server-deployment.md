# Despliegue en un servidor con aplicaciones separadas

## Objetivo

Ejecutar Web, Ticketing, Store y Payments como aplicaciones independientes en un
solo servidor, sin Kubernetes y con posibilidad de trasladarlas posteriormente.

Este documento describe la arquitectura objetivo. Mientras dure la migración, el
despliegue actual de la aplicación en la raíz continúa siendo la versión productiva.

## Capacidad inicial recomendada

Para el tráfico descrito, el punto de partida razonable es:

- 4 vCPU.
- 8 GiB de RAM.
- SSD de 80 a 120 GiB.
- MySQL en el mismo servidor durante la primera etapa.
- Redis para caché, rate limiting, sesiones y colas.
- Apache como gateway y terminación TLS.
- Backups diarios externos al servidor.

Si las imágenes y videos crecen, deben moverse a almacenamiento de objetos; no se
deben escalar los discos locales indefinidamente.

## Procesos y puertos internos

| Aplicación | Puerto sugerido | Exposición |
| --- | ---: | --- |
| Web Backend | 8010 | Solo `127.0.0.1` |
| Ticketing Backend | 8020 | Solo `127.0.0.1` |
| Store Backend | 8030 | Solo `127.0.0.1` |
| Payments Backend | 8040 | Solo `127.0.0.1` |
| Web Frontend | Archivos estáticos | Apache |
| Ticketing Frontend | Archivos estáticos | Apache |
| Store Frontend | Archivos estáticos | Apache |

En producción, PHP-FPM es preferible a ejecutar `php artisan serve`. Cada backend
tendrá su propio pool y límites de procesos para impedir que un pico de boletería
consuma toda la memoria disponible.

## Bases de datos y usuarios

```sql
CREATE DATABASE veraguas_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE veraguas_ticketing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE veraguas_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE veraguas_payments CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Cada base debe tener un usuario exclusivo. Ningún usuario de aplicación tendrá
permisos globales ni acceso a otra base.

## Variables mínimas por backend

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dominio-correspondiente
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=base_del_servicio
DB_USERNAME=usuario_del_servicio
DB_PASSWORD=secreto_independiente

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_SECURE_COOKIE=true
```

Payments tendrá además secretos de TiloPay y secretos diferentes para autenticar a
Store y Ticketing. Los secretos no se guardarán en Git.

## Procedimiento de despliegue

1. Activar mantenimiento únicamente para el servicio afectado, si es necesario.
2. Crear backup de su base y registrar la versión desplegada.
3. Descargar el código en un directorio de release nuevo.
4. Ejecutar `composer install --no-dev --prefer-dist --optimize-autoloader`.
5. Ejecutar instalación reproducible del frontend con `npm ci` y `npm run build`.
6. Ejecutar migraciones compatibles hacia adelante con `php artisan migrate --force`.
7. Ejecutar `config:cache`, `route:cache` y `view:cache`.
8. Cambiar el enlace `current` o la ruta del VirtualHost al nuevo release.
9. Reiniciar únicamente el pool PHP y workers del servicio afectado.
10. Ejecutar pruebas de humo.
11. Retirar mantenimiento y observar métricas durante al menos 15 minutos.

No se debe ejecutar `migrate:fresh`, seeders destructivos ni actualizaciones de
dependencias no incluidas en el release.

## Pruebas de humo

- `/up` responde 200 en cada backend.
- Página principal, tienda y boletería responden 200.
- El administrador puede iniciar sesión.
- Las APIs públicas devuelven datos esperados.
- Store puede crear una orden de prueba sin duplicarla.
- Ticketing respeta capacidad e idempotencia.
- Payments rechaza webhooks no verificados.
- Un pago sandbox puede crearse, aprobarse, capturarse y conciliarse.
- Los workers de cola procesan y reintentan correctamente.
- No aparecen errores nuevos en logs.

## Despliegue gradual y rollback

El gateway permite cambiar solamente una parte del tráfico. Al extraer Store, por
ejemplo, Web y Ticketing siguen apuntando a la aplicación actual.

Rollback:

1. Detener el cambio de tráfico del servicio afectado.
2. Volver el gateway al release anterior.
3. Reiniciar su pool y workers.
4. No revertir automáticamente una migración destructiva.
5. Restaurar base únicamente después de confirmar el alcance de escrituras ocurridas.
6. Registrar el incidente y los identificadores de órdenes afectados.

Las migraciones deben diseñarse para permitir que la versión anterior y la nueva
coexistan durante la ventana de despliegue.

## Backups

- Backup lógico diario de cada base.
- Backup previo a cada migración.
- Copia cifrada fuera del servidor.
- Retención mínima sugerida: 7 diarios, 4 semanales y 6 mensuales.
- Prueba trimestral de restauración.
- Exportación separada de archivos multimedia.

Un backup que nunca se ha restaurado no se considera verificado.

## Observabilidad y límites

Medir por servicio:

- Solicitudes por minuto y errores 4xx/5xx.
- Latencia p50, p95 y p99.
- CPU, memoria y disco.
- Conexiones y consultas lentas de MySQL.
- Profundidad y antigüedad de colas.
- Órdenes pendientes y pagos sin conciliar.
- Capacidad vendida por partido.

Apache, PHP-FPM y los workers deben tener límites independientes. Durante una venta
de entradas se puede aumentar temporalmente el pool de Ticketing sin modificar Web o
Store.

## Momento de escalar físicamente

Primero se optimizan índices, caché, consultas, archivos estáticos y concurrencia.
Después se aumenta verticalmente el servidor. Solo entonces, y con métricas que lo
justifiquen, se mueve Ticketing o Payments a otra máquina.

Para un sistema mayormente inactivo con picos breves, un servicio bajo demanda como
Cloud Run puede ser más económico que cuatro máquinas virtuales permanentes. La base
de datos seguirá siendo el componente que requiere más cuidado de disponibilidad y
coste.
