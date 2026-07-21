# Veraguas United Store

Backend Laravel independiente para catálogo, promociones, inventario, carrito, checkout, órdenes y fulfillment. Store sólo llama a Payments por su API interna; no importa código ni consulta la base de Payments.

Configurar `.env.example` con `veraguas_store`, Redis `veraguas_store_` y un token de servicio fuera del repositorio. Los importes se almacenan como enteros en CRC.

```bash
php artisan migrate:fresh --env=testing
php artisan test
```

El contrato público está descrito en `docs/openapi.yaml`. La reserva de stock usa transacciones y locks; el frontend nunca es fuente de verdad.
