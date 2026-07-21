import { describe, expect, it } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import { Dashboard } from '../pages/Dashboard';
import { UsersList } from '../pages/UsersList';

describe('admin shell', () => {
    it('renders the dashboard with sidebar navigation', () => {
        render(
            <MemoryRouter>
                <Dashboard />
            </MemoryRouter>,
        );
        expect(screen.getByRole('heading', { name: 'Dashboard' })).toBeInTheDocument();
        expect(screen.getByRole('navigation', { name: 'Navegación administrativa' })).toBeInTheDocument();
    });

    it('asks for confirmation before suspending a user', () => {
        render(
            <MemoryRouter>
                <UsersList />
            </MemoryRouter>,
        );
        fireEvent.click(screen.getAllByRole('button', { name: 'Suspender' })[0]);
        expect(screen.getByRole('heading', { name: 'Suspender usuario' })).toBeInTheDocument();
        fireEvent.click(screen.getByRole('button', { name: 'Cancelar' }));
    });
});
