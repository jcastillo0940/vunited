import { describe, expect, it } from 'vitest';
import { render, screen } from '@testing-library/react';
import { FormField } from '../FormField';
import { Input } from '../Input';

describe('FormField', () => {
    it('associates the label and error message with the control', () => {
        render(
            <FormField htmlFor="email" label="Correo" error="Correo inválido" required>
                <Input id="email" invalid />
            </FormField>,
        );

        const input = screen.getByLabelText(/Correo/);
        expect(input).toHaveAttribute('aria-invalid', 'true');
        expect(screen.getByRole('alert')).toHaveTextContent('Correo inválido');
    });

    it('does not render FieldError when there is no error', () => {
        render(
            <FormField htmlFor="name" label="Nombre">
                <Input id="name" />
            </FormField>,
        );
        expect(screen.queryByRole('alert')).not.toBeInTheDocument();
    });
});
