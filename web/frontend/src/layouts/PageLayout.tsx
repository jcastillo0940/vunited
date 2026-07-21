import type { ReactNode } from 'react';
import { Container } from '@veraguas/ui';

export interface PageLayoutProps {
    kicker?: string;
    title: string;
    intro?: string;
    children?: ReactNode;
}

/** "Layout de páginas": contenido institucional/CMS genérico de una columna. */
export function PageLayout({ kicker, title, intro, children }: PageLayoutProps) {
    return (
        <div className="section-space">
            <Container className="max-w-3xl">
                {kicker ? <p className="display-kicker mb-2">{kicker}</p> : null}
                <h1 className="section-heading">{title}</h1>
                {intro ? <p className="mt-4 text-lg text-text-main/80">{intro}</p> : null}
                <div className="mt-10 space-y-4 text-base leading-relaxed text-text-main">{children}</div>
            </Container>
        </div>
    );
}
