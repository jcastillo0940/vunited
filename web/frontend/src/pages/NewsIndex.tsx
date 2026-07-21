import { NewsListLayout, type NewsSummary } from '../layouts/NewsLayout';
import { Seo } from '../seo/Seo';

// Datos de muestra: la API real de Noticias (dominio Web) llega en una fase
// posterior de implementación de backend; este shell prueba el layout.
const MOCK_NEWS: NewsSummary[] = [
    {
        slug: 'presentacion-refuerzos',
        title: 'Presentación de refuerzos para el Clausura',
        excerpt: 'El club oficializó la incorporación de tres jugadores para reforzar el plantel.',
        publishedAt: '2026-07-01',
    },
    {
        slug: 'agenda-fanfest',
        title: 'Agenda del próximo FanFest',
        excerpt: 'Actividades familiares antes del derbi de provincias en el Estadio Atalaya.',
        publishedAt: '2026-06-20',
    },
];

export function NewsIndex() {
    return (
        <>
            <Seo title="Noticias" description="Últimas noticias del Veraguas United FC." canonicalPath="/noticias" />
            <NewsListLayout items={MOCK_NEWS} />
        </>
    );
}
