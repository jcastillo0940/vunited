import { describe, expect, it } from 'vitest';
import { render, screen } from '@testing-library/react';
import { Navigation } from '../Navigation';

describe('Navigation', () => {
    it('renders active links and disables pending ones', () => {
        render(
            <Navigation
                links={[
                    { label: 'Inicio', url: '/', active: true },
                    { label: 'FanClub', url: null, pending: true, pendingLabel: 'FanClub pendiente' },
                ]}
            />,
        );

        const active = screen.getByRole('link', { name: 'Inicio' });
        expect(active).toHaveAttribute('aria-current', 'page');

        const pending = screen.getByText('FanClub pendiente');
        expect(pending.tagName).toBe('SPAN');
        expect(pending).toHaveAttribute('aria-disabled', 'true');
    });
});
