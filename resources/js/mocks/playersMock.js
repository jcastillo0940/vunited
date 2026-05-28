function createPlayerProfile(overrides = {}) {
    return {
        team: 'Veraguas United FC',
        age: 26,
        height: '1.84 m',
        dominantFoot: 'Derecho',
        biography:
            'Originario de Santiago de Veraguas, este futbolista representa la intensidad competitiva del club con disciplina, ambicion y una conexion real con la aficion.',
        stats: [
            { key: 'matches', label: 'Partidos Jugados', value: '18', tone: 'primary' },
            { key: 'goals', label: 'Goles Anotados', value: '12', tone: 'accent' },
            { key: 'assists', label: 'Asistencias', value: '05', tone: 'primary' },
            { key: 'minutes', label: 'Minutos', value: '1,542', tone: 'neutral' },
        ],
        attributes: [
            { key: 'speed', label: 'Velocidad', value: 94 },
            { key: 'finishing', label: 'Finalizacion', value: 89 },
            { key: 'stamina', label: 'Resistencia', value: 82 },
            { key: 'dribbling', label: 'Regate', value: 87 },
        ],
        gallery: [
            {
                id: 1,
                type: 'image',
                label: 'Celebracion',
                imageUrl:
                    'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=900&q=80',
            },
            {
                id: 2,
                type: 'video',
                label: 'Training',
                imageUrl:
                    'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=80',
            },
            {
                id: 3,
                type: 'image',
                label: 'Action Header',
                imageUrl:
                    'https://images.unsplash.com/photo-1570498839593-e565b39455fc?auto=format&fit=crop&w=900&q=80',
            },
            {
                id: 4,
                type: 'image',
                label: 'Fans',
                imageUrl:
                    'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=900&q=80',
            },
        ],
        socialActions: [
            { id: 'share', icon: 'share', label: 'Compartir' },
            { id: 'favorite', icon: 'favorite', label: 'Favorito' },
        ],
        ...overrides,
    };
}

function createPlayer(player) {
    return {
        ...player,
        profile: createPlayerProfile(player.profile),
    };
}

const playersMock = {
    hero: {
        eyebrow: 'Temporada 2024',
        title: 'NUESTRA',
        highlight: 'PLANTILLA',
        description:
            'Talento, disciplina y caracter competitivo en cada linea. Conoce a los jugadores y al cuerpo tecnico que representan el orgullo de Veraguas.',
        imageUrl:
            'https://images.unsplash.com/photo-1547347298-4074fc3086f0?auto=format&fit=crop&w=1600&q=80',
    },
    squadFilters: [
        { id: 'first-team', label: 'Primer Equipo (LPF)' },
        { id: 'women-team', label: 'Equipo Femenino (LFF)' },
        { id: 'academy', label: 'Cantera' },
        { id: 'staff', label: 'Cuerpo tecnico' },
    ],
    positionFilters: [
        { id: 'all', label: 'Todos' },
        { id: 'goalkeeper', label: 'Porteros' },
        { id: 'defender', label: 'Defensas' },
        { id: 'midfielder', label: 'Volantes' },
        { id: 'forward', label: 'Delanteros' },
    ],
    squads: [
        {
            id: 'first-team',
            label: 'Primer Equipo (LPF)',
            players: [
                createPlayer({
                    id: 1,
                    slug: 'marcos-allen',
                    name: 'Marcos Allen',
                    firstName: 'Marcos',
                    lastName: 'Allen',
                    position: 'Portero',
                    positionKey: 'goalkeeper',
                    number: '01',
                    nationality: 'Panama',
                    meta: 'Panama • Primer equipo',
                    imageUrl:
                        'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=900&q=80',
                    profile: {
                        age: 28,
                        height: '1.89 m',
                        biography:
                            'Arquero de grandes reflejos y liderazgo silencioso, Marcos organiza la ultima linea y transmite seguridad en cada salida.',
                        stats: [
                            { key: 'matches', label: 'Partidos Jugados', value: '20', tone: 'primary' },
                            { key: 'cleanSheets', label: 'Vallas Invictas', value: '08', tone: 'accent' },
                            { key: 'saves', label: 'Atajadas', value: '54', tone: 'primary' },
                            { key: 'minutes', label: 'Minutos', value: '1,800', tone: 'neutral' },
                        ],
                        attributes: [
                            { key: 'reflexes', label: 'Reflejos', value: 91 },
                            { key: 'positioning', label: 'Colocacion', value: 87 },
                            { key: 'distribution', label: 'Salida', value: 80 },
                            { key: 'stamina', label: 'Resistencia', value: 78 },
                        ],
                    },
                }),
                createPlayer({
                    id: 2,
                    slug: 'luis-torres',
                    name: 'Luis Torres',
                    firstName: 'Luis',
                    lastName: 'Torres',
                    position: 'Defensa',
                    positionKey: 'defender',
                    number: '04',
                    nationality: 'Panama',
                    meta: 'Panama • Primer equipo',
                    imageUrl:
                        'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 3,
                    slug: 'javier-guerra',
                    name: 'Javier Guerra',
                    firstName: 'Javier',
                    lastName: 'Guerra',
                    position: 'Volante',
                    positionKey: 'midfielder',
                    number: '10',
                    nationality: 'Panama',
                    meta: 'Panama • Capitan',
                    imageUrl:
                        'https://images.unsplash.com/photo-1570498839593-e565b39455fc?auto=format&fit=crop&w=900&q=80',
                    profile: {
                        age: 29,
                        height: '1.78 m',
                        biography:
                            'Capitan del mediocampo indio. Javier marca el ritmo del juego y conecta la intensidad del club con claridad en cada posesion.',
                        stats: [
                            { key: 'matches', label: 'Partidos Jugados', value: '19', tone: 'primary' },
                            { key: 'goals', label: 'Goles Anotados', value: '06', tone: 'accent' },
                            { key: 'assists', label: 'Asistencias', value: '09', tone: 'primary' },
                            { key: 'minutes', label: 'Minutos', value: '1,691', tone: 'neutral' },
                        ],
                        attributes: [
                            { key: 'vision', label: 'Vision', value: 92 },
                            { key: 'passing', label: 'Pase', value: 90 },
                            { key: 'stamina', label: 'Resistencia', value: 86 },
                            { key: 'dribbling', label: 'Regate', value: 84 },
                        ],
                    },
                }),
                createPlayer({
                    id: 4,
                    slug: 'alexis-canto',
                    name: 'Alexis Canto',
                    firstName: 'Alexis',
                    lastName: 'Canto',
                    position: 'Delantero',
                    positionKey: 'forward',
                    number: '09',
                    nationality: 'Panama',
                    meta: 'Panama • Goleador',
                    imageUrl:
                        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80',
                    profile: {
                        age: 26,
                        height: '1.84 m',
                        biography:
                            'Delantero explosivo con olfato goleador y hambre competitiva. Alexis castiga espacios cortos y largos con la misma determinacion.',
                        stats: [
                            { key: 'matches', label: 'Partidos Jugados', value: '18', tone: 'primary' },
                            { key: 'goals', label: 'Goles Anotados', value: '12', tone: 'accent' },
                            { key: 'assists', label: 'Asistencias', value: '05', tone: 'primary' },
                            { key: 'minutes', label: 'Minutos', value: '1,542', tone: 'neutral' },
                        ],
                        attributes: [
                            { key: 'speed', label: 'Velocidad', value: 94 },
                            { key: 'finishing', label: 'Finalizacion', value: 89 },
                            { key: 'stamina', label: 'Resistencia', value: 82 },
                            { key: 'dribbling', label: 'Regate', value: 87 },
                        ],
                    },
                }),
                createPlayer({
                    id: 5,
                    slug: 'andres-batista',
                    name: 'Andres Batista',
                    firstName: 'Andres',
                    lastName: 'Batista',
                    position: 'Volante',
                    positionKey: 'midfielder',
                    number: '08',
                    nationality: 'Panama',
                    meta: 'Panama • Formacion club',
                    imageUrl:
                        'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 6,
                    slug: 'jose-murillo',
                    name: 'Jose Murillo',
                    firstName: 'Jose',
                    lastName: 'Murillo',
                    position: 'Delantero',
                    positionKey: 'forward',
                    number: '19',
                    nationality: 'Panama',
                    meta: 'Panama • Presion alta',
                    imageUrl:
                        'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 7,
                    slug: 'kevin-solis',
                    name: 'Kevin Solis',
                    firstName: 'Kevin',
                    lastName: 'Solis',
                    position: 'Defensa',
                    positionKey: 'defender',
                    number: '14',
                    nationality: 'Panama',
                    meta: 'Panama • Banda derecha',
                    imageUrl:
                        'https://images.unsplash.com/photo-1504257432389-52343af06ae3?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 8,
                    slug: 'ronald-aguirre',
                    name: 'Ronald Aguirre',
                    firstName: 'Ronald',
                    lastName: 'Aguirre',
                    position: 'Portero',
                    positionKey: 'goalkeeper',
                    number: '12',
                    nationality: 'Panama',
                    meta: 'Panama • Relevo seguro',
                    imageUrl:
                        'https://images.unsplash.com/photo-1506795660185-6697f2a4d4dd?auto=format&fit=crop&w=900&q=80',
                }),
            ],
        },
        {
            id: 'women-team',
            label: 'Equipo Femenino (LFF)',
            players: [
                createPlayer({
                    id: 9,
                    slug: 'melissa-castillo',
                    name: 'Melissa Castillo',
                    firstName: 'Melissa',
                    lastName: 'Castillo',
                    position: 'Volante',
                    positionKey: 'midfielder',
                    number: '07',
                    nationality: 'Panama',
                    meta: 'Panama • Equipo femenino',
                    imageUrl:
                        'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 10,
                    slug: 'natalia-vega',
                    name: 'Natalia Vega',
                    firstName: 'Natalia',
                    lastName: 'Vega',
                    position: 'Delantera',
                    positionKey: 'forward',
                    number: '11',
                    nationality: 'Panama',
                    meta: 'Panama • Equipo femenino',
                    imageUrl:
                        'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 11,
                    slug: 'camila-soto',
                    name: 'Camila Soto',
                    firstName: 'Camila',
                    lastName: 'Soto',
                    position: 'Defensa',
                    positionKey: 'defender',
                    number: '03',
                    nationality: 'Panama',
                    meta: 'Panama • Equipo femenino',
                    imageUrl:
                        'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 12,
                    slug: 'daniela-ruiz',
                    name: 'Daniela Ruiz',
                    firstName: 'Daniela',
                    lastName: 'Ruiz',
                    position: 'Portera',
                    positionKey: 'goalkeeper',
                    number: '01',
                    nationality: 'Panama',
                    meta: 'Panama • Equipo femenino',
                    imageUrl:
                        'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=900&q=80',
                }),
            ],
        },
        {
            id: 'academy',
            label: 'Cantera',
            players: [
                createPlayer({
                    id: 13,
                    slug: 'isaac-campos',
                    name: 'Isaac Campos',
                    firstName: 'Isaac',
                    lastName: 'Campos',
                    position: 'Volante',
                    positionKey: 'midfielder',
                    number: '16',
                    nationality: 'Panama',
                    meta: 'Panama • Proyeccion cantera',
                    imageUrl:
                        'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 14,
                    slug: 'mateo-gomez',
                    name: 'Mateo Gomez',
                    firstName: 'Mateo',
                    lastName: 'Gomez',
                    position: 'Defensa',
                    positionKey: 'defender',
                    number: '05',
                    nationality: 'Panama',
                    meta: 'Panama • Proyeccion cantera',
                    imageUrl:
                        'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 15,
                    slug: 'sebastian-rojas',
                    name: 'Sebastian Rojas',
                    firstName: 'Sebastian',
                    lastName: 'Rojas',
                    position: 'Delantero',
                    positionKey: 'forward',
                    number: '18',
                    nationality: 'Panama',
                    meta: 'Panama • Proyeccion cantera',
                    imageUrl:
                        'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=900&q=80',
                }),
                createPlayer({
                    id: 16,
                    slug: 'daniel-pinzon',
                    name: 'Daniel Pinzon',
                    firstName: 'Daniel',
                    lastName: 'Pinzon',
                    position: 'Portero',
                    positionKey: 'goalkeeper',
                    number: '13',
                    nationality: 'Panama',
                    meta: 'Panama • Proyeccion cantera',
                    imageUrl:
                        'https://images.unsplash.com/photo-1504257432389-52343af06ae3?auto=format&fit=crop&w=900&q=80',
                }),
            ],
        },
    ],
    staff: {
        featured: {
            id: 101,
            name: 'Gonzalo Mendez',
            firstName: 'Gonzalo',
            lastName: 'Mendez',
            role: 'Director Tecnico',
            description:
                'Liderando la vision tactica de los Indios con mas de 15 anos de experiencia internacional. Un estratega forjado en la disciplina y la victoria.',
            imageUrl:
                'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=900&q=80',
        },
        assistants: [
            {
                id: 102,
                name: 'Ricardo Vega',
                role: 'Asistente Tecnico',
                imageUrl:
                    'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=700&q=80',
            },
            {
                id: 103,
                name: 'Marco Calderon',
                role: 'Preparador Fisico',
                imageUrl:
                    'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=700&q=80',
            },
            {
                id: 104,
                name: 'Ivan Moreno',
                role: 'Analista de Rendimiento',
                imageUrl:
                    'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=700&q=80',
            },
        ],
    },
};

export function getAllPlayers() {
    return playersMock.squads.flatMap((squad) => squad.players);
}

export function getPlayerBySlug(slug) {
    return getAllPlayers().find((player) => player.slug === slug) ?? null;
}

export default playersMock;
