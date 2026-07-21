# Payments Backend

Backend objetivo y único propietario de la integración con TiloPay, verificación de
webhooks, captura, reembolsos y conciliación.

Base de datos propia: `veraguas_payments`.

Las notificaciones hacia Store y Ticketing deben estar firmadas, ser idempotentes y
ejecutarse por cola con reintentos. Un webhook no verificado nunca debe modificar una
orden comercial.
