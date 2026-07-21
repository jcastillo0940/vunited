import { Container, Button } from '@veraguas/ui';
import { Seo } from '../seo/Seo';

export function ServerError() {
    return (
        <>
            <Seo title="Error del servidor" description="Ocurrió un error inesperado." canonicalPath="/500" noIndex />
            <Container className="flex min-h-[60vh] flex-col items-center justify-center text-center">
                <p className="display-kicker">Error 500</p>
                <h1 className="section-heading mt-2">Algo falló de nuestro lado</h1>
                <p className="mt-4 max-w-md text-text-main/70">
                    Ya estamos al tanto. Intenta de nuevo en unos minutos.
                </p>
                <Button onClick={() => window.location.reload()} className="mt-8">
                    Reintentar
                </Button>
            </Container>
        </>
    );
}
