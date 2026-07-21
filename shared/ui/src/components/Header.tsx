import { useState } from 'react';
import { Container } from './Container';
import { Logo } from './Logo';
import { Navigation, type NavLink } from './Navigation';
import { MobileMenu } from './MobileMenu';
import { Button } from './Button';
import { cx } from '../cx';
import { zIndex } from '../tokens';

export interface HeaderProps {
    brandName?: string;
    logoUrl?: string | null;
    links: NavLink[];
    variant?: 'light' | 'dark';
    fixed?: boolean;
    offsetTop?: boolean;
    ctaLabel?: string;
    ctaHref?: string | null;
}

/** Equivalente tipado de MainNavbar.jsx del frontend actual. */
export function Header({
    brandName = 'VERAGUAS UNITED',
    logoUrl,
    links,
    variant = 'light',
    fixed = true,
    offsetTop = true,
    ctaLabel,
    ctaHref,
}: HeaderProps) {
    const [open, setOpen] = useState(false);
    const isLight = variant === 'light';
    const brandParts = brandName.split(' ');

    return (
        <nav
            className={cx(
                'relative',
                fixed && 'fixed left-0 right-0',
                fixed && offsetTop && 'top-10',
                isLight
                    ? 'border-b border-gray-200 bg-white/95 text-primary shadow-sm backdrop-blur-md'
                    : 'border-b border-white/10 bg-primary text-white shadow-lg',
            )}
            style={{ zIndex: zIndex.navbar }}
        >
            <Container className="max-w-7xl">
                <div className="flex items-center justify-between gap-6 py-4">
                    <div className="flex items-center gap-4">
                        {logoUrl ? (
                            <img src={logoUrl} alt={brandName} className="h-12 w-12 object-contain" />
                        ) : (
                            <Logo className={cx('h-12 w-12', isLight ? 'text-primary' : 'text-white')} title={brandName} />
                        )}
                        <div className={cx('font-display text-2xl font-bold uppercase leading-none tracking-tight', isLight ? 'text-primary' : 'text-white')}>
                            {brandParts[0]}
                            <br />
                            {brandParts.slice(1).join(' ')}
                        </div>
                    </div>

                    <Navigation links={links} variant={variant} />

                    <div className="flex items-center gap-3">
                        {ctaHref ? (
                            <Button as="a" href={ctaHref} size="sm" className="hidden lg:inline-flex">
                                {ctaLabel}
                            </Button>
                        ) : null}
                        <MobileMenu open={open} onToggle={() => setOpen((v) => !v)} links={links} />
                    </div>
                </div>
            </Container>
        </nav>
    );
}
