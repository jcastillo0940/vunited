# Gateway Apache

Apache será la única entrada pública en el despliegue inicial de un servidor. Los
backends escucharán solamente en `127.0.0.1` y no expondrán sus puertos internos.

Mapa objetivo:

| Host o prefijo | Destino local |
| --- | --- |
| `veraguas.example` | Web Frontend |
| `boletos.veraguas.example` | Ticketing Frontend |
| `tienda.veraguas.example` | Store Frontend |
| `/api/v1/web` | Web Backend |
| `/api/v1/ticketing` | Ticketing Backend |
| `/api/v1/store` | Store Backend |
| `/internal/v1/payments` | Payments Backend, solo red local |

La configuración concreta se añadirá cuando se conozcan los dominios y rutas del
servidor de producción. No debe activarse un VirtualHost nuevo antes de completar las
pruebas de humo y el procedimiento de rollback.
