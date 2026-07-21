import { describe, expect, it } from 'vitest';
import { render, screen } from '@testing-library/react';
import { MemoryRouter, Routes, Route } from 'react-router-dom';
import { RouteShell } from '../../layouts/RouteShell';
import { Home } from '../Home';
import { NotFound } from '../NotFound';

function renderAt(path: string) {
    return render(
        <MemoryRouter initialEntries={[path]}>
            <Routes>
                <Route element={<RouteShell />}>
                    <Route path="/" element={<Home />} />
                    <Route path="*" element={<NotFound />} />
                </Route>
            </Routes>
        </MemoryRouter>,
    );
}

describe('routing', () => {
    it('renders the homepage hero at /', () => {
        renderAt('/');
        expect(screen.getByRole('heading', { level: 1 })).toHaveTextContent('RUGE EL INDIO');
    });

    it('renders NotFound for an unknown path', () => {
        renderAt('/esta-ruta-no-existe');
        expect(screen.getByText('Esta página no existe')).toBeInTheDocument();
    });

    it('marks the active header link for the current route', () => {
        renderAt('/');
        const activeLink = screen.getByRole('link', { name: 'Inicio' });
        expect(activeLink).toHaveAttribute('aria-current', 'page');
    });
});
