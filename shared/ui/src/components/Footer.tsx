import { Container } from './Container';
import { Logo } from './Logo';
import { Icon } from './Icon';

export interface FooterColumn {
    title: string;
    items: Array<{ label: string; url?: string | null }>;
}

export interface FooterSocialLink {
    label: string;
    url: string;
    icon: string;
}

export interface FooterProps {
    brandName?: string;
    logoUrl?: string | null;
    description?: string;
    socialLinks?: FooterSocialLink[];
    columns: FooterColumn[];
    legalLinks?: Array<{ label: string; url: string }>;
}

/** Equivalente tipado de Footer.jsx del frontend actual. */
export function Footer({
    brandName = 'Veraguas United FC',
    logoUrl,
    description = 'El orgullo de la provincia de Veraguas.',
    socialLinks = [],
    columns,
    legalLinks = [],
}: FooterProps) {
    return (
        <footer className="bg-primary pb-12 pt-24 text-white">
            <Container className="max-w-7xl">
                <div className="mb-20 grid gap-20 lg:grid-cols-4">
                    <div className="lg:col-span-1">
                        <div className="mb-10 flex items-center gap-4">
                            {logoUrl ? (
                                <img src={logoUrl} alt={brandName} className="h-16 w-16 object-contain" />
                            ) : (
                                <Logo className="h-16 w-16 text-white" title={brandName} />
                            )}
                            <div className="font-display text-3xl font-bold uppercase leading-none tracking-tight text-white">
                                {brandName}
                            </div>
                        </div>
                        <p className="mb-10 max-w-md text-sm leading-relaxed text-white/70">{description}</p>
                        {socialLinks.length ? (
                            <div className="flex gap-4">
                                {socialLinks.map((social) => (
                                    <a
                                        key={social.label}
                                        href={social.url}
                                        aria-label={social.label}
                                        className="rounded-full bg-white/10 p-2 text-white hover:bg-white/20"
                                    >
                                        <Icon name={social.icon} size="sm" />
                                    </a>
                                ))}
                            </div>
                        ) : null}
                    </div>

                    {columns.map((column) => (
                        <div key={column.title}>
                            <h3 className="mb-4 font-display text-sm font-bold uppercase tracking-wide text-white/60">
                                {column.title}
                            </h3>
                            <ul className="flex flex-col gap-2 text-sm text-white/80">
                                {column.items.map((item) => (
                                    <li key={item.label}>
                                        {item.url ? (
                                            <a href={item.url} className="hover:text-white hover:underline">
                                                {item.label}
                                            </a>
                                        ) : (
                                            <span className="text-white/40">{item.label}</span>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                {legalLinks.length ? (
                    <div className="flex flex-wrap gap-6 border-t border-white/10 pt-8 text-xs text-white/50">
                        {legalLinks.map((link) => (
                            <a key={link.label} href={link.url} className="hover:text-white">
                                {link.label}
                            </a>
                        ))}
                    </div>
                ) : null}
            </Container>
        </footer>
    );
}
