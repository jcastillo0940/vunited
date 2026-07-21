import { useLocation } from 'react-router-dom';
import { InstitutionalLayout } from '../layouts/InstitutionalLayout';
import { Seo } from '../seo/Seo';
import { EmptyState } from '@veraguas/ui';

const PAGES: Record<string, { kicker: string; title: string; description: string }> = {
    '/directiva': { kicker: 'El Club', title: 'Directiva', description: 'Junta directiva del Veraguas United FC.' },
    '/plantilla': { kicker: 'El Club', title: 'Plantilla', description: 'Jugadores del primer equipo.' },
    '/fuerzas-basicas': { kicker: 'El Club', title: 'Fuerzas Básicas', description: 'Categorías formativas U-15, U-17 y U-20.' },
    '/pruebas': { kicker: 'El Club', title: 'Pruebas', description: 'Convocatorias abiertas para nuevos talentos.' },
    '/estadio': { kicker: 'El Club', title: 'Estadio Atalaya', description: 'La casa del Veraguas United FC.' },
    '/patrocinadores': { kicker: 'El Club', title: 'Patrocinadores', description: 'Marcas que apoyan al club.' },
    '/fanfest': { kicker: 'Comunidad', title: 'FanFest', description: 'Actividades para la afición antes de cada partido.' },
    '/expedicion-india': { kicker: 'Comunidad', title: 'Expedición India', description: 'La barra oficial en cada salida.' },
};

/** Shell compartido para las páginas institucionales listadas en la navegación. */
export function InstitutionalPage() {
    const { pathname } = useLocation();
    const page = PAGES[pathname];

    if (!page) {
        return <EmptyState title="Página no encontrada" />;
    }

    return (
        <>
            <Seo title={page.title} description={page.description} canonicalPath={pathname} />
            <InstitutionalLayout kicker={page.kicker} title={page.title} description={page.description}>
                <p className="text-text-main/70">
                    Contenido detallado de "{page.title}" pendiente de conectar a la API real del dominio Web
                    (CMS). Este shell reproduce el layout institucional del sitio actual.
                </p>
            </InstitutionalLayout>
        </>
    );
}
