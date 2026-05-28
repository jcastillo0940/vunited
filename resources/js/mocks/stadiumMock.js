const stadiumMock = {
    hero: {
        title: 'ESTADIO',
        highlight: 'ATALAYA',
        description:
            'La casa del rugido indio. Un punto de encuentro para la provincia, el futbol y la energia de cada jornada en Veraguas.',
        imageUrl:
            'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1600&q=80',
    },
    info: {
        name: 'Estadio Atalaya',
        subtitle: 'Casa oficial de Veraguas United FC',
        location: 'Santiago, Veraguas, Panama',
        capacity: '8,500 aficionados',
        address: 'Via Atalaya, Santiago de Veraguas',
        venueType: 'Sede principal del club',
        actionLabel: 'COMO LLEGAR',
        actionHref: 'https://maps.google.com',
    },
    map: {
        title: 'Ubicacion del estadio',
        description:
            'Placeholder visual para futura integracion de mapa, accesos oficiales y recomendaciones de estacionamiento.',
        pinLabel: 'ATALAYA',
        actionLabel: 'ABRIR EN GOOGLE MAPS',
        actionHref: 'https://maps.google.com',
    },
    zones: [
        {
            id: 'general',
            name: 'General',
            badge: 'SUR / NORTE',
            description: 'Acceso amplio para vivir la grada popular con la tribuna local.',
            feature: 'Ambiente de barra y vision abierta del campo.',
        },
        {
            id: 'preferencial',
            name: 'Preferencial',
            badge: 'ESTE / OESTE',
            description: 'Ubicacion intermedia con mejor cercania al juego y acceso agil.',
            feature: 'Asientos mas comodos y mejor angulo del partido.',
        },
        {
            id: 'vip',
            name: 'VIP Indio',
            badge: 'PREMIUM',
            description: 'Experiencia destacada con ingreso prioritario y zona exclusiva.',
            feature: 'Accesos preferenciales y hospitalidad visual premium.',
        },
        {
            id: 'visitante',
            name: 'Visitante',
            badge: 'CONTROLADA',
            description: 'Area delimitada para aficion rival con circulacion independiente.',
            feature: 'Acceso senalizado y control operativo de seguridad.',
        },
    ],
    matchday: [
        {
            id: 1,
            icon: 'route',
            title: 'Llegada al estadio',
            description: 'Accesos perimetrales claros, ingreso escalonado y apoyo logístico visual en jornada.',
        },
        {
            id: 2,
            icon: 'event_seat',
            title: 'Accesos y gradas',
            description: 'Orientacion por zonas, puertas senalizadas y experiencia de tribuna ordenada.',
        },
        {
            id: 3,
            icon: 'local_dining',
            title: 'Comida y activaciones',
            description: 'Puntos de venta, fan zone y experiencia de comunidad antes y despues del partido.',
        },
        {
            id: 4,
            icon: 'verified_user',
            title: 'Seguridad y asistencia',
            description: 'Protocolos de ingreso, personal de apoyo y recomendaciones para una visita segura.',
        },
        {
            id: 5,
            icon: 'shopping_bag',
            title: 'Tienda y fan zone',
            description: 'Espacios comerciales y de experiencia alrededor del colorido del club.',
        },
    ],
    rules: [
        'Llega con anticipacion para evitar filas en accesos principales.',
        'Ten tu boleto digital visual listo antes de ingresar a la zona.',
        'Respeta las indicaciones del personal operativo y de seguridad.',
        'Evita objetos prohibidos y sigue la senalizacion del recinto.',
    ],
    cta: {
        title: 'VIVE EL PARTIDO DESDE LA CASA DEL INDIO',
        description:
            'Consulta zonas, visualiza la experiencia de estadio y prepárate para tu próxima jornada junto al club.',
        actionLabel: 'COMPRAR BOLETOS',
        actionHref: '/boletos',
    },
};

export default stadiumMock;
