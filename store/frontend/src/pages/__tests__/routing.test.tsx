import { describe, expect, it } from 'vitest';
import { render, screen } from '@testing-library/react';
import { MemoryRouter, Routes, Route } from 'react-router-dom';
import { RouteShell } from '../../layouts/RouteShell';
import { Catalog } from '../Catalog';
import { Cart } from '../Cart';
import { NotFound } from '../NotFound';

function renderAt(path: string) {
    return render(
        <MemoryRouter initialEntries={[path]}>
            <Routes>
                <Route element={<RouteShell />}>
                    <Route path="/" element={<Catalog />} />
                    <Route path="/carrito" element={<Cart />} />
                    <Route path="*" element={<NotFound />} />
                </Route>
            </Routes>
        </MemoryRouter>,
    );
}

describe('store routing', () => {
    it('renders the catalog at /', () => {
        renderAt('/');
        expect(screen.getByRole('heading', { name: 'Catálogo' })).toBeInTheDocument();
    });

    it('shows the empty cart state at /carrito', () => {
        renderAt('/carrito');
        expect(screen.getByText('Tu carrito está vacío')).toBeInTheDocument();
    });

    it('renders NotFound for an unknown path', () => {
        renderAt('/no-existe');
        expect(screen.getByText('Esta página no existe')).toBeInTheDocument();
    });
});
