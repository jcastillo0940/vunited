import { useParams } from 'react-router-dom';
import { NewsDetailLayout } from '../layouts/NewsLayout';
import { Seo } from '../seo/Seo';
import { EmptyState } from '@veraguas/ui';

export function NewsShow() {
    const { slug } = useParams<{ slug: string }>();

    if (!slug) {
        return <EmptyState title="Noticia no encontrada" />;
    }

    return (
        <>
            <Seo title="Noticia" description="Detalle de noticia del Veraguas United FC." canonicalPath={`/noticias/${slug}`} />
            <NewsDetailLayout
                title="Presentación de refuerzos para el Clausura"
                publishedAt="2026-07-01"
                body="El club oficializó la incorporación de nuevos jugadores al plantel profesional de cara al Torneo Clausura. Más detalles pronto."
            />
        </>
    );
}
