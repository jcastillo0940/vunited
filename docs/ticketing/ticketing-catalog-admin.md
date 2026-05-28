# Ticketing Catalog Admin

Fecha: 2026-05-28

## Objetivo

Phase 4C mueve `/boletos` desde un mock visual puro a un catalogo real administrable desde backoffice.

En esta fase:

- los partidos salen de base de datos
- las zonas y precios salen de base de datos
- la disponibilidad inicial es informativa
- `/boletos` consume API publica real
- el flujo de pago sigue siendo mock
- no existen `ticket_orders`
- no existe QR real

## Backoffice

Rutas admin creadas:

- `GET /admin/match-events`
- `GET /admin/match-events/create`
- `POST /admin/match-events`
- `GET /admin/match-events/{matchEvent}/edit`
- `PUT /admin/match-events/{matchEvent}`
- `DELETE /admin/match-events/{matchEvent}`
- `GET /admin/match-events/{matchEvent}/ticket-zones`
- `GET /admin/match-events/{matchEvent}/ticket-zones/create`
- `POST /admin/match-events/{matchEvent}/ticket-zones`
- `GET /admin/match-events/{matchEvent}/ticket-zones/{ticketZone}/edit`
- `PUT /admin/match-events/{matchEvent}/ticket-zones/{ticketZone}`
- `DELETE /admin/match-events/{matchEvent}/ticket-zones/{ticketZone}`

Permisos:

- `match_events.view`
- `match_events.manage`
- `ticket_zones.view`
- `ticket_zones.manage`

## Que puede gestionar el administrador

Partidos:

- code
- equipos local y visitante
- competencia
- jornada
- fecha y hora
- estadio
- ubicacion
- estado
- marcador
- destacado
- activo/inactivo
- metadata JSON

Zonas:

- nombre
- slug
- descripcion
- precio
- moneda
- capacidad
- disponibilidad
- orden
- activa/inactiva
- metadata JSON

## Reglas operativas

- solo partidos activos salen en API publica
- solo zonas activas salen en API publica
- el partido destacado sale por `is_featured = true`
- `available_quantity` es informativo por ahora
- no se crean ordenes de boletos en esta fase
- no se procesa pago
- no se genera QR real

Protecciones:

- no se permite borrar un partido con zonas asociadas
- para zonas, el flujo recomendado sigue siendo desactivar antes que borrar

## API publica consumida por /boletos

- `GET /api/ticketing/matches`
- `GET /api/ticketing/matches/featured`
- `GET /api/ticketing/matches/{code}`
- `GET /api/ticketing/matches/{code}/zones`

## Como impacta en el frontend

`/boletos` ahora intenta cargar:

- partido destacado activo
- zonas activas del partido

Si la API falla, el frontend usa fallback temporal basado en `ticketsMock.js` para no romper la experiencia local.

## Que sigue mock todavia

- seleccion de cantidad como estado local
- CTA `PAGAR AHORA`
- estado de exito
- boleto digital
- QR
- orden de ticket
- validacion de entrada
- pago real
- PayPal para boletos

## Siguiente fase recomendada

La siguiente fase natural para ticketing es:

- `Ticket Orders + PayPal`

Antes de eso todavia faltan:

- modelo de orden de boleto
- snapshot de partido/zona/precio
- flujo real de confirmacion post-pago
- emision real de ticket digital
- QR real y validacion de acceso
