const registrationConfirmationMock = {
    hero: {
        title: 'BIENVENIDO A LA TRIBU',
        description:
            'Ya eres parte del orgullo de Veraguas. Tu pasion ahora tiene un lugar oficial en el corazon del equipo.',
    },
    memberCard: {
        membershipTitle: 'SOCIO INDIO',
        membershipSubtitle: 'MEMBRESIA OFICIAL 2024',
        memberName: 'JUAN PEREZ',
        membershipId: '9-000-000',
        validFrom: 'OCT 2024',
        validUntil: 'SEP 2025',
        accessLabel: 'ACCESO ESTADIO ARISTOCLES "TOCKO" CASTILLO',
        crestLabel: 'VUFC',
        qrImageUrl:
            'https://images.unsplash.com/photo-1601597111158-2fceff292cdc?auto=format&fit=crop&w=900&q=80',
    },
    benefits: [
        {
            id: 1,
            icon: 'confirmation_number',
            title: 'Acceso Preferencial',
            description: 'Entrada rapida al estadio en todos los partidos de local.',
        },
        {
            id: 2,
            icon: 'shopping_bag',
            title: '10% Descuento',
            description: 'En tiendas oficiales y mercancia seleccionada del club.',
        },
        {
            id: 3,
            icon: 'event_available',
            title: 'Preventa Exclusiva',
            description: 'Prioridad de compra para boletos de liguilla y clasicos.',
        },
        {
            id: 4,
            icon: 'redeem',
            title: 'Kit de Bienvenida',
            description: 'Recibe tu experiencia inicial como Socio Indio de la temporada.',
        },
        {
            id: 5,
            icon: 'handshake',
            title: 'Aliados del Club',
            description: 'Acceso a descuentos especiales en comercios aliados.',
        },
    ],
    nextSteps: [
        { id: 1, icon: 'mail', title: 'Revisa tu correo', description: 'Encontraras instrucciones y beneficios de tu membresia.' },
        { id: 2, icon: 'download', title: 'Descarga tu carnet', description: 'Usa el acceso visual para guardar tu referencia digital mock.' },
        { id: 3, icon: 'person', title: 'Visita Mi Cuenta', description: 'La futura area de usuario centralizara historial y beneficios.' },
        { id: 4, icon: 'confirmation_number', title: 'Compra boletos', description: 'Aprovecha preventas y acceso preferencial cuando el modulo exista.' },
        { id: 5, icon: 'share', title: 'Sigue al club', description: 'Mantente conectado con noticias, resultados y promociones.' },
    ],
    actions: {
        download: { label: 'DESCARGAR CARNET', href: null },
        account: { label: 'MI CUENTA PROXIMAMENTE', href: null },
        home: { label: 'VOLVER AL INICIO', href: '/' },
    },
};

export default registrationConfirmationMock;
