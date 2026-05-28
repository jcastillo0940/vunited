const academyMock = {
    hero: {
        title: 'SEMILLERO INDIO:',
        highlight: 'EL FUTURO DE VERAGUAS',
        description:
            'Nuestra mision es forjar el caracter y el talento de los jovenes futbolistas de la provincia. En Veraguas United, la cantera no es solo un equipo, es el corazon de nuestra identidad competitiva.',
        imageUrl:
            'https://images.unsplash.com/photo-1517927033932-b3d18e61fb3a?auto=format&fit=crop&w=1600&q=80',
        ctaLabel: 'INSCRIBIRSE EN LAS PRUEBAS',
        ctaHref: '/pruebas',
    },
    intro: {
        eyebrow: 'Desarrollo con identidad',
        title: 'FORMAMOS TALENTO VERAGUENSE',
        description:
            'Desarrollamos futbolistas con metodologia, disciplina y acompanamiento humano para que cada categoria juvenil llegue preparada al siguiente nivel.',
        imageUrl:
            'https://images.unsplash.com/photo-1526232761682-d26e03ac148e?auto=format&fit=crop&w=1200&q=80',
    },
    categories: [
        {
            id: 'sub-21',
            label: 'SUB-21 (LIGA PROM)',
            name: 'Sub-21',
            ageRange: '2005 - 2007',
            description:
                'Ultimo escalon antes del profesionalismo, con enfoque competitivo y lectura tactica avanzada.',
            icon: 'military_tech',
            statLabel: 'Jugadores',
            statValue: '24',
            featured: true,
            players: [
                {
                    slug: 'alexis-canto',
                    name: 'Juan Castro',
                    number: '09',
                    position: 'Delantero',
                    imageUrl:
                        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80',
                    stats: [
                        { label: 'PJ', value: '14' },
                        { label: 'GOLES', value: '11' },
                        { label: 'MINS', value: "1,200'" },
                    ],
                },
                {
                    slug: 'ronald-aguirre',
                    name: 'Luis Vega',
                    number: '01',
                    position: 'Portero',
                    imageUrl:
                        'https://images.unsplash.com/photo-1506795660185-6697f2a4d4dd?auto=format&fit=crop&w=900&q=80',
                    stats: [
                        { label: 'PJ', value: '15' },
                        { label: 'VALLA', value: '06' },
                        { label: 'MINS', value: "1,350'" },
                    ],
                },
                {
                    slug: 'javier-guerra',
                    name: 'Mateo Ruiz',
                    number: '08',
                    position: 'Volante',
                    imageUrl:
                        'https://images.unsplash.com/photo-1570498839593-e565b39455fc?auto=format&fit=crop&w=900&q=80',
                    stats: [
                        { label: 'PJ', value: '12' },
                        { label: 'ASIST', value: '07' },
                        { label: 'MINS', value: "980'" },
                    ],
                },
                {
                    slug: 'luis-torres',
                    name: 'Diego Arias',
                    number: '04',
                    position: 'Defensa',
                    imageUrl:
                        'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=80',
                    stats: [
                        { label: 'PJ', value: '15' },
                        { label: 'G/C', value: '04' },
                        { label: 'MINS', value: "1,350'" },
                    ],
                },
            ],
        },
        {
            id: 'sub-17',
            label: 'SUB-17',
            name: 'Sub-17',
            ageRange: '2008 - 2009',
            description:
                'Desarrollo integral con foco en fundamentos tecnicos, transiciones y toma de decisiones.',
            icon: 'sports_soccer',
            statLabel: 'Jugadores',
            statValue: '28',
        },
        {
            id: 'sub-15',
            label: 'SUB-15',
            name: 'Sub-15',
            ageRange: '2010 - 2012',
            description:
                'Base metodologica para consolidar tecnica, valores del club y preparacion competitiva.',
            icon: 'school',
            statLabel: 'Jugadores',
            statValue: '32',
        },
    ],
    impactStats: [
        {
            id: 'selected',
            icon: 'flag',
            value: '12',
            label: 'Jugadores en Seleccion Nacional',
        },
        {
            id: 'debutants',
            icon: 'rocket_launch',
            value: '08',
            label: 'Debutantes en Primer Equipo',
        },
        {
            id: 'titles',
            icon: 'emoji_events',
            value: '05',
            label: 'Titulos de Cantera',
        },
        {
            id: 'coaches',
            icon: 'groups',
            value: '14',
            label: 'Entrenadores Formadores',
        },
    ],
    process: [
        {
            id: 1,
            icon: 'conversion_path',
            title: 'Metodologia',
            description:
                'Plan de formacion por etapas, seguimiento tecnico y evolucion competitiva por categoria.',
        },
        {
            id: 2,
            icon: 'workspace_premium',
            title: 'Disciplina',
            description:
                'Rutinas, orden tactico y comportamiento profesional desde los primeros ciclos de cantera.',
        },
        {
            id: 3,
            icon: 'trending_up',
            title: 'Proyeccion Profesional',
            description:
                'Ruta clara hacia Liga Prom, primer equipo y vitrina nacional para el talento de Veraguas.',
        },
        {
            id: 4,
            icon: 'favorite',
            title: 'Valores del Club',
            description:
                'Sentido de pertenencia, trabajo colectivo y orgullo por representar a la provincia.',
        },
    ],
    finalCta: {
        title: '¿TIENES LO QUE SE NECESITA?',
        description:
            'Buscamos talentos nacidos entre 2005 y 2012 para unirse a nuestras filas. El camino al profesionalismo comienza aqui.',
        primaryLabel: 'INSCRIBIRSE EN LAS PRUEBAS',
        primaryHref: '/pruebas',
        secondaryLabel: 'MAS INFORMACION',
        secondaryHref: '/pagina/cantera',
        imageUrl:
            'https://images.unsplash.com/photo-1518604666860-9ed391f76460?auto=format&fit=crop&w=1200&q=80',
    },
};

export default academyMock;
