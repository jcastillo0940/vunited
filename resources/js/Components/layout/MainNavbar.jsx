import { useState } from 'react';
import CTAButton from '@/components/common/CTAButton';

export default function MainNavbar({
    fixed = true,
    variant = 'light',
    logoUrl,
    brandName = 'VERAGUAS UNITED',
    links = [],
    ctaLabel = 'UNETE A LA TRIBU',
    ctaHref = null,
    ctaPending = true,
    ctaPendingLabel = 'FanClub pendiente',
}) {
    const [open, setOpen] = useState(false);
    const isLight = variant === 'light';
    const brandParts = brandName.split(' ');

    return (
        <nav
            className={[
                fixed ? 'fixed left-0 right-0 z-40' : 'relative z-10',
                fixed ? 'top-10' : '',
                isLight
                    ? 'border-b border-gray-200 bg-white/95 text-primary shadow-sm backdrop-blur-md'
                    : 'border-b border-white/10 bg-primary text-white shadow-lg',
            ].join(' ')}
        >
            <div className="page-shell max-w-7xl">
                <div className="flex items-center justify-between gap-6 py-4">
                    <div className="flex items-center gap-4">
                        {logoUrl ? (
                            <img
                                src={logoUrl}
                                alt={brandName}
                                className="h-12 w-12 object-contain"
                            />
                        ) : (
                            <div className="flex h-12 w-12 items-center justify-center rounded-md bg-surface text-primary">
                                <span className="material-symbols-outlined">shield</span>
                            </div>
                        )}
                        <div
                            className={[
                                'font-display text-2xl font-bold uppercase leading-none tracking-tight',
                                isLight ? 'text-primary' : 'text-white',
                            ].join(' ')}
                        >
                            {brandParts[0]}
                            <br />
                            {brandParts.slice(1).join(' ')}
                        </div>
                    </div>

                    <div className="hidden items-center gap-8 lg:flex">
                        {links.map((link) => (
                            <DesktopNavItem key={`${link.label}-${link.url ?? 'pending'}`} link={link} isLight={isLight} />
                        ))}
                    </div>

                    <div className="flex items-center gap-4">
                        <div className="hidden lg:flex lg:flex-col lg:items-end lg:gap-1">
                            {ctaHref ? (
                                <CTAButton as="a" href={ctaHref} size="md" className="hidden lg:inline-flex">
                                    {ctaLabel}
                                </CTAButton>
                            ) : (
                                <CTAButton size="md" className="hidden cursor-default opacity-90 lg:inline-flex" type="button">
                                    {ctaLabel}
                                </CTAButton>
                            )}
                            {ctaPending ? (
                                <span className={['text-[10px] font-bold uppercase tracking-[0.22em]', isLight ? 'text-gray-400' : 'text-white/60'].join(' ')}>
                                    {ctaPendingLabel}
                                </span>
                            ) : null}
                        </div>
                        <button
                            type="button"
                            className={['lg:hidden', isLight ? 'text-primary' : 'text-white'].join(' ')}
                            aria-label="Abrir navegacion"
                            aria-expanded={open}
                            onClick={() => setOpen((value) => !value)}
                        >
                            <span className="material-symbols-outlined">menu</span>
                        </button>
                    </div>
                </div>

                {open ? (
                    <div className="border-t border-gray-200 bg-white py-4 shadow-sm lg:hidden">
                        <div className="flex flex-col gap-4">
                            {links.map((link) => (
                                <MobileNavItem key={`mobile-${link.label}-${link.url ?? 'pending'}`} link={link} />
                            ))}
                            {ctaHref ? (
                                <CTAButton as="a" href={ctaHref} size="md" className="mt-2 w-full justify-center">
                                    {ctaLabel}
                                </CTAButton>
                            ) : (
                                <div className="mt-2 space-y-2">
                                    <CTAButton size="md" className="w-full justify-center opacity-90" type="button">
                                        {ctaLabel}
                                    </CTAButton>
                                    {ctaPending ? (
                                        <p className="text-center text-[10px] font-bold uppercase tracking-[0.22em] text-gray-400">
                                            {ctaPendingLabel}
                                        </p>
                                    ) : null}
                                </div>
                            )}
                        </div>
                    </div>
                ) : null}
            </div>
        </nav>
    );
}

function DesktopNavItem({ link, isLight }) {
    const baseClasses = [
        'font-body text-sm font-semibold transition-colors',
        link.active
            ? isLight
                ? 'border-b-2 border-primary pb-1 text-primary'
                : 'border-b-2 border-accent pb-1 text-accent'
            : isLight
              ? 'text-gray-600 hover:text-accent'
              : 'text-white/80 hover:text-accent',
    ].join(' ');

    if (link.children?.length) {
        return (
            <div className="group relative">
                <button type="button" className={`${baseClasses} inline-flex items-center gap-2`}>
                    {link.label}
                    <span className="material-symbols-outlined text-base">expand_more</span>
                </button>
                <div className="invisible absolute left-0 top-full z-20 mt-4 min-w-64 rounded-xl border border-gray-200 bg-white p-3 opacity-0 shadow-xl transition-all duration-150 group-hover:visible group-hover:opacity-100">
                    <div className="flex flex-col gap-1">
                        {link.children.map((child) => (
                            <NavAnchor
                                key={`${link.label}-${child.label}`}
                                link={child}
                                className="rounded-lg px-4 py-3 text-sm font-semibold text-gray-700 transition-colors hover:bg-surface hover:text-primary"
                            />
                        ))}
                    </div>
                </div>
            </div>
        );
    }

    return <NavAnchor link={link} className={baseClasses} />;
}

function MobileNavItem({ link }) {
    if (link.children?.length) {
        return (
            <div className="space-y-2">
                <div className={`font-body text-sm font-semibold ${link.active ? 'text-primary' : 'text-gray-600'}`}>
                    {link.label}
                </div>
                <div className="ml-4 flex flex-col gap-2 border-l border-gray-200 pl-4">
                    {link.children.map((child) => (
                        <NavAnchor
                            key={`${link.label}-${child.label}`}
                            link={child}
                            className={`font-body text-sm font-semibold transition-colors ${child.active ? 'text-primary' : 'text-gray-600 hover:text-accent'}`}
                        />
                    ))}
                </div>
            </div>
        );
    }

    return (
        <NavAnchor
            link={link}
            className={[
                'font-body text-sm font-semibold transition-colors',
                link.active ? 'text-primary' : 'text-gray-600 hover:text-accent',
            ].join(' ')}
        />
    );
}

function NavAnchor({ link, className }) {
    if (link.url) {
        return (
            <a href={link.url} className={className}>
                {link.label}
            </a>
        );
    }

    return (
        <span className={`${className} cursor-default opacity-70`}>
            {link.label}
            {link.pendingLabel ? (
                <span className="ml-2 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">
                    {link.pendingLabel}
                </span>
            ) : null}
        </span>
    );
}
