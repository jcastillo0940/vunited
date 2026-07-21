# SEO y accesibilidad — Fase 3

## Implementado

| Requisito | Dónde | Notas |
| --- | --- | --- |
| Semántica HTML | `shared/ui/Header`, `Footer`, `Layout`, `Table`, `Tabs`, `Modal` (`<dialog>`) | `<nav>`, `<main>`, `<footer>`, `<table><thead><tbody>`, roles ARIA nativos |
| Titles | `web/frontend/src/seo/Seo.tsx` | `document.title` por página, formato `Título · Veraguas United FC` |
| Meta description | `Seo.tsx` | Una por página; `index.html` trae un valor por defecto para el primer render |
| Canonical | `Seo.tsx` | `<link rel="canonical">` generado con `SITE_URL + canonicalPath` |
| Open Graph | `Seo.tsx` | `og:title`, `og:description`, `og:type`, `og:url`, `og:image` opcional |
| Sitemap base | `web/frontend/scripts/generate-seo-files.mjs` → `public/sitemap.xml` | Se genera antes del build (`npm run build` corre `generate:seo` primero) |
| Robots por ambiente | mismo script | `VITE_ENV=production` → `Allow: /`, `Disallow: /admin`; cualquier otro ambiente → `Disallow: /` (evita indexar staging). Store y Ticketing usan `Disallow: /` estático (son flujos transaccionales, no contenido a indexar) |
| Focus visible | `shared/ui/src/styles/base.css` (`:focus-visible`) | Outline de 2px en `accent`, aplica a los 3 frontends vía el CSS base compartido |
| Navegación por teclado | `Header`/`MobileMenu`/`Tabs`/`Modal`/`Drawer` | Todos los elementos interactivos son `<button>`/`<a>` reales; `Modal` usa `<dialog>` nativo (Esc cierra, foco atrapado por el navegador); `Drawer` cambió su backdrop de `<div onClick>` a `<button>` para ser operable por teclado |
| Labels | `shared/ui/FormField` | Asocia `label[for]` ↔ `input[id]` explícitamente; probado en `shared/ui` (`FormField.test.tsx`) |
| Skip links | `shared/ui/SkipLink`, usado en `Layout` y `AdminLayout` | Oculto hasta recibir foco (`.skip-link`), salta a `#main-content` / `#admin-content` |
| Mensajes accesibles | `FieldError` (`role="alert"`), `ErrorState`/`Alert` (`role="alert"`/`"status"`), `Spinner` (`role="status"` + `aria-label`), `ToastProvider` (`aria-live="polite"`) | |

## Hallazgo real de contraste (no corregido — ver justificación)

`text-accent` (`#5BC2E7`) usado como **color de texto** sobre fondo blanco en
`.display-kicker` (la clase "eyebrow" uppercase de 10px usada en kickers de
sección en los 3 frontends) tiene un ratio de contraste calculado de
**≈2.0:1**, muy por debajo del mínimo WCAG AA para texto normal (4.5:1) e
incluso del umbral de texto grande (3:1 — no aplica aquí, el texto es de
10px).

Este valor **ya existía en el frontend institucional actual**
(`resources/css/app.css`, `.display-kicker { ... text-accent }`) — no es una
regresión introducida en esta fase. Por la instrucción explícita de "no
rediseño" de la Fase 3, **no se cambió el color** unilateralmente en
`shared/ui`. Queda documentado aquí como decisión pendiente para quien sea
dueño de la marca: o se acepta el kicker como decorativo/no esencial para la
comprensión del contenido (defendible bajo WCAG 1.4.3 si el texto es
puramente decorativo y la información no depende de leerlo), o se ajusta el
tono de `accent` para texto pequeño en una fase de diseño explícita.

Todos los demás pares de color verificados sí cumplen AA:

| Texto | Fondo | Ratio aprox. | Resultado |
| --- | --- | --- | --- |
| `#1D428A` (primary) | `#FFFFFF` | ~8.6:1 | AA/AAA |
| `#2B2B2B` (text-main) | `#FFFFFF` | ~13:1 | AA/AAA |
| `#FFFFFF` | `#1D428A` (primary) | ~8.6:1 | AA/AAA |
| `#2B2B2B` (text-main) | `#F4F6F9` (surface) | ~12.3:1 | AA/AAA |

## Pendiente (fuera de alcance de esta fase)

- SSR/pre-render por página para que `title`/`description`/OG sean visibles
  a crawlers que no ejecutan JavaScript (la solución actual con
  `document.title` funciona para Google/bots modernos pero no para todos).
  Documentado como mejora futura, no bloqueante para Fase 3.
- Pruebas automatizadas de contraste (axe-core / Lighthouse CI) no se
  integraron en esta fase; el hallazgo de arriba se verificó a mano con la
  fórmula de luminancia relativa de WCAG.
