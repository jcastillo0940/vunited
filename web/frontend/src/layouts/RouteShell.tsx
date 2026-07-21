import { Outlet, useLocation } from 'react-router-dom';
import { AppShell } from './AppShell';

/** Conecta la ruta activa (react-router) con AppShell para marcar el link activo. */
export function RouteShell() {
    const { pathname } = useLocation();
    return (
        <AppShell activeUrl={pathname}>
            <Outlet />
        </AppShell>
    );
}
