import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
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

beforeEach(() => {
    vi.stubGlobal(
        'fetch',
        vi.fn(async (input: RequestInfo | URL) => {
            const url = String(input);
            if (url.includes('/products')) {
                return new Response(JSON.stringify({ data: [] }), { status: 200 });
            }
            if (url.includes('/cart')) {
                return new Response(
                    JSON.stringify({ id: 1, token: 'test-token', currency: 'CRC', expires_at: '', items: [] }),
                    { status: 200 },
                );
            }

            return new Response('{}', { status: 200 });
        }),
    );
});

afterEach(() => {
    vi.unstubAllGlobals();
});

describe('store routing', () => {
    it('renders the catalog at /', () => {
        renderAt('/');
        expect(screen.getByRole('heading', { name: 'Catálogo' })).toBeInTheDocument();
    });

    it('shows the empty cart state at /carrito', async () => {
        renderAt('/carrito');
        expect(await screen.findByText('Tu carrito está vacío')).toBeInTheDocument();
    });

    it('renders NotFound for an unknown path', () => {
        renderAt('/no-existe');
        expect(screen.getByText('Esta página no existe')).toBeInTheDocument();
    });
});
