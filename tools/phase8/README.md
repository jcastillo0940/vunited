# Fase 8 — Integración, sincronización y migración

Scripts reproducibles para validar contratos, independencia de dominios, ETL idempotente, conciliación y ensayos. No ejecutan una migración productiva sin `-Final` explícito.

```powershell
pwsh ./tools/phase8/run-phase8.ps1 -Trial 1
pwsh ./tools/phase8/run-phase8.ps1 -Trial 2
pwsh ./tools/phase8/run-phase8.ps1 -Trial 3
pwsh ./tools/phase8/run-phase8.ps1 -Final
```

La ausencia de datasets legacy se registra como `blocked` y evita declarar Go automáticamente.
