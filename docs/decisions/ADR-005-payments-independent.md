# ADR-005: Payments independiente

- Estado: aceptada.
- Decisión: Payments es el único propietario de TiloPay, firmas, intents,
  transacciones, reembolsos y conciliación; Store y Ticketing nunca procesan
  webhooks ni secretos del proveedor.
