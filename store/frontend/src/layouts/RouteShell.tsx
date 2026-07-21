import { Outlet, useLocation } from 'react-router-dom';
import { StoreShell } from './StoreShell';

export function RouteShell() {
    const { pathname } = useLocation();
    return (
        <StoreShell activeUrl={pathname}>
            <Outlet />
        </StoreShell>
    );
}
