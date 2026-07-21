# Backlog por fases

| ID | Dominio | Tarea | Dependencias | Aceptación | Prueba | Riesgo |
| --- | --- | --- | --- | --- | --- | --- |
| F2-STORE-01 | Store | Runtime y migraciones de catálogo | backup F1, contrato v1 | migra desde cero y restaura | migración/restore/contrato | diferencias de datos |
| F3-STORE-02 | Store | Frontend independiente | API Store | paridad visual y móvil | E2E/accesibilidad | cambio de tráfico |
| F4-PAY-01 | Payments | TiloPay, intents y webhooks | credenciales oficiales | firma e idempotencia verificadas | integración/concurrencia | doble cobro |
| F5-STORE-03 | Store | órdenes e inventario transaccional | Payments F4 | cero duplicados bajo concurrencia | carga/conciliación | sobreventa |
| F6-TICK-01 | Ticketing | capacidad, holds y emisión | Payments F4 | no sobreventa ni doble uso | carga/QR | acceso duplicado |
| F7-TICK-02 | Ticketing | frontend y escáner | backend F6 | compra a ingreso auditado | E2E/dispositivos | operación offline |
| F8-WEB-01 | Web | CMS/admin independiente | APIs y auth | sin consultas cruzadas | contratos/permisos | pérdida de contenido |
| F9-OPS-01 | Infra | releases, observabilidad y retiro | dos despliegues exitosos | rollback/DR probado | performance/security | indisponibilidad |

La Fase 2 no se inicia en este commit.
