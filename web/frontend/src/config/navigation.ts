import type { NavLink } from '@veraguas/ui';
import type { FooterColumn } from '@veraguas/ui';

// Replica resources/js/config/publicNavigation.js (frontend actual).
// Nota de paridad: el submenú desplegable "El Club" se aplana aquí en una
// lista simple — shared/ui/Navigation todavía no soporta un segundo nivel
// (ver docs/architecture/design-inventory.md, columna "Diferencias
// justificadas"). El contenido y las rutas son las mismas.
const CLUB_LINKS: NavLink[] = [
    { label: 'Directiva', url: '/directiva' },
    { label: 'Plantilla', url: '/plantilla' },
    { label: 'Fuerzas Básicas', url: '/fuerzas-basicas' },
    { label: 'Pruebas', url: '/pruebas' },
    { label: 'Estadio', url: '/estadio' },
    { label: 'Patrocinadores', url: '/patrocinadores' },
    { label: 'FanFest', url: '/fanfest' },
    { label: 'Expedición India', url: '/expedicion-india' },
];

export function buildHeaderLinks(activeUrl: string): NavLink[] {
    const items: NavLink[] = [
        { label: 'Inicio', url: '/' },
        { label: 'Calendario', url: '/calendario' },
        ...CLUB_LINKS,
        { label: 'Noticias', url: '/noticias' },
        { label: 'Boletos', url: '/boletos' },
    ];
    return items.map((item) => ({ ...item, active: item.url === activeUrl }));
}

export const footerColumns: FooterColumn[] = [
    {
        title: 'Club',
        items: [
            { label: 'Historia del club', url: null },
            { label: 'Directiva', url: '/directiva' },
            { label: 'Plantilla', url: '/plantilla' },
            { label: 'Fuerzas Básicas', url: '/fuerzas-basicas' },
            { label: 'Estadio', url: '/estadio' },
        ],
    },
    {
        title: 'Comunidad',
        items: [
            { label: 'Calendario', url: '/calendario' },
            { label: 'Noticias', url: '/noticias' },
            { label: 'Boletos', url: '/boletos' },
            { label: 'Tienda', url: '/tienda' },
        ],
    },
    {
        title: 'Club social',
        items: [
            { label: 'FanFest', url: '/fanfest' },
            { label: 'Expedición India', url: '/expedicion-india' },
            { label: 'Patrocinadores', url: '/patrocinadores' },
            { label: 'Pruebas', url: '/pruebas' },
        ],
    },
];

export const legalLinks = [
    { label: 'Aviso legal', url: '/legal/aviso' },
    { label: 'Privacidad', url: '/legal/privacidad' },
    { label: 'Prensa', url: '/noticias' },
];

export const primaryCta = { label: 'ÚNETE A LA TRIBU', href: '/fanclub' };
