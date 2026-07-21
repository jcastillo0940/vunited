import { useParams } from 'react-router-dom';
import { Container, Card, Button } from '@veraguas/ui';

export function MatchDetail() {
    const { id } = useParams<{ id: string }>();
    return (
        <Container className="section-space max-w-2xl">
            <p className="display-kicker mb-2">Evento #{id}</p>
            <h1 className="section-heading mb-8">Veraguas United vs Herrera FC</h1>
            <Card className="flex flex-col gap-4">
                <p className="text-text-main/70">19 de octubre, 19:00 · Estadio Atalaya</p>
                <p className="text-text-main/70">Derbi de provincias. Aforo limitado.</p>
                <Button as="a" href="/zona" size="lg" className="w-fit">
                    Elegir zona
                </Button>
            </Card>
        </Container>
    );
}
