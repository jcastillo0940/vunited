# Vite Manifest Local Note

Fecha: 2026-05-27

## Contexto

Durante la fase visual publica se detecto un comportamiento intermitente en entorno local Windows donde `public/build/manifest.json` alternaba entre claves relativas y claves absolutas, por ejemplo:

- `resources/js/app.jsx`
- `F:/weVeraguas/resources/js/app.jsx`

Esto provocaba fallos esporadicos en `php artisan test` al resolver assets desde `resources/views/app.blade.php`, aun cuando `npm run build` terminaba correctamente.

## Mitigacion aplicada

En [F:\weVeraguas\resources\views\app.blade.php](F:\weVeraguas\resources\views\app.blade.php) se implemento una resolucion tolerante del entrypoint:

1. intenta la clave relativa
2. si no existe, intenta la clave absoluta
3. si no hay manifest o no coincide ninguna clave, vuelve al path relativo

## Motivo de seguridad

La mitigacion es aceptable porque:

- no toca backend funcional ni logica de negocio
- no altera autenticacion, permisos ni datos
- no cambia el bundle emitido por Vite
- mantiene el comportamiento normal esperado en produccion Linux/macOS, donde lo correcto sigue siendo la clave relativa
- evita que el entorno local Windows rompa la renderizacion Inertia por una diferencia de formato en el manifest

## Limite de la mitigacion

Esta solucion debe considerarse una proteccion de compatibilidad para entorno local Windows, no una preferencia arquitectonica.

Si en una futura fase se identifica la causa exacta en Vite o en el plugin Laravel Vite para Windows, lo ideal seria corregir la generacion del manifest en origen y simplificar nuevamente `app.blade.php`.

## Estado

- build local: verificado
- tests: verificados
- rutas publicas clave: verificadas
- seguro continuar con fases visuales mock: si
