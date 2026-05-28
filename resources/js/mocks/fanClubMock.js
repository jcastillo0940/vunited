const fanClubMock = {
    hero: {
        title: 'UNETE A',
        highlight: 'LA TRIBU',
        description:
            'El orgullo de Veraguas se vive desde adentro. Hazte miembro y disfruta de beneficios exclusivos durante toda la temporada.',
        imageUrl:
            'https://images.unsplash.com/photo-1547347298-4074fc3086f0?auto=format&fit=crop&w=1600&q=80',
        primaryAction: {
            label: 'SER SOCIO AHORA',
            href: '/registro-tribu',
        },
        secondaryAction: {
            label: 'VER BENEFICIOS',
            href: '#beneficios',
        },
    },
    annualPass: {
        eyebrow: 'PASE ANUAL',
        name: 'EL INDIO ABONADO',
        badge: '2 JUEGOS GRATIS',
        price: '$120.00',
        cadence: '/ temporada',
        ctaLabel: 'ADQUIRIR MI ABONO',
        ctaHref: '/registro-tribu',
        bullets: [
            {
                icon: 'confirmation_number',
                text: 'Acceso a todos los partidos de local (fase regular)',
            },
            {
                icon: 'airline_seat_recline_extra',
                text: 'Asiento preferencial garantizado',
            },
            {
                icon: 'badge',
                text: 'Carnet digital de miembro exclusivo',
            },
        ],
    },
    salesCopy: {
        eyebrow: 'SIENTE LA PASION',
        title: 'MAS QUE UN FAN,',
        highlight: 'ERES PARTE DEL CLUB.',
        description:
            'Ser un Indio Abonado no es solo comprar una entrada, es asegurar tu lugar en la historia. Apoya al equipo de la provincia y disfruta de la mejor experiencia futbolistica del estadio.',
        stats: [
            { value: '10+', label: 'Partidos en casa' },
            { value: 'VIP', label: 'Acceso preferencial' },
        ],
    },
    benefits: [
        {
            id: 1,
            icon: 'shopping_bag',
            title: 'TIENDA OFICIAL (20% OFF)',
            description:
                'Precios exclusivos en indumentaria y accesorios oficiales de Veraguas United.',
        },
        {
            id: 2,
            icon: 'handshake',
            title: 'COMERCIOS ALIADOS',
            description:
                'Descuentos especiales en restaurantes, gimnasios y clinicas deportivas afiliadas.',
        },
        {
            id: 3,
            icon: 'lock_open',
            title: 'ENTRENAMIENTOS',
            description:
                'Invitaciones exclusivas para presenciar sesiones a puerta cerrada y convivencias.',
        },
        {
            id: 4,
            icon: 'event_available',
            title: 'PREVENTA EXCLUSIVA',
            description:
                'Prioridad de compra en boletos para clasicos, finales y partidos de alta demanda.',
        },
    ],
    welcomeKit: [
        {
            id: 1,
            title: 'CAMISETA OFICIAL',
            description: 'Diseno exclusivo La Tribu 2024',
            imageUrl:
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80',
        },
        {
            id: 2,
            title: 'TERMO DE ACERO',
            description: 'Inoxidable con grabado laser del escudo',
            imageUrl:
                'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=900&q=80',
        },
        {
            id: 3,
            title: 'BANDANA',
            description: 'Edicion especial conmemorativa',
            imageUrl:
                'https://images.unsplash.com/photo-1523398002811-999ca8dec234?auto=format&fit=crop&w=900&q=80',
        },
    ],
    allies: [
        { id: 1, name: 'Cafe Atalaya', shortLabel: 'CA' },
        { id: 2, name: 'Rapi Envios', shortLabel: 'RE' },
        { id: 3, name: 'Hotel Santiago', shortLabel: 'HS' },
        { id: 4, name: 'Gimnasio Titan', shortLabel: 'GT' },
        { id: 5, name: 'Clinica SportsMed', shortLabel: 'SM' },
        { id: 6, name: 'Mercado Veraguas', shortLabel: 'MV' },
    ],
    finalCta: {
        title: 'FORMA PARTE DEL CORAZON DE VERAGUAS',
        description:
            'Conecta con el club, apoya a la plantilla y vive beneficios exclusivos durante toda la temporada.',
        actionLabel: 'QUIERO SER DE LA TRIBU',
        actionHref: '/registro-tribu',
    },
};

export default fanClubMock;
