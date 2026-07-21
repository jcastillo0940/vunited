# Preparación para iniciar la Fase 1

## Estado

La preparación local está lista. El inicio de la Fase 1 no cambia bases de datos ni
tráfico productivo. Los controles sobre la VM deben ejecutarse y documentarse antes de
cualquier despliegue.

## Controles locales

| Control | Estado |
| --- | --- |
| Estructura objetivo creada | Listo |
| Roadmap aprobado como documento base | Listo |
| API Store versionada compatible | Listo |
| Correlation ID en APIs | Listo |
| Conexión Store preparada, no activada | Listo |
| Contrato OpenAPI Store inicial | Listo |
| Suite completa | 411 pruebas y 1,260 assertions aprobadas |
| Preflight local automatizado | Listo |

Ejecutar en Windows:

```powershell
.\infrastructure\scripts\phase0-preflight.ps1 -RunTests
```

## Controles pendientes sobre producción

- [ ] Reservar la IP pública como estática.
- [ ] Habilitar protección contra eliminación.
- [ ] Confirmar reglas explícitas para 80/443 y restricción de SSH.
- [ ] Revisar espacio del disco de 10 GB y planificar ampliación a 50 GB o más.
- [ ] Confirmar snapshots diarios existentes y su retención.
- [ ] Crear un backup lógico cifrado de MySQL fuera de la VM.
- [ ] Restaurar ese backup en una base temporal y comparar conteos.
- [ ] Registrar baseline de CPU, RAM, disco, MySQL, errores y latencia.
- [ ] Confirmar `APP_ENV=production`, `APP_DEBUG=false` y cookies seguras.
- [ ] Confirmar las credenciales y el secreto de webhook reales de TiloPay.

Ejecutar en Debian sin modificar el servidor:

```bash
chmod +x infrastructure/scripts/phase0-preflight.sh
BASE_URL=https://dominio.example \
  infrastructure/scripts/phase0-preflight.sh /ruta/al/release/actual
```

## Puertas de seguridad

Estas tareas no bloquean el trabajo estructural de la Fase 1, pero bloquean cualquier
cambio transaccional o de tráfico:

1. Los webhooks con verificación `skipped` no deben actualizar pagos ni órdenes.
2. El acceso público a órdenes debe usar un token aleatorio, no solo el número de orden.
3. Las creaciones de órdenes deben implementar `Idempotency-Key` persistente.
4. La generación de números de orden debe soportar concurrencia sin colisiones.
5. Los respaldos deben demostrar restauración, no solo programación.

## Autorización de inicio

La Fase 1 puede comenzar en desarrollo local cuando el preflight local termine en verde.
No se desplegará en producción hasta completar y registrar los controles pendientes de
la VM.
