export interface SkipLinkProps {
    targetId?: string;
    label?: string;
}

/** Enlace "saltar al contenido" — oculto hasta recibir foco por teclado. */
export function SkipLink({ targetId = 'main-content', label = 'Saltar al contenido principal' }: SkipLinkProps) {
    return (
        <a href={`#${targetId}`} className="skip-link">
            {label}
        </a>
    );
}
