# Ticketing Backend

Backend objetivo para partidos vendibles, zonas, capacidad, órdenes, emisión y
validación de entradas.

Base de datos propia: `veraguas_ticketing`.

La confirmación de pagos llegará desde Payments Backend mediante una API interna
autenticada e idempotente. Este servicio no almacenará credenciales de TiloPay.
