# Runbook de migración sin interrupción

## Principio

La versión de producción actual no se reemplazará con un cambio masivo. Cada dominio
se extraerá, validará y activará de manera independiente.

## Checklist por dominio

- [ ] Inventario de tablas, modelos, rutas, tareas y archivos.
- [ ] Contrato OpenAPI versionado.
- [ ] Nueva base y usuario con privilegios mínimos.
- [ ] Migraciones reproducibles desde cero.
- [ ] Exportador e importador idempotentes de datos existentes.
- [ ] Pruebas unitarias, integración, contrato y carga.
- [ ] Logs con `X-Correlation-ID`.
- [ ] Métricas y alarmas.
- [ ] Backups y restauración ensayados.
- [ ] Despliegue paralelo sin tráfico.
- [ ] Comparación de respuestas entre legado y servicio nuevo.
- [ ] Cambio gradual de tráfico.
- [ ] Ventana de observación.
- [ ] Rollback probado.

## Orden aprobado

1. Store.
2. Ticketing.
3. Payments.
4. Web y administración.

Payments se implementa en paralelo desde el inicio, pero no recibe tráfico productivo
hasta corregir y probar captura, webhooks e idempotencia. Store y Ticketing pueden
extraerse inicialmente usando un adaptador compatible con el flujo actual.

## Condición de finalización

Un módulo solo se elimina de la aplicación raíz después de:

- Dos despliegues exitosos del servicio nuevo.
- Ausencia de errores críticos durante el periodo de observación.
- Conciliación completa de órdenes y pagos.
- Confirmación de backups recuperables.
- Aprobación explícita del responsable del producto.
