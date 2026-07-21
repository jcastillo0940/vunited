import { describe, expect, it, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import { Button } from '../Button';

describe('Button', () => {
    it('renders children and responds to click', () => {
        const onClick = vi.fn();
        render(<Button onClick={onClick}>Comprar entradas</Button>);
        const button = screen.getByRole('button', { name: 'Comprar entradas' });
        fireEvent.click(button);
        expect(onClick).toHaveBeenCalledTimes(1);
    });

    it('renders as an anchor when as="a"', () => {
        render(
            <Button as="a" href="/boletos">
                Ver boletos
            </Button>,
        );
        const link = screen.getByRole('link', { name: 'Ver boletos' });
        expect(link).toHaveAttribute('href', '/boletos');
    });

    it('shows the pending label and disables the button while pending', () => {
        render(
            <Button pending pendingLabel="Enviando…">
                Enviar
            </Button>,
        );
        const button = screen.getByRole('button', { name: 'Enviando…' });
        expect(button).toBeDisabled();
    });
});
