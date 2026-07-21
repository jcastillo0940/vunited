import type { ReactNode } from 'react';
import { Header, type HeaderProps } from './Header';
import { Footer, type FooterProps } from './Footer';
import { SkipLink } from './SkipLink';
import { cx } from '../cx';

export interface LayoutProps {
    header: HeaderProps;
    footer: FooterProps;
    children: ReactNode;
    mainClassName?: string;
    announcement?: ReactNode;
}

/** Armazón de página: SkipLink + Header + <main> + Footer, sin datos propios. */
export function Layout({ header, footer, children, mainClassName = 'pt-24', announcement }: LayoutProps) {
    return (
        <div className="min-h-screen bg-background text-text-main">
            <SkipLink />
            {announcement}
            <Header {...header} />
            <main id="main-content" className={cx(mainClassName)}>
                {children}
            </main>
            <Footer {...footer} />
        </div>
    );
}
