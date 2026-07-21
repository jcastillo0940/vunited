import type { ReactNode } from 'react';
import { Container } from '@veraguas/ui';

export interface InstitutionalLayoutProps {
    kicker: string;
    title: string;
    description?: string;
    children: ReactNode;
}

/** "Layout institucional": Directiva, Patrocinadores, Estadio, Plantilla, FanFest, Expedición. */
export function InstitutionalLayout({ kicker, title, description, children }: InstitutionalLayoutProps) {
    return (
        <>
            <div className="bg-surface py-16 md:py-20">
                <Container>
                    <p className="display-kicker mb-2">{kicker}</p>
                    <h1 className="section-heading">{title}</h1>
                    {description ? <p className="mt-4 max-w-2xl text-lg text-text-main/80">{description}</p> : null}
                </Container>
            </div>
            <div className="section-space">
                <Container>{children}</Container>
            </div>
        </>
    );
}
