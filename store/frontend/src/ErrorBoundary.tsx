import { Component, type ReactNode } from 'react';
import { Container, Button, Icon } from '@veraguas/ui';

interface Props {
    children: ReactNode;
}

interface State {
    error: Error | null;
}

export class ErrorBoundary extends Component<Props, State> {
    state: State = { error: null };

    static getDerivedStateFromError(error: Error): State {
        return { error };
    }

    componentDidCatch(error: Error) {
        console.error('[store/frontend] error no manejado:', error);
    }

    render() {
        if (this.state.error) {
            return (
                <Container className="flex min-h-screen flex-col items-center justify-center text-center">
                    <Icon name="error" size="lg" className="text-red-500" />
                    <p className="display-kicker mt-4">Error 500</p>
                    <h1 className="section-heading mt-2">Algo falló de nuestro lado</h1>
                    <Button onClick={() => window.location.reload()} className="mt-8">
                        Reintentar
                    </Button>
                </Container>
            );
        }
        return this.props.children;
    }
}
