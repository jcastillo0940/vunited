import { Outlet, useLocation } from 'react-router-dom';
import { TicketingShell } from './TicketingShell';

export function RouteShell() {
    const { pathname } = useLocation();
    return (
        <TicketingShell activeUrl={pathname}>
            <Outlet />
        </TicketingShell>
    );
}
