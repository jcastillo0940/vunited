# Plantilla de baseline de producción

Fecha y ventana observada:

Commit o release:

Responsable:

## Infraestructura

| Métrica | Normal | Pico | Fuente |
| --- | ---: | ---: | --- |
| CPU (%) | | | Google Cloud Monitoring |
| Memoria usada (%) | | | Ops Agent |
| Disco usado (%) | | | Ops Agent |
| Disco libre (GB) | | | Ops Agent / `df` |
| Tráfico de red | | | Cloud Monitoring |

## Aplicación

| Métrica | Normal | Pico | Fuente |
| --- | ---: | ---: | --- |
| Solicitudes por minuto | | | Apache |
| Errores 4xx (%) | | | Apache / Laravel |
| Errores 5xx (%) | | | Apache / Laravel |
| Latencia p50 | | | APM / logs |
| Latencia p95 | | | APM / logs |
| Latencia p99 | | | APM / logs |
| Workers ocupados | | | PHP-FPM / colas |
| Profundidad de cola | | | Laravel |

## MySQL y negocio

| Métrica | Normal | Pico | Fuente |
| --- | ---: | ---: | --- |
| Conexiones activas | | | MySQL |
| Consultas lentas | | | slow query log |
| Tamaño total de bases | | | MySQL |
| Órdenes Store pendientes | | | Aplicación |
| Órdenes Ticketing pendientes | | | Aplicación |
| Pagos sin conciliar | | | Aplicación |
| Validaciones por minuto | | | Aplicación |

## Observaciones y decisión

Anomalías encontradas:

Capacidad disponible estimada:

Acciones obligatorias antes del despliegue:
