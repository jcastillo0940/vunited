import { useParams } from 'react-router-dom';
import { Container, EmptyState } from '@veraguas/ui';

export function Category() {
    const { slug } = useParams<{ slug: string }>();
    return (
        <Container className="section-space">
            <p className="display-kicker mb-2">Categoría</p>
            <h1 className="section-heading mb-10 capitalize">{slug?.replace(/-/g, ' ')}</h1>
            <EmptyState
                icon="category"
                title="Sin productos todavía"
                message="Esta categoría se conectará al catálogo real del backend de Store."
            />
        </Container>
    );
}
