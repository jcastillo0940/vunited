const boardMock = {
    hero: {
        badge: 'ESTRUCTURA CORPORATIVA',
        title: 'LIDERAZGO',
        highlight: 'Y VISION',
        description:
            'Comprometidos con el desarrollo integral del futbol en la provincia de Veraguas. Nuestra directiva trabaja bajo los pilares de la excelencia deportiva, la sostenibilidad financiera y el impacto social en nuestra comunidad.',
        imageUrl:
            'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=1600&q=80',
    },
    president: {
        name: 'Ing. Ricardo Mendez',
        role: 'Presidencia',
        title: 'Presidente Ejecutivo',
        message:
            'Con mas de 20 anos de experiencia en gestion empresarial y una pasion inquebrantable por el deporte veraguense, lidera el proyecto United con la conviccion de profesionalizar cada area del club. Bajo su gestion, Veraguas United ha alcanzado hitos clave en infraestructura, cantera y sostenibilidad institucional.',
        imageUrl:
            'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=1100&q=80',
        primaryAction: { label: 'Ver trayectoria', href: null },
        socialActions: [
            { id: 'share', label: 'Compartir', icon: 'share', href: null },
            { id: 'mail', label: 'Correo', icon: 'alternate_email', href: null },
        ],
    },
    executives: [
        {
            id: 1,
            name: 'Carlos Villarreal',
            role: 'Vicepresidente',
            area: 'Operaciones y Logistica',
            description:
                'Coordina estructura operativa, matchday, viajes y relacion institucional con sedes y proveedores.',
            imageUrl:
                'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80',
            tone: 'primary',
            icons: ['groups', 'mail'],
        },
        {
            id: 2,
            name: 'Manuel Batista',
            role: 'Director Deportivo',
            area: 'Gestion de Talento',
            description:
                'Lidera scouting, metodologia competitiva y la conexion entre primer equipo y semillero indio.',
            imageUrl:
                'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=900&q=80',
            tone: 'accent',
            icons: ['sports_soccer', 'mail'],
        },
        {
            id: 3,
            name: 'Dra. Elena Ruiz',
            role: 'Gerente General',
            area: 'Administracion Central',
            description:
                'Supervisa gestion financiera, cumplimiento, gobierno corporativo y proyeccion institucional.',
            imageUrl:
                'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=900&q=80',
            tone: 'primary',
            icons: ['description', 'mail'],
        },
    ],
    members: [
        { id: 1, name: 'J. Santamaria', role: 'Vocal Principal', category: 'Junta Directiva' },
        { id: 2, name: 'L. de Gracia', role: 'Secretario', category: 'Junta Directiva' },
        { id: 3, name: 'M. Castillo', role: 'Tesorero', category: 'Junta Directiva' },
        { id: 4, name: 'Corporacion Veraguas', role: 'Socio Estrategico', category: 'Accionistas' },
        { id: 5, name: 'F. Espinoza', role: 'Asesor Legal', category: 'Gobernanza' },
        { id: 6, name: 'H. Jimenez', role: 'Marketing', category: 'Apoyo Ejecutivo' },
        { id: 7, name: 'G. Pitti', role: 'Vocal', category: 'Junta Directiva' },
        { id: 8, name: 'R. Vega', role: 'Vocal', category: 'Junta Directiva' },
    ],
    transparency: {
        title: 'COMPROMISO ETICO Y TRANSPARENCIA',
        description:
            'En Veraguas United FC, la integridad es nuestra mayor divisa. Mantenemos procesos de auditoria abierta y reportes de gestion trimestrales para asegurar que cada recurso sea invertido en el fortalecimiento de nuestra identidad y futuro deportivo.',
        action: {
            label: 'PORTAL DE TRANSPARENCIA',
            href: null,
        },
    },
};

export default boardMock;
