# Registro de riesgos previo a la Fase 1

| ID | Prioridad | Riesgo | Control requerido | Bloquea |
| --- | --- | --- | --- | --- |
| SEC-001 | Crítica | Webhooks `skipped` pueden procesarse sin verificación | Procesar exclusivamente eventos verificados y configurar webhook ID | Tráfico transaccional |
| SEC-002 | Alta | Una orden puede consultarse usando un número predecible | Token público aleatorio o autenticación del propietario | APIs nuevas de órdenes |
| PAY-001 | Crítica | Reintentos de checkout pueden crear pagos duplicados | `Idempotency-Key` persistente y respuesta reproducible | Store/Payments independiente |
| DATA-001 | Alta | Números de orden basados en conteo pueden colisionar | UUID/ULID o secuencia transaccional | Escritura concurrente |
| OPS-001 | Crítica | Backup programado no implica restauración comprobada | Ejecutar runbook y conservar evidencia | Primer despliegue |
| OPS-002 | Alta | Disco productivo de 10 GB ofrece poco margen | Medir uso, alertar y ampliar antes de extraer runtimes | Despliegue productivo |
| OPS-003 | Alta | IP efímera y protección de eliminación inactiva | Reservar IP y habilitar protección | Cambio de gateway/DNS |

## Criterio

Los riesgos pueden corregirse o aceptarse temporalmente por escrito con responsable,
fecha límite y mitigación. Los marcados como críticos no pueden aceptarse para activar
nuevos flujos transaccionales.
