# Contingencia de validación en puerta — Fase 7 §12

## Qué ocurre sin Internet

El escáner (`ticketing/frontend` PWA, ver `docs/architecture/ticketing-scanner.md`)
pierde la capacidad de llamar a `POST /api/validate`. Sin ese endpoint no hay
forma de marcar un ticket como `used` de forma **atómica y compartida** entre
puertas — la atomicidad de `TicketValidationService` (un solo `UPDATE ...
WHERE status='issued'`) depende de que todas las puertas escriban contra la
**misma** base de datos.

## Qué se precarga

Antes del evento, con conexión, el operador sincroniza en el dispositivo:

- La lista de `qr_token` **firmados** (HMAC) de los tickets `issued` para
  ese evento — no los datos personales del comprador, solo
  `ticket_public_id` + `event_id` firmados (lo mismo que ya va en el QR).
- Un set local de tokens marcados "usado" localmente durante el modo sin
  conexión (vacío al empezar).

Con eso, el modo degradado puede:

1. **Verificar la firma** del QR sin red (detecta alteración/QR inventado).
2. **Verificar que el token pertenece a este evento** sin red.
3. **Rechazar un token que YA fue marcado usado en ESTE dispositivo**
   durante la sesión sin conexión.

Con eso NO puede:

4. Saber si ese mismo ticket **ya fue usado en OTRA puerta** (otro
   dispositivo) durante la misma ventana sin conexión.

## Por qué no se puede prometer más que eso

**No se afirma que dos escáneres totalmente desconectados entre sí puedan
impedir matemáticamente el doble uso.** Es imposible sin coordinación: cada
dispositivo aislado solo conoce su propio historial local. Si el mismo
boleto (mismo QR, por ejemplo una foto de pantalla compartida) se presenta
en la Puerta A y la Puerta B al mismo tiempo, y ambas están sin conexión,
**ambas puertas lo aceptarán como válido**, porque ninguna de las dos sabe
lo que hizo la otra. Esto es una limitación física de la falta de un
estado compartido, no un defecto del código — cualquier sistema que
prometa lo contrario para dispositivos genuinamente aislados está
mintiendo sobre lo que es posible.

## Cómo se mitiga (mientras no hay red)

- **Minimizar puertas simultáneas en modo offline**: si hay más de una
  puerta y se cae la red, la coordinación operativa (ver "quién coordina"
  abajo) debe decidir si cerrar puertas redundantes temporalmente en vez de
  operar todas en paralelo sin coordinación.
- **Ventana corta**: cuanto menos tiempo dure el modo sin conexión, menor la
  probabilidad de que el mismo ticket se presente dos veces en puertas
  distintas antes de reconectar.
- **Detección visual**: el ticket (QR + representación visual) puede
  incluir un identificador corto legible (no el token completo) que el
  operador puede anotar/memorizar para comparar manualmente en casos
  sospechosos — medida humana, no técnica.

## Cómo se sincroniza al recuperar conexión

1. Cada dispositivo sube su lista local de tokens marcados "usado" durante
   el modo offline, con timestamp de escaneo y `device_id`.
2. El backend aplica cada uno contra `TicketValidationService::validate()`
   normalmente (mismo `UPDATE ... WHERE status='issued'` atómico).
3. **El primero en llegar al backend gana** (por orden de sincronización,
   no por hora del escaneo local — los relojes de dispositivo no son una
   fuente de verdad confiable). Los que lleguen después para el mismo
   ticket se registran como `already_used` en `validation_events`, igual
   que un doble escaneo en línea.
4. Toda validación offline sincronizada tardíamente queda con
   `correlation_id` propio y se puede auditar en `validation_events`
   filtrando por `device_id` + rango de tiempo.

## Quién coordina

Debe existir **un responsable operativo por evento** (no un dispositivo, una
persona) con:

- Radio o chat directo con todos los operadores de puerta.
- Autoridad para decidir cerrar puertas si la red cae y hay más de una
  puerta activa.
- Visibilidad del estado de red de cada dispositivo (aunque sea manual:
  "¿tienes señal?" por radio).

Sin esta persona, la mitigación de "minimizar puertas simultáneas" no se
puede ejecutar — es un proceso humano, no algo que el software resuelva
por sí solo.

## Cómo se concilian las validaciones después del evento

- `validation_events` es el registro de auditoría completo: todo escaneo
  (válido, rechazado, offline sincronizado tarde) queda ahí con
  `ticket_id`, `result`, `door_id`, `operator_id`, `device_id`,
  `correlation_id`, `occurred_at`.
- El reporte post-evento (backoffice, Fase 7 §13) debe poder listar:
  - Tickets con más de un intento de escaneo (incluyendo los rechazados).
  - Tickets validados en modo offline con timestamp de sincronización
    distinto al de escaneo local (evidencia de una ventana sin conexión).
- Esto no deshace un posible doble ingreso físico ya ocurrido, pero permite
  saber exactamente cuándo y dónde pasó, para decisiones operativas futuras
  (más puertas con red redundante, personal adicional, etc.).

## Riesgos que permanecen

- Doble ingreso físico con el mismo QR en puertas distintas durante una
  ventana sin conexión con más de una puerta activa **sigue siendo
  posible** — se minimiza, no se elimina.
- Un QR compartido (captura de pantalla) sigue siendo indistinguible de el
  original mientras no haya validado un primer uso — el sistema detecta
  el **segundo** uso, no previene que alguien comparta la imagen antes del
  primer escaneo.
- La reconciliación depende de que los dispositivos efectivamente
  sincronicen — un dispositivo que nunca vuelve a tener red dentro de la
  ventana operativa relevante deja huecos en la auditoría hasta que lo haga.
