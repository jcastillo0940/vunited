import { Container, Button } from '@veraguas/ui';
import { Seo } from '../seo/Seo';

export function NotFound() {
    return (
        <>
            <Seo title="Página no encontrada" description="La página que buscas no existe." canonicalPath="/404" noIndex />
            <Container className="flex min-h-[60vh] flex-col items-center justify-center text-center">
                <p className="display-kicker">Error 404</p>
                <h1 className="section-heading mt-2">Esta página no existe</h1>
                <p className="mt-4 max-w-md text-text-main/70">
                    Puede que el enlace esté roto o que la página se haya movido.
                </p>
                <Button as="a" href="/" className="mt-8">
                    Volver al inicio
                </Button>
            </Container>
        </>
    );
}
