# Arquitectura objetivo del monorepo

## Decisión

Veraguas United evolucionará desde la aplicación Laravel integrada de la raíz hacia
aplicaciones independientes dentro del mismo repositorio y, en la primera etapa,
dentro del mismo servidor.

La separación será por dominio y no por tecnología. Cada backend tendrá su propio
código, proceso, variables de entorno, migraciones, pruebas y base de datos. Los
frontends no tendrán acceso a bases de datos.

## Topología inicial

```text
Internet
   |
Apache / gateway / TLS
   |-- Web Frontend -------- Web Backend -------- veraguas_web
   |-- Ticketing Frontend -- Ticketing Backend -- veraguas_ticketing
   |                              |
   |-- Store Frontend ------ Store Backend ------ veraguas_store
                                  |
                            Payments Backend ---- veraguas_payments
                                  |
                                TiloPay
```

Todos los procesos pueden coexistir en un único servidor. La separación lógica
permite trasladar una aplicación a otra máquina posteriormente sin modificar sus
responsabilidades ni compartir tablas.

## Límites de dominio

### Web

Es propietario de identidad administrativa, permisos, CMS, páginas, menús, noticias,
configuración global, jugadores, cuerpo técnico, directiva, patrocinadores, estadio,
FanFest y expediciones.

Puede publicar referencias estables de partidos, productos o promociones, pero no
es propietario de inventario, capacidad ni pagos.

### Ticketing

Es propietario de eventos vendibles, zonas, capacidad, órdenes de entradas, entradas
emitidas y validaciones en puerta. Debe garantizar reserva o decremento atómico de
capacidad y evitar la reventa de una misma plaza por concurrencia.

### Store

Es propietario de categorías, productos, inventario, carrito validado del lado del
servidor, órdenes y fulfillment. Debe garantizar movimientos atómicos de inventario.

### Payments

Es el único propietario de credenciales TiloPay, creación y captura de pagos,
verificación de webhooks, reembolsos y conciliación. Nunca procesa un webhook con
verificación omitida o fallida.

## Reglas de datos

1. Cada backend utiliza un usuario MySQL diferente con permisos solo sobre su base.
2. No se permiten claves foráneas ni consultas SQL entre bases de distintos dominios.
3. Las referencias externas se guardan como identificadores opacos, no como IDs
   autoincrementales públicos.
4. Los datos personales de órdenes requieren un token de consulta aleatorio o una
   sesión autenticada.
5. Los cambios de estado entre servicios son idempotentes y quedan auditados.
6. Los backups y restauraciones se prueban por base de datos.

## Comunicación

Los navegadores consumen APIs públicas a través del gateway. Las rutas internas no
son accesibles desde Internet.

```text
POST /api/v1/ticketing/orders
POST /api/v1/store/orders
POST /internal/v1/payments/intents
POST /internal/v1/ticketing/payment-events
POST /internal/v1/store/payment-events
```

En el primer servidor las llamadas internas usan `127.0.0.1` y un secreto rotatorio
por servicio. La evolución futura puede reemplazarlo por una red privada o identidad
de servicio sin cambiar el contrato.

## Consistencia

No se utilizarán transacciones distribuidas. El proceso comercial seguirá una saga:

1. El dominio comercial crea una orden pendiente con `Idempotency-Key`.
2. Payments crea la intención en TiloPay.
3. TiloPay devuelve al comprador; el retorno del navegador no confirma el pago.
4. Payments verifica el webhook y registra el evento.
5. Payments notifica al dominio propietario mediante una cola con reintentos.
6. Ticketing o Store aplica el evento una sola vez y confirma la orden.
7. Una tarea de conciliación repara eventos demorados o fallidos.

## Estrategia de extracción

La aplicación actual seguirá funcionando en la raíz hasta finalizar la transición.
No se hará un movimiento masivo de archivos.

### Fase 0: preparación

- Crear carpetas y documentación.
- Congelar los contratos actuales mediante pruebas.
- Corregir seguridad de TiloPay y consultas públicas de órdenes.
- Definir métricas, logs y backups.

### Fase 1: Store

- Crear `veraguas_store`.
- Copiar y adaptar migraciones y modelos de Store.
- Implementar API v1 y pruebas contractuales.
- Migrar datos con doble lectura controlada, sin doble escritura permanente.
- Cambiar el tráfico de Store y conservar rollback.

### Fase 2: Ticketing

- Crear `veraguas_ticketing`.
- Migrar partidos vendibles, zonas, órdenes y entradas.
- Incorporar reserva atómica de capacidad y tokens seguros.
- Cambiar el tráfico después de una prueba de carga equivalente al pico esperado.

### Fase 3: Payments

- Crear `veraguas_payments`.
- Centralizar credenciales y webhooks.
- Implementar captura, idempotencia, conciliación y reembolsos.
- Conectar Store y Ticketing exclusivamente por contratos internos.

### Fase 4: Web

- Extraer CMS, identidad y administración.
- Separar el frontend institucional.
- Eliminar el código legado solo después del periodo de estabilidad acordado.

## Criterios para mover un dominio a otro servidor

La separación física se justifica cuando exista evidencia de al menos una condición:

- CPU sostenida superior al 70 % en periodos de tráfico normal.
- Memoria sostenida superior al 80 %.
- El tiempo de respuesta p95 supera el objetivo aun después de optimizar consultas y
  caché.
- Los despliegues de un dominio afectan la disponibilidad de otro.
- Se requiere una política de seguridad o disponibilidad distinta.
- El coste medido de escalar el servidor completo supera el de aislar un servicio.

Un pico de 3,000 asistentes, por sí solo, no requiere cuatro servidores.
