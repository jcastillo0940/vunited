export function buildPublicHeaderLinks(activeUrl = '') {
    const items = [
        { label: 'Inicio', url: '/' },
        { label: 'Calendario', url: '/calendario' },
        {
            label: 'El Club',
            url: null,
            pending: false,
            children: [
                { label: 'Directiva',        url: '/directiva' },
                { label: 'Plantilla',        url: '/plantilla' },
                { label: 'Fuerzas Básicas',  url: '/fuerzas-basicas' },
                { label: 'Pruebas',          url: '/pruebas' },
                { label: 'Estadio',          url: '/estadio' },
                { label: 'Patrocinadores',   url: '/patrocinadores' },
                { label: 'FanFest',          url: '/fanfest' },
                { label: 'Expedición India', url: '/expedicion-india' },
            ],
        },
        { label: 'Noticias', url: '/noticias' },
        { label: 'Boletos',  url: '/boletos' },
    ];

    return items.map((item) => withActiveState(item, activeUrl));
}

export function buildPublicFooterLinks() {
    return [
        { label: 'Historia del club',  url: null, pending: true, pendingLabel: 'CMS pendiente' },
        { label: 'Directiva',          url: '/directiva' },
        { label: 'Plantilla',          url: '/plantilla' },
        { label: 'Fuerzas Básicas',    url: '/fuerzas-basicas' },
        { label: 'Estadio',            url: '/estadio' },
        { label: 'Calendario',         url: '/calendario' },
        { label: 'Noticias',           url: '/noticias' },
        { label: 'Boletos',            url: '/boletos' },
        { label: 'Tienda',             url: '/tienda' },
        { label: 'FanFest',            url: '/fanfest' },
        { label: 'Expedición India',   url: '/expedicion-india' },
        { label: 'Patrocinadores',     url: '/patrocinadores' },
        { label: 'Pruebas',            url: '/pruebas' },
    ];
}

export const publicLegalLinks = [
    { label: 'Aviso legal', url: null, pending: true, pendingLabel: 'CMS pendiente' },
    { label: 'Privacidad',  url: null, pending: true, pendingLabel: 'CMS pendiente' },
    { label: 'Prensa',      url: '/noticias' },
];

export const publicPrimaryCta = {
    label:        'UNETE A LA TRIBU',
    url:          '/fanclub',
    pending:      false,
    pendingLabel: null,
};

function withActiveState(item, activeUrl) {
    const children   = item.children?.map((child) => withActiveState(child, activeUrl)) ?? [];
    const selfActive = item.url ? item.url === activeUrl : false;
    const childActive = children.some((child) => child.active);

    return {
        ...item,
        active: selfActive || childActive,
        children,
    };
}
