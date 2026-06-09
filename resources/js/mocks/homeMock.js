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
    exportedTalents: [
        { id: 1, name: 'Martín Morán',      position: 'Mediocentro',  club: 'Septemvri Sofia',   league: 'efbet Liga',    country: 'Bulgaria',    photoUrl: null, achievements: ['Campeón LPF Clausura', '2 Partidos Internacionales'] },
        { id: 2, name: 'Davis Contreras',   position: 'Delantero',    club: 'CDS Municipal',     league: 'Primera Cat.', country: 'Guatemala',   photoUrl: null, achievements: ['4× Campeón LPF Clausura', 'Selección U23 Panamá'] },
        { id: 3, name: 'José Córdoba',      position: 'Def. Central', club: 'Norwich City',      league: 'Championship', country: 'Inglaterra',  photoUrl: null, achievements: ['Campeón Copa Bulgaria', 'Campeón LPF Clausura'] },
        { id: 4, name: 'Joseph Rosales',    position: 'Lat. Izq.',    club: 'Minnesota United',  league: 'MLS',          country: 'EE.UU.',      photoUrl: null, achievements: ['2× Campeón LPF Clausura'] },
        { id: 5, name: 'Orman Davis',       position: 'Def. Central', club: 'Independiente CAÍ', league: 'Primera Cat.', country: 'Panamá',      photoUrl: null, achievements: ['4× Campeón LPF Clausura', 'Campeón LPF Apertura'] },
        { id: 6, name: 'Javier Betegón',    position: 'Extremo Izq.', club: 'Universitario',     league: 'Primera Cat.', country: 'Panamá',      photoUrl: null, achievements: ['Campeón LPF Clausura'] },
        { id: 7, name: 'Carlos Hernández',  position: 'Centrocampista', club: 'Racing Club B',   league: 'Primera Div.', country: 'Uruguay',     photoUrl: null, achievements: ['Experiencia en Sudamérica'] },
    ],
    standings: [
        { position: 1, club: 'VERAGUAS UNITED', played: 14, points: 32, featured: true },
        { position: 2, club: 'Tauro FC', played: 14, points: 29 },
        { position: 3, club: 'Alianza FC', played: 14, points: 26 },
        { position: 4, club: 'Plaza Amador', played: 13, points: 24 },
    ],
    shopPreview: [
        {
            id: 'jersey-local',
            title: 'Jersey Local 2024',
            subtitle: 'Navy/Sky Blue Pro',
            price: '$65.00',
            icon: 'checkroom',
        },
        {
            id: 'bufanda-gala',
            title: 'Bufanda de Gala',
            subtitle: 'Edicion Coleccionista',
            price: '$20.00',
            icon: 'sports_score',
        },
        {
            id: 'gorra-indio',
            title: 'Gorra Indio',
            subtitle: 'Snapback Oficial',
            price: '$25.00',
            icon: 'sports_baseball',
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
