# Integraciones auditadas

| Integración | Estado actual | Propietario objetivo | Evidencia/riesgo |
| --- | --- | --- | --- |
| PayPal REST + webhook | Implementada en monolito | Payments | `/api/webhooks/paypal`; debe migrarse y verificar firma estrictamente |
| TiloPay | No implementada | Payments | Faltan credenciales, secreto y contrato oficial |
| Transfermarkt | Comandos semanales de importación | Web | Requiere scheduler y límites de proveedor |
| Correo | Driver configurable; local observado como log | Web | Faltan credenciales/SMTP del entorno real |
| MySQL | Activo local por WAMP | Cada backend | Debe separarse por base y usuario |
| Redis | No evidenciado localmente | Cada backend | Debe permanecer en loopback |

No se encontraron service accounts, buckets, snapshots ni Ops Agent accesibles desde
este entorno Windows.
