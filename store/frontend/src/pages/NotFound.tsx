import { Container, Button } from '@veraguas/ui';

export function NotFound() {
    return (
        <Container className="flex min-h-[60vh] flex-col items-center justify-center text-center">
            <p className="display-kicker">Error 404</p>
            <h1 className="section-heading mt-2">Esta página no existe</h1>
            <Button as="a" href="/" className="mt-8">
                Ir al catálogo
            </Button>
        </Container>
    );
}
