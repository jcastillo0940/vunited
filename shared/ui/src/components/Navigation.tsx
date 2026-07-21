export interface NavLink {
    label: string;
    url: string | null;
    pending?: boolean;
    pendingLabel?: string | null;
    active?: boolean;
}

export interface NavigationProps {
    links: NavLink[];
    variant?: 'light' | 'dark';
}

/** Links de escritorio — replica DesktopNavItem de MainNavbar.jsx. */
export function Navigation({ links, variant = 'light' }: NavigationProps) {
    const isLight = variant === 'light';
    return (
        <div className="hidden items-center gap-8 lg:flex">
            {links.map((link) => {
                const disabled = !link.url;
                const label = disabled && link.pendingLabel ? link.pendingLabel : link.label;
                const className = [
                    'text-sm font-semibold uppercase tracking-tight transition-colors',
                    isLight ? 'text-text-main hover:text-primary' : 'text-white/80 hover:text-white',
                    link.active && (isLight ? 'text-primary' : 'text-white'),
                    disabled && 'cursor-not-allowed opacity-50',
                ]
                    .filter(Boolean)
                    .join(' ');

                if (disabled) {
                    return (
                        <span key={link.label} className={className} aria-disabled="true">
                            {label}
                        </span>
                    );
                }
                return (
                    <a key={link.label} href={link.url ?? undefined} className={className} aria-current={link.active ? 'page' : undefined}>
                        {label}
                    </a>
                );
            })}
        </div>
    );
}
