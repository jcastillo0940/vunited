import type { ReactNode } from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { LoadingState } from '@veraguas/ui';
import { useCustomerAuth } from '../context/CustomerAuthContext';

export function RequireAuth({ children }: { children: ReactNode }) {
    const { customer, loading } = useCustomerAuth();
    const location = useLocation();

    if (loading) return <LoadingState label="Verificando tu sesión…" />;

    if (!customer) {
        return <Navigate to="/ingresar" state={{ from: location.pathname }} replace />;
    }

    return <>{children}</>;
}
