import { Component, type ErrorInfo, type ReactNode } from 'react';
import { ServerError } from './pages/ServerError';

interface Props {
    children: ReactNode;
}

interface State {
    error: Error | null;
}

/** Atrapa errores de render no manejados y muestra ErrorState con marca. */
export class ErrorBoundary extends Component<Props, State> {
    state: State = { error: null };

    static getDerivedStateFromError(error: Error): State {
        return { error };
    }

    componentDidCatch(error: Error, info: ErrorInfo) {
        console.error('[web/frontend] error no manejado:', error, info.componentStack);
    }

    render() {
        if (this.state.error) {
            return <ServerError />;
        }
        return this.props.children;
    }
}
