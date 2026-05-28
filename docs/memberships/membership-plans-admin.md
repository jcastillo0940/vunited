# Membership Plans Admin - Phase 4A

Fecha: 2026-05-28

## Objetivo

Eliminar el hardcode del plan publico de membresia y moverlo a backoffice para que:

- `/fanclub` consuma el plan activo real
- `/registro-tribu` use el precio real del plan activo
- `MembershipOrderService` tome precio y moneda desde base de datos

## Backoffice

Rutas admin:

- `GET /admin/membership-plans`
- `GET /admin/membership-plans/create`
- `POST /admin/membership-plans`
- `GET /admin/membership-plans/{membershipPlan}/edit`
- `PUT /admin/membership-plans/{membershipPlan}`
- `DELETE /admin/membership-plans/{membershipPlan}`

Permisos:

- `membership_plans.view`
- `membership_plans.manage`

## Campos del plan

- `code`
- `name`
- `headline`
- `description`
- `price`
- `currency`
- `duration_months`
- `benefits`
- `kit_items`
- `partner_discounts`
- `is_active`
- `sort_order`
- `metadata`

## Regla operativa

El flujo actual de membresias usa el plan activo para `code = tribu`.

Si no existe un plan activo:

- `GET /api/membership-plans/active` responde error controlado
- `POST /api/membership-orders` rechaza la solicitud con error claro
- el frontend publico puede caer temporalmente al mock visual para no romper la UX

## Impacto en frontend

`/fanclub` y `/registro-tribu` ahora intentan leer:

- `GET /api/membership-plans/active`

Si la API responde:

- el precio mostrado deja de ser hardcodeado
- beneficios, kit y aliados/descuentos se llenan desde el plan activo
- el code del plan enviado al crear la orden sale del plan activo

Si la API falla:

- se mantiene fallback mock temporal

## Precio usado para PayPal

El precio enviado a PayPal ya no sale de constantes internas.

Ahora sale del plan activo:

- `membership_orders.membership_price = membership_plans.price`
- `membership_orders.currency = membership_plans.currency`
- `payments.amount = membership_plans.price`
- `payments.currency = membership_plans.currency`

## Snapshot historico

Al crear una orden se guarda un snapshot minimo del plan en `membership_orders.metadata.plan_snapshot`.

Eso evita perder contexto historico si luego cambian:

- precio
- nombre
- beneficios
- kit
- descuentos

Ejemplo:

- una orden creada a `120.00 USD` sigue conservando ese valor historico aunque el plan despues suba a `135.00 USD`

## Restriccion de borrado

Si un plan ya tiene ordenes asociadas:

- no debe eliminarse
- debe desactivarse o editarse segun corresponda

## Siguiente paso natural

Con esto listo, el siguiente vertical administrable ya puede ser:

- tienda real
- boletos reales

sin volver a hardcodear precio ni catalogo en frontend.
