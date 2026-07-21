# Plan de desarrollo por fases

## Objetivo

Separar progresivamente la plataforma actual en aplicaciones independientes dentro del
mismo servidor, cada una en su carpeta y con su propia base de datos, sin interrumpir la
producción. La comunicación entre backends se realizará exclusivamente mediante APIs
versionadas.

Servicios objetivo:

- Backend Web y administración.
- Frontend Web.
- Backend Store.
- Frontend Store.
- Backend Payments.
- Backend Boletería.
- Frontend Boletería y validación de accesos.

## Reglas de ejecución

1. El monolito continúa siendo la fuente productiva hasta aprobar cada corte.
2. Ningún servicio nuevo consulta directamente la base de datos de otro servicio.
3. Cada cambio mantiene un camino de rollback probado.
4. Primero se duplican y comparan lecturas; después se cambian escrituras.
5. Pagos y emisión de entradas nunca se activan sin idempotencia y conciliación.
6. Una fase no comienza su corte productivo hasta cumplir los criterios de salida de la
   fase anterior.

## Fase 0 — Línea base y protección de producción

### Trabajo

- Inventariar rutas, tablas, tareas programadas, colas, archivos y dependencias.
- Registrar CPU, RAM, disco, conexiones MySQL, tiempos p95/p99 y errores actuales.
- Confirmar respaldos externos de archivos y MySQL mediante una restauración de prueba.
- Reservar IP estática, habilitar protección contra eliminación y revisar firewall.
- Documentar configuración productiva sin guardar secretos en Git.
- Corregir primero los riesgos críticos de pagos, webhooks y acceso público a órdenes.

### Salida obligatoria

- Respaldo restaurable y rollback documentado.
- Métricas de referencia de al menos un periodo normal y un pico conocido.
- Suite actual completamente verde.
- Lista priorizada de riesgos críticos resuelta o aceptada expresamente.

## Fase 1 — Fundación del monorepo y contratos

### Trabajo

- Crear las carpetas de aplicaciones, infraestructura y contratos compartidos.
- Definir `/api/v1` para APIs públicas y `/internal/v1` para APIs internas.
- Propagar `X-Correlation-ID` en solicitudes, respuestas y logs.
- Definir autenticación entre servicios, timeouts, reintentos e idempotencia.
- Preparar Apache como gateway sin exponer puertos internos.
- Preparar conexiones y usuarios MySQL independientes con privilegios mínimos.

### Estado actual

- Estructura obligatoria de primer nivel: preparada y verificada por prueba.
- `/api/v1/store`: catálogo de solo lectura preparado dentro del monolito.
- `X-Correlation-ID`: validado, propagado en respuestas y disponible en logs.
- Conexión `store`: configurada, pero no activada en los modelos.
- Contrato OpenAPI inicial de Store: validado automáticamente.
- Dinero del contrato v1: unidades mínimas enteras y moneda ISO.
- Operaciones de orden: excluidas hasta implementar idempotencia y autorización.

### Salida obligatoria

- Convenciones aprobadas.
- Contratos validables automáticamente.
- La API antigua y la versionada producen respuestas equivalentes.
- Ningún cambio de base o tráfico productivo realizado todavía.

## Fase 2 — Backend Store: catálogo de solo lectura

### Trabajo

- Crear el runtime Laravel independiente en `store/backend`.
- Extraer categorías, productos, imágenes e inventario de lectura.
- Crear migraciones reproducibles para `veraguas_store`.
- Crear exportador/importador idempotente desde la base actual.
- Ejecutar sincronización temporal de catálogo durante la transición.
- Comparar automáticamente respuestas del monolito y del servicio nuevo.
- Enrutar solamente lecturas de catálogo al nuevo backend.

### Fuera de alcance

- Creación de órdenes.
- Descuento definitivo de inventario.
- TiloPay y webhooks.

### Salida obligatoria

- Cero diferencias de datos relevantes durante la ventana de comparación.
- Migración desde cero y restauración probadas.
- Catálogo nuevo operando con rollback inmediato al monolito.

## Fase 3 — Frontend Store independiente

### Trabajo

- Crear el frontend en `store/frontend`.
- Migrar catálogo, detalle, carrito y manejo de errores.
- Centralizar la URL del backend por ambiente.
- Mantener el carrito local sin datos sensibles.
- Publicarlo bajo una ruta o subdominio controlado por Apache.

### Salida obligatoria

- Paridad visual y funcional con el Store actual.
- Pruebas de navegación, accesibilidad básica y dispositivos móviles.
- Cambio de tráfico reversible sin afectar Web ni Boletería.

## Fase 4 — Backend Payments independiente

### Trabajo

- Crear `payments/backend` y `veraguas_payments`.
- Encapsular credenciales, creación, captura, reembolso y conciliación de TiloPay.
- Verificar criptográficamente todos los webhooks antes de procesarlos.
- Implementar `Idempotency-Key` persistente para solicitudes internas.
- Hacer idempotentes webhooks, callbacks y cambios de estado.
- Autenticar Store y Boletería mediante credenciales internas rotables.
- Probar fallos, reintentos, timeouts y eventos fuera de orden.

### Salida obligatoria

- Ningún secreto expuesto al frontend o a logs.
- Una solicitud repetida no crea cobros duplicados.
- Conciliación entre TiloPay y la base local sin diferencias.
- Rollback probado al adaptador de pagos actual.

## Fase 5 — Store transaccional

### Trabajo

- Extraer órdenes, renglones de orden e inventario a `veraguas_store`.
- Sustituir llamadas directas a clases de Payments por la API interna.
- Usar identificadores públicos no secuenciales para consultar órdenes.
- Implementar reserva/descuento de inventario con concurrencia controlada.
- Implementar compensación cuando un pago o callback falla.
- Migrar órdenes históricas necesarias y conciliarlas.
- Cambiar la escritura al servicio nuevo mediante feature flag.

### Salida obligatoria

- Cero cobros, órdenes o descuentos de stock duplicados en pruebas concurrentes.
- Conciliación completa de órdenes y pagos.
- Dos despliegues satisfactorios antes de retirar la escritura antigua.

## Fase 6 — Backend Boletería

### Trabajo

- Crear el runtime independiente y `veraguas_ticketing`.
- Extraer partidos, zonas, capacidad, órdenes, entradas y validaciones.
- Implementar reservas con expiración para evitar sobreventa.
- Consumir Payments exclusivamente por API interna.
- Reemplazar tokens predecibles por credenciales aleatorias y revocables.
- Hacer idempotentes la emisión y validación de entradas.
- Probar escenarios de 100, 300 y 1,000 usuarios concurrentes.

### Salida obligatoria

- Imposibilidad comprobada de sobreventa.
- Una entrada no puede utilizarse dos veces aun con solicitudes simultáneas.
- Operación degradada documentada para problemas de conectividad en el estadio.
- Capacidad suficiente para el pico medido con margen acordado.

## Fase 7 — Frontend Boletería y acceso

### Trabajo

- Extraer compra, confirmación, consulta de entradas y escáner.
- Separar permisos de comprador, operador y administrador.
- Diseñar estados claros para entradas válidas, usadas, anuladas o desconocidas.
- Incorporar reintentos seguros y sincronización controlada para validación.

### Salida obligatoria

- Flujo completo probado desde compra hasta ingreso al estadio.
- Pruebas en dispositivos reales usados por el personal.
- Auditoría completa de cada validación y operador.

## Fase 8 — Web y administración

### Trabajo

- Crear Backend Web con contenido, noticias, equipo, patrocinadores y configuración.
- Crear Frontend Web independiente.
- Convertir el panel administrativo en consumidor de las APIs de cada dominio.
- Implementar inicio de sesión y autorización centralizados sin compartir tablas.
- Mover archivos públicos a almacenamiento compartido o servicio de objetos.

### Salida obligatoria

- Web y administración sin consultas cruzadas entre bases.
- Permisos y auditoría equivalentes o superiores a los actuales.
- Frontend desplegable sin reiniciar los backends.

## Fase 9 — Endurecimiento y retiro del monolito

### Trabajo

- Configurar procesos PHP-FPM, workers, logs y límites por servicio.
- Centralizar monitoreo, alertas y paneles operativos.
- Ejecutar pruebas de carga, seguridad, restauración y recuperación de desastres.
- Observar cada servicio durante al menos dos despliegues exitosos.
- Eliminar rutas y tablas antiguas solo después de conciliación y aprobación.
- Evaluar con métricas si algún servicio necesita otra VM o Cloud Run.

### Salida obligatoria

- Monolito sin tráfico productivo y con respaldo final recuperable.
- Runbooks de incidentes y responsables documentados.
- Objetivos de disponibilidad y rendimiento medidos.

## Orden de aprobación

Cada fase tendrá cuatro revisiones antes de continuar:

1. Revisión funcional con el responsable del producto.
2. Revisión técnica y de seguridad.
3. Evidencia de pruebas y conciliación.
4. Aprobación del despliegue y del rollback.

## Siguiente decisión

Antes de escribir más código se debe aprobar este orden. Después se cerrará la Fase 0,
en especial respaldos, métricas y riesgos críticos, y se terminará la Fase 1 antes de
crear el runtime independiente de Store.
