# Fase 7 — Pruebas de carga: límites reales documentados

## Lo que se probó de verdad

| Escenario | Resultado real |
| --- | --- |
| 10 compradores concurrentes por el último cupo (capacidad=1) | Exactamente 1 éxito, 9 rechazos, `capacity_available` nunca negativo |
| 10 escaneos simultáneos del mismo boleto | Exactamente 1 `valid`, 4 `already_used` |
| 10 reservas simultáneas del mismo asiento único | Exactamente 1 éxito |
| **50 compradores concurrentes por 20 cupos** | Exactamente 20 éxitos, 30 rechazos, cupo nunca negativo |

Todas usan `pcntl_fork` con procesos reales (no hilos simulados en un solo
proceso), cada uno con su propia conexión a MySQL, contra la base de datos
real `veraguas_ticketing_test`.

## Por qué no se llegó a 100 / 300 / 1,000 en este servidor

Esta VM **sirve producción real** (`united.wp-pa.com`) al mismo tiempo que
se ejecutaron estas pruebas. Recursos reales medidos antes de decidir:

```
MySQL max_connections = 151
CPU: 2 núcleos
RAM: 3.8Gi total, 1.7Gi libre en el momento de la prueba
```

Abrir 100+ conexiones MySQL concurrentes de prueba consumiría más de la
mitad del límite de conexiones del servidor **compartido con producción**,
y 300-1,000 procesos PHP forkeados en una VM de 2 núcleos competirían
directamente por CPU con los procesos reales de `united.wp-pa.com`. El
riesgo de degradar o tumbar producción por una prueba de carga no es
aceptable sin una ventana de mantenimiento coordinada — que no se pidió
para esta fase.

**No se afirma que el sistema soporte 1,000 compradores concurrentes.** Lo
que sí está probado, con evidencia real y reproducible, es que el mecanismo
de atomicidad (`UPDATE ... WHERE capacity_available >= N`) es
matemáticamente correcto e independiente del volumen — no hay ninguna razón
técnica por la que dejaría de serlo a mayor escala (el motor de base de
datos, no la aplicación, es quien garantiza la atomicidad de cada
`UPDATE` individual, sin importar cuántos lleguen a la vez), pero **la
capacidad de la VM para atender esa carga sin degradarse es una pregunta
distinta**, no respondida aquí.

## Cómo probar 100/300/1,000 de verdad

Recomendación para cuando sea necesario:

1. Levantar una instancia separada (staging), con `veraguas_ticketing`
   restaurado desde un backup real (`infrastructure/scripts/backup-database.sh`
   / `restore-database.sh` de Fase 2).
2. Usar una herramienta de carga HTTP real (k6, Gatling, Locust) contra el
   endpoint público `POST /api/events/{id}/orders`, no solo el servicio de
   capacidad en aislamiento — eso adicionalmente mide el límite real del
   pool PHP-FPM (`pm.max_children` de Fase 2, hoy en 6 para `ticketing`;
   subirlo es una decisión de capacidad, no de código).
3. Medir p95/p99 de latencia y tasa de error, no solo "¿sobrevendió?".
4. Repetir el mismo assert de esta fase (compradores exitosos == cupo
   inicial, nunca más) a la escala real que se quiera soportar.
