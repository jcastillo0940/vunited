# Mapa de dominios y propiedad

| Dominio | Propiedad | No debe hacer |
| --- | --- | --- |
| Web | identidad, CMS, navegación, contenido, roles, permisos, auditoría | pagos, stock, capacidad o emisión |
| Store | catálogo, precios, promociones, inventario, carrito, órdenes y fulfillment | leer Payments o emitir tickets |
| Ticketing | eventos, zonas, capacidad, holds, órdenes, QR y validación | capturar tarjetas o leer Payments |
| Payments | TiloPay, intents, transacciones, webhooks, firmas y conciliación | cambiar órdenes, stock o capacidad ajenas |

Las referencias entre dominios serán UUID/ULID o identificadores opacos. `shared/`
solo contiene contratos, UI y configuración técnica; no es un dominio compartido.
