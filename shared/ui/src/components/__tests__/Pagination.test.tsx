import { describe, expect, it, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import { Pagination } from '../Pagination';

describe('Pagination', () => {
    it('does not render with a single page', () => {
        const { container } = render(<Pagination page={1} totalPages={1} onChange={vi.fn()} />);
        expect(container).toBeEmptyDOMElement();
    });

    it('disables the previous button on the first page and calls onChange going forward', () => {
        const onChange = vi.fn();
        render(<Pagination page={1} totalPages={3} onChange={onChange} />);

        expect(screen.getByLabelText('Página anterior')).toBeDisabled();
        fireEvent.click(screen.getByLabelText('Página siguiente'));
        expect(onChange).toHaveBeenCalledWith(2);
    });
});
