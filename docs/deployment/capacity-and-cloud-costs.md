# Capacidad y estrategia de infraestructura

## Línea base actual de producción

Inventario informado el 20 de julio de 2026:

- Compute Engine `veraguasunited` en `us-central1-a`.
- Debian 12, arquitectura x86-64.
- Máquina `e2-medium`: 2 vCPU y 4 GiB de RAM.
- Disco persistente balanceado de 10 GB.
- IP pública efímera.
- Reinicio automático y migración en vivo habilitados.
- vTPM y supervisión de integridad habilitados; arranque seguro deshabilitado.
- Protección contra eliminación deshabilitada.
- Reglas automáticas de tráfico HTTP/HTTPS no marcadas en la instancia.

Esta máquina puede seguir operando durante la fase de preparación y extracción gradual
si las métricas actuales son saludables. Sin embargo, 2 vCPU, 4 GiB de RAM y 10 GB de
disco dejan poco margen para ejecutar simultáneamente varios backends PHP-FPM, MySQL,
Redis, workers de cola y compilaciones.

Antes de activar los servicios separados en producción:

1. Ampliar preferiblemente a 4 vCPU y 8 GiB de RAM, después de observar las métricas
   actuales y programando la parada necesaria para cambiar el tipo de máquina.
2. Ampliar el disco al menos a 50 GB. El disco puede crecer posteriormente, pero no
   reducirse; revisar también partición y sistema de archivos después del cambio.
3. Reservar la IP pública como estática antes de depender de ella para DNS o integraciones.
4. Habilitar protección contra eliminación accidental.
5. Confirmar reglas de firewall explícitas para 80/443 y restringir SSH; los puertos
   internos de cada servicio no deben exponerse a Internet.
6. Confirmar que la política diaria realmente genera instantáneas recuperables. La
   existencia de una programación no sustituye una prueba de restauración ni un respaldo
   externo de MySQL.
7. Mantener secretos fuera del repositorio y revisar los permisos de la cuenta de
   servicio con el principio de mínimo privilegio.

La conexión a puertos en serie puede permanecer deshabilitada. Solo resulta útil como
canal de recuperación cuando la red o SSH dejan de funcionar; no es requisito para la
aplicación, Apache, MySQL ni los despliegues normales.

## Decisión recomendada para la etapa actual

Mantener todos los servicios en un solo servidor, separados por carpetas, procesos,
usuarios de base de datos y contratos API. No se recomiendan cuatro máquinas virtuales
permanentes en esta etapa.

La asistencia máxima de 3,000 personas no equivale a 3,000 solicitudes simultáneas.
El pico real se concentra en la apertura de ventas, validaciones de acceso y minutos
previos al partido. Antes de dividir servidores se deben medir concurrencia, solicitudes
por segundo, CPU, memoria, conexiones MySQL y percentiles de latencia.

Configuración inicial orientativa:

- 4 vCPU y 8 GiB de RAM.
- 80–120 GiB de almacenamiento SSD.
- Apache como puerta de entrada y PHP-FPM para cada backend.
- MySQL local inicialmente, con un esquema y usuario independiente por servicio.
- Redis para caché, sesiones, colas y bloqueos de inventario.
- Copias de seguridad diarias fuera del servidor y restauraciones probadas.
- Monitoreo de CPU, RAM, disco, MySQL, colas, errores HTTP y latencias p95/p99.

## Alternativas

### Un servidor para todo

Es la opción recomendada ahora: menor costo y menor complejidad operativa. Mantiene el
punto único de falla actual, por lo que exige respaldos externos, monitoreo y un plan de
recuperación.

### Un servidor de aplicación y Cloud SQL

Separa la base de datos del servidor web y simplifica respaldos y mantenimiento del
motor, pero añade un costo mensual permanente. Es un buen siguiente paso si MySQL se
convierte en el cuello de botella o si la continuidad operativa justifica el servicio
administrado.

### Cloud Run por servicio y Cloud SQL

Es una opción futura especialmente adecuada para Boletería y Pagos: Cloud Run puede
escalar a cero cuando no recibe tráfico y cobrar por uso con facturación basada en
solicitudes. No requiere administrar Kubernetes, pero sí contenerizar cada backend y
externalizar sesiones, archivos, colas y base de datos.

La página oficial de precios de Cloud Run incluye una capa gratuita y un ejemplo de
10 millones de solicitudes mensuales por aproximadamente USD 13.69 en una región y
configuración concretas. Es solo una referencia: región, CPU, memoria, concurrencia,
tiempo de ejecución y tráfico cambian el importe.

Cloud SQL se cobra permanentemente mientras la instancia está activa. En la tabla
oficial de `us-central1`, una instancia compartida `db-f1-micro` figura a USD 0.0105 por
hora y `db-g1-small` a USD 0.035 por hora, antes de almacenamiento, respaldos, red e
impuestos. Los tipos compartidos no tienen SLA y no deben asumirse como la configuración
final de producción.

Referencias oficiales:

- [Precios de Cloud Run](https://cloud.google.com/run/pricing)
- [Descripción general y escalado de Cloud Run](https://docs.cloud.google.com/run/docs/overview/what-is-cloud-run)
- [Precios de Cloud SQL](https://cloud.google.com/sql/pricing)
- [Capa gratuita de Google Cloud](https://docs.cloud.google.com/free/docs/free-cloud-features)

### Cuatro máquinas virtuales permanentes

No se recomienda todavía. Multiplica sistemas operativos, discos, certificados,
monitoreo, copias de seguridad, despliegues y gasto ocioso. Separar una máquina por Web,
Boletería, Pagos y Store tiene sentido solo cuando las métricas o los requisitos de
seguridad/disponibilidad lo justifiquen.

## Cuándo separar físicamente un servicio

Evaluar una máquina o plataforma independiente cuando ocurra al menos una de estas
condiciones de forma sostenida:

- CPU mayor a 70% o memoria mayor a 80% durante los picos.
- Latencia p95 fuera del objetivo aun después de optimizar consultas y caché.
- Un despliegue o incidente de un módulo afecta a los demás.
- Boletería necesita escalar durante pocas horas sin escalar todo el sistema.
- Pagos requiere aislamiento adicional por seguridad o cumplimiento.
- El costo medido de una plataforma elástica es menor que mantener capacidad ociosa.

## Prueba antes del siguiente cambio

Registrar primero el tráfico real de producción. Después ejecutar pruebas escalonadas
con escenarios de 100, 300 y 1,000 usuarios concurrentes, incluyendo consulta de eventos,
reserva, compra, confirmación de pago y validación de entradas. La prueba debe confirmar:

- ausencia de sobreventa y operaciones duplicadas;
- idempotencia en pagos y confirmaciones;
- estabilidad de conexiones MySQL y workers de cola;
- recuperación después de fallos parciales;
- latencia y uso de recursos dentro de los objetivos acordados.

La decisión de escalar debe basarse en estas mediciones, no únicamente en la capacidad
total del estadio.
