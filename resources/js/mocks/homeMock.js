const homeMock = {
    ticker: {
        ctaLabel: 'COMPRAR ENTRADAS',
    },
    hero: {
        badge: 'PROXIMO ENCUENTRO',
        title: 'RUGE EL INDIO,',
        highlight: 'SOMOS VERAGUAS',
        description:
            'Unete a la pasion en el Estadio Atalaya. Defendamos juntos nuestra tierra en el Clausura 2024.',
        primaryAction: { label: 'COMPRAR BOLETOS', href: '/boletos' },
        secondaryAction: { label: 'HAZTE MIEMBRO', href: '/fanclub' },
        imageUrl:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuCxnv_8t9jrNX0YTvVkpfb3_dCsPoOBdjm2bDLVd8-_iqdCuY6ZZ6l4yj0Ll50xY_FGpmA9mt24_pNY71nZjwCa1-a_yB2GnUGX7VRp4Y3AqmKuCayuNnYSExt7rX-alrx0IAp9ZQY_SQjGPt8Qy-3PVH7J3aNh02bf0X-PoVloPyZ6n8STAfd2-JWLhvzPfCSsiHyKruYTXbWVYMu8iSDw2X_TjKYE-U1LIwkHz4BrsxGxUbyx53IrUpW9fJLST_8mPSjkXzxBCoor',
    },
    lastResult: {
        label: 'ULTIMO RESULTADO',
        homeCode: 'VUA',
        homeScore: '2',
        awayCode: 'TAU',
        awayScore: '1',
        note: 'Victoria en Casa',
        date: '12 OCT, 2024',
    },
    nextMatch: {
        label: 'PROXIMO PARTIDO',
        homeCode: 'HFC',
        homeName: 'Herrera',
        awayCode: 'VUA',
        awayName: 'Veraguas',
        note: 'DERBI DE PROVINCIAS',
        date: '19 OCT, 19:00',
    },
    academy: {
        title: 'CANTERA: EL FUTURO ES HOY',
        description:
            'Nuestro compromiso con el desarrollo del talento veraguense es inquebrantable. Conoce como trabajamos con nuestras categorias U-15, U-17 y U-20 para formar a los proximos idolos.',
        stats: [
            { value: '+500', label: 'Jovenes Talento' },
            { value: '12', label: 'Entrenadores Pro' },
            { value: '4', label: 'Titulos Juveniles' },
            { value: '08', label: 'Debutantes 2024' },
        ],
    },
    standings: [
        { position: 1, club: 'VERAGUAS UNITED', played: 14, points: 32, featured: true },
        { position: 2, club: 'Tauro FC', played: 14, points: 29 },
        { position: 3, club: 'Alianza FC', played: 14, points: 26 },
        { position: 4, club: 'Plaza Amador', played: 13, points: 24 },
    ],
    shopPreview: [
        {
            id: 'jersey-local',
            type: 'featured',
            title: 'Jersey Local 2024',
            subtitle: 'Navy/Sky Blue Pro',
            price: '$65.00',
            icon: 'checkroom',
        },
        {
            id: 'bufanda-gala',
            type: 'compact',
            title: 'Bufanda de Gala',
            subtitle: 'Edicion Coleccionista',
            price: '$20.00',
            icon: 'sports_score',
        },
    ],
    membership: {
        title: 'TODAVIA NO ERES DE LA TRIBU?',
        description:
            'Obten descuentos exclusivos, acceso a preventas y mucho mas siendo Fanatico Indio.',
        ctaLabel: 'QUIERO UNIRME',
    },
    partners: ['PATROCINADOR A', 'PATROCINADOR B', 'PATROCINADOR C', 'PATROCINADOR D', 'PATROCINADOR E'],
};

export default homeMock;
