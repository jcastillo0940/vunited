# PayPal Future Integration

Fecha: 2026-05-27

## Decision

PayPal sera el proveedor principal de pagos reales del sistema en una futura fase llamada `Payment Foundation`.

Esta decision no implica implementacion activa en las pantallas actuales. En la fase visual actual:

- no se procesan pagos reales
- no se crean ordenes PayPal
- no se capturan pagos
- no se persisten transacciones
- no se integran webhooks

## Alcance Futuro

La implementacion real de PayPal quedara para una fase posterior de arquitectura de pagos. Esa fase debera cubrir:

- configuracion segura desde backoffice
- abstraccion de proveedor de pagos
- flujo backend de orden y captura
- persistencia de pagos
- validacion de estado final
- sincronizacion via webhooks

## Configuracion en Backoffice

El administrador debera poder configurar PayPal desde el backoffice con, como minimo:

- `mode`: `sandbox` o `live`
- `client_id`
- `client_secret`
- `webhook_id`
- `currency`
- `enabled`

Requisitos:

- las credenciales sensibles deben guardarse cifradas o protegidas
- nunca deben hardcodearse en frontend
- el `client_secret` nunca debe exponerse al navegador

## Reglas de Seguridad

Reglas obligatorias para la futura implementacion:

- el frontend nunca debe recibir ni exponer `client_secret`
- Laravel backend debe crear la orden PayPal
- Laravel backend debe capturar la orden PayPal
- Laravel backend debe registrar el payment
- Laravel backend debe verificar el estado final del pago
- Laravel backend debe recibir y procesar webhooks
- el sistema no debe confiar solo en el frontend para marcar un pago como exitoso
- nunca se debe guardar numero de tarjeta en base de datos
- nunca se debe guardar CVV en base de datos

## Abstraccion de Proveedor

PayPal debe implementarse dentro de una abstraccion de pagos para permitir extensibilidad futura.

Interfaces y clases previstas:

- `PaymentProvider`
- `PayPalPaymentProvider`

La abstraccion debe aislar:

- creacion de orden
- captura
- consulta de estado
- normalizacion de respuesta
- validacion de eventos webhook

## Flujo Real Esperado

El flujo real de PayPal debe quedar orquestado por backend Laravel:

1. el frontend solicita iniciar pago
2. Laravel crea orden PayPal
3. Laravel devuelve al frontend solo los datos necesarios para continuar el flujo seguro
4. el usuario autoriza el pago
5. Laravel captura la orden PayPal
6. Laravel registra el payment local
7. Laravel verifica estado final
8. webhooks de PayPal confirman y sincronizan eventos posteriores

## Modelo de Datos Futuro

La futura tabla `payments` debe permitir asociacion polimorfica con ordenes o reservas de distintos dominios.

Asociaciones previstas:

- `membership_orders`
- `ticket_orders`
- `store_orders`
- `bus_reservations` si luego se cobra ese modulo

Objetivo:

- unificar el registro de pagos sin acoplar la tabla a un solo dominio

## Webhooks

Los webhooks de PayPal seran obligatorios para:

- confirmar eventos de pago
- mantener sincronizado el estado local
- detectar capturas, anulaciones, reversiones o discrepancias
- fortalecer la consistencia entre PayPal y la base de datos local

## Estado de las Pantallas Actuales

Hasta que exista `Payment Foundation`, las pantallas actuales deben seguir siendo visuales o mock.

Aplica explicitamente a:

- `Registro Tribu`
- `Boletos`
- `Tienda`

Restricciones actuales:

- no procesar pagos reales
- no enviar datos sensibles a backend
- no almacenar tarjetas
- no almacenar CVV
- no marcar pagos como completados reales

## Nota de Arquitectura

Esta decision documenta el destino tecnico del sistema de pagos, pero no autoriza implementacion parcial o improvisada en frontend.

Cualquier fase futura que active cobros reales debera:

- nacer desde backend
- respetar la abstraccion `PaymentProvider`
- proteger secretos
- validar estado final con PayPal
- integrar webhooks antes de considerarse lista para produccion

---

## Estado de implementación (actualizado 2026-05-27)

| Fase | Estado | Entregable |
|---|---|---|
| Phase 3A — Payment Settings admin | ✅ DONE | `payment_settings` table, backoffice, permisos, audit |
| Phase 3B — PaymentProvider abstraction | ✅ DONE | Interface, DTO, enum — sin implementación concreta |
| Phase 3C — Payments lifecycle base | ✅ DONE | `payments` table, modelo, `PaymentLifecycleService` |
| Phase 3D — PayPal Sandbox Provider | ✅ DONE | `PayPalPaymentProvider` + `PayPalAccessTokenService` |
| Phase 3E — Webhooks | ✅ DONE | `POST /api/webhooks/paypal`, `payment_events`, verificación de firma |
| Phase 3F→3H — Conexión módulos | ⏳ Pendiente | Membresías, boletos, tienda |

Las pantallas de comercio (`/boletos`, `/tienda`, `/carrito`, `/registro-tribu`) siguen siendo mock visual hasta que Phase 3D→3F estén completas y verificadas en sandbox.
