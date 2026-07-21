# Fase 8 — cierre de integración

Los tres ensayos se ejecutan con `tools/phase8/run-phase8.ps1 -Trial 1|2|3`. Cada reporte registra dataset, tiempos, conteos, diferencias, errores, correcciones y pasos manuales. La corrida final requiere `-Final`, snapshot y dataset legacy disponible; el script produce `NO-GO` si falta cualquiera de esas evidencias.

La compatibilidad de contraseñas legacy no se asume: si el algoritmo no está documentado, se fuerza restablecimiento mediante el flujo de recuperación de Web.
