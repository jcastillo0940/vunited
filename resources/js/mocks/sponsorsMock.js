const sponsorsMock = {
    hero: {
        badge: 'ALLIANCE PROGRAM 2024',
        title: 'ALIADOS DEL INDIO',
        description:
            'Construimos alianzas que conectan a las marcas con la pasion de Veraguas. Un programa comercial pensado para crecer junto a la aficion, la cantera y la identidad del club.',
        imageUrl:
            'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=1600&q=80',
    },
    tiers: [
        {
            key: 'main-partners',
            title: 'Main Partners',
            variant: 'main',
            sponsors: [
                {
                    id: 1,
                    name: 'Banco Provincial',
                    tier: 'Main Partner',
                    tagline: 'Aliado principal de la temporada 2024',
                    shortLabel: 'BP',
                },
                {
                    id: 2,
                    name: 'Canal 7 Deportes',
                    tier: 'Main Partner',
                    tagline: 'Transmision oficial y experiencias en estadio',
                    shortLabel: 'C7',
                },
            ],
        },
        {
            key: 'official-sponsors',
            title: 'Official Sponsors',
            variant: 'official',
            sponsors: [
                {
                    id: 3,
                    name: 'Cemento Veraguas',
                    tier: 'Official Sponsor',
                    shortLabel: 'CV',
                },
                {
                    id: 4,
                    name: 'Rapi Envios',
                    tier: 'Official Sponsor',
                    shortLabel: 'RE',
                },
                {
                    id: 5,
                    name: 'Panama Segura',
                    tier: 'Official Sponsor',
                    shortLabel: 'PS',
                },
                {
                    id: 6,
                    name: 'Hotel Atalaya',
                    tier: 'Official Sponsor',
                    shortLabel: 'HA',
                },
            ],
        },
        {
            key: 'strategic-alliances',
            title: 'Alianzas Estrategicas',
            variant: 'alliance',
            sponsors: [
                {
                    id: 7,
                    name: 'Agro Centro',
                    tier: 'Alianza Estrategica',
                    shortLabel: 'AC',
                },
                {
                    id: 8,
                    name: 'Digital 360',
                    tier: 'Alianza Estrategica',
                    shortLabel: 'D360',
                },
                {
                    id: 9,
                    name: 'Fundacion Veraguas',
                    tier: 'Alianza Estrategica',
                    shortLabel: 'FV',
                },
                {
                    id: 10,
                    name: 'Cafe del Istmo',
                    tier: 'Alianza Estrategica',
                    shortLabel: 'CI',
                },
                {
                    id: 11,
                    name: 'Rutas del Indio',
                    tier: 'Alianza Estrategica',
                    shortLabel: 'RI',
                },
                {
                    id: 12,
                    name: 'Energia Central',
                    tier: 'Alianza Estrategica',
                    shortLabel: 'EC',
                },
            ],
        },
    ],
    valueProps: [
        {
            id: 1,
            icon: 'visibility',
            title: 'Visibilidad de Impacto',
            description:
                'Presencia de marca en estadio, contenidos del club y piezas de campana con alcance local y nacional.',
        },
        {
            id: 2,
            icon: 'stadium',
            title: 'Activaciones FanFest',
            description:
                'Experiencias vivenciales para conectar con la aficion antes, durante y despues de cada jornada clave.',
        },
        {
            id: 3,
            icon: 'groups',
            title: 'Red de Socios Indios',
            description:
                'Acceso a networking comercial, comunidad empresarial y colaboraciones de alto valor con otros aliados.',
        },
    ],
    leadForm: {
        title: 'UNETE A LA TRIBU EMPRESARIAL',
        description:
            'Comparte tus datos y te contactaremos para construir una propuesta comercial a la medida de tu marca.',
        interestOptions: [
            { label: 'Selecciona un nivel', value: '' },
            { label: 'Main Partner', value: 'main-partner' },
            { label: 'Official Sponsor', value: 'official-sponsor' },
            { label: 'Alianza Estrategica', value: 'strategic-alliance' },
        ],
    },
};

export function getAllSponsors() {
    return sponsorsMock.tiers.flatMap((tier) => tier.sponsors);
}

export default sponsorsMock;
