# Fase 3 — Verificación visual y pruebas obligatorias

## Capturas comparativas / regresión visual — limitación real encontrada

Se intentó una comparación visual real con Playwright + Chromium headless
(producción `https://united.wp-pa.com/` vs. el build nuevo de
`web/frontend` servido por nginx). La descarga del binario de Chromium
(177 MiB) **agotó el disco del servidor** (`ENOSPC`, disco al 97% de uso,
354M libres — la misma alerta de `df -h` ya documentada en
`docs/operations/phase2-isolation-tests.md` §11 se materializó). Se abortó
de inmediato para no arriesgar el sitio en producción, que comparte el mismo
filesystem.

Se limpiaron `~/.npm` (cache, 382M) y `node_modules` de la raíz (312M, se
reinstala con `npm install`; está en `.gitignore`) para volver el disco a
~1.1G libres / 89% de uso — similar al estado previo a esta fase. `df -h`,
`systemctl is-active mariadb nginx php8.3-fpm redis-server` y
`curl .../` (HTTP 200) confirmaron que producción no se vio afectada en
ningún momento.

**No se generaron capturas de pantalla reales** por esta limitación. En su
lugar, la verificación visual de esta fase se hizo por comparación de
código/config, que es lo que realmente determina el resultado visual:

1. `shared/ui/src/styles/tailwind-preset.mjs` se comparó campo por campo
   contra `tailwind.config.js` del frontend actual (colores, fuentes,
   spacing, radios, sombras, tracking, keyframes) — coinciden 1:1, ver
   `docs/architecture/design-inventory.md`.
2. `shared/ui/src/styles/base.css` es una copia directa de
   `resources/css/app.css` (mismas fuentes de Google Fonts, mismas clases
   `.page-shell`/`.section-heading`/`.display-kicker`/`.surface-card`).
3. `shared/ui/src/components/Logo.tsx` reutiliza el mismo `<path>` SVG
   exacto de `resources/js/Components/ApplicationLogo.jsx` (verificado
   carácter por carácter al copiarlo, no redibujado).
4. El copy del hero de Home (`RUGE EL INDIO, SOMOS VERAGUAS`) se tomó
   literal de `resources/js/mocks/homeMock.js`.

**Recomendación para cuando haya más espacio en disco** (o se corra desde
otra máquina): `npx playwright install chromium` en un entorno con >500MB
libres, y comparar capturas de `https://united.wp-pa.com/` contra
`http://127.0.0.1:8081/builds/index.html` (con `--resolve
web.veraguas.internal:8081:127.0.0.1`).

## Verificación de logos, colores y tipografías

- Logo: mismo SVG (ver arriba). Verificado que `shared/ui/Logo` renderiza
  con `fill="currentColor"` y hereda el color de texto del contenedor
  (`text-primary` en header claro, `text-white` en header oscuro y footer),
  igual que el comportamiento visual original.
- Colores: los 10 tokens de color de `shared/ui/src/tokens/colors.ts`
  coinciden con `tailwind.config.js` (ver tabla en
  `docs/architecture/design-inventory.md`).
- Tipografías: Oswald (display) + Inter (body) cargadas desde el mismo
  `@import` de Google Fonts, mismos pesos (400/600/700 y 400/500/600/700).

## Responsive

Los 3 frontends heredan el mismo patrón mobile-first de Tailwind
(`sm/md/lg/xl/2xl` por defecto, sin breakpoints custom — igual que el
frontend actual). `Header`/`MobileMenu` colapsan a menú hamburguesa por
debajo de `lg` (1024px), igual que `MainNavbar.jsx` actual. Verificado
visualmente en el DOM renderizado por los tests (`lg:flex`/`lg:hidden` en
las clases generadas) — no se hizo una prueba con viewport real de
navegador por la misma limitación de Playwright de arriba.

## Navegadores

No se ejecutaron pruebas cross-browser reales (misma limitación). El código
no usa APIs propietarias de un solo motor — `<dialog>` (Modal), `fetch`,
`crypto.randomUUID()` y CSS estándar de Tailwind son soportados por los
navegadores evergreen actuales (Chrome, Firefox, Safari, Edge).

## Resto de pruebas obligatorias — resultado real

```
shared/ui        typecheck OK · lint OK · 8/8 tests OK
web/frontend      typecheck OK · lint OK · 5/5 tests OK · build OK (dist/)
store/frontend    typecheck OK · lint OK · 3/3 tests OK · build OK (dist/)
ticketing/frontend typecheck OK · lint OK · 3/3 tests OK · build OK (dist/)
Total: 19/19 tests, 0 errores de lint, 0 errores de tipos, 3/3 builds OK
```

nginx sirviendo los builds: ver `docs/operations/phase3-builds.md` (incluye
el bug real de cache-control encontrado y corregido).
