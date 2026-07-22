import { describe, expect, it } from 'vitest';
import { render, screen } from '@testing-library/react';
import { MemoryRouter, Routes, Route } from 'react-router-dom';
import { RouteShell } from '../../layouts/RouteShell';
import { OrderFlowProvider } from '../../context/OrderFlowContext';
import { CustomerAuthProvider } from '../../context/CustomerAuthContext';
import { Events } from '../Events';
import { Wallet } from '../Wallet';
import { NotFound } from '../NotFound';

function renderAt(path: string) {
    return render(
        <CustomerAuthProvider>
            <OrderFlowProvider>
                <MemoryRouter initialEntries={[path]}>
                    <Routes>
                        <Route element={<RouteShell />}>
                            <Route path="/" element={<Events />} />
                            <Route path="/wallet" element={<Wallet />} />
                            <Route path="*" element={<NotFound />} />
                        </Route>
                    </Routes>
                </MemoryRouter>
            </OrderFlowProvider>
        </CustomerAuthProvider>,
    );
}

describe('ticketing routing', () => {
    it('renders upcoming events at /', () => {
        renderAt('/');
        expect(screen.getByRole('heading', { name: 'Próximos eventos' })).toBeInTheDocument();
    });

    it('renders the empty wallet state at /wallet when there is no active order', () => {
        renderAt('/wallet');
        expect(screen.getByRole('heading', { name: 'Mi wallet' })).toBeInTheDocument();
        expect(screen.getByText('Todavía no tienes entradas')).toBeInTheDocument();
    });

    it('renders NotFound for an unknown path', () => {
        renderAt('/no-existe');
        expect(screen.getByText('Esta página no existe')).toBeInTheDocument();
    });
});
