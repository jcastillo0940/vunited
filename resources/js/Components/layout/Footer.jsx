import CTAButton from '@/components/common/CTAButton';

export default function Footer({
    logoUrl,
    brandName = 'Veraguas United FC',
    description = 'El orgullo de la provincia de Veraguas.',
    socialLinks = [],
    footerMenu = [],
    legalMenu = [],
}) {
    const footerColumns = footerMenu.length
        ? normalizeFooterMenu(footerMenu)
        : [
              {
                  title: 'Club',
                  items: [
                      { label: 'Historia', url: null, pendingLabel: 'CMS pendiente' },
                      { label: 'Organigrama', url: null, pendingLabel: 'CMS pendiente' },
                  ],
              },
              {
                  title: 'Comunidad',
                  items: [
                      { label: 'Contacto', url: null, pendingLabel: 'Pendiente' },
                      { label: 'Prensa', url: '/noticias' },
                  ],
              },
          ];

    return (
        <footer className="bg-primary pb-12 pt-24 text-white">
            <div className="page-shell max-w-7xl">
                <div className="mb-20 grid gap-20 lg:grid-cols-4">
                    <div className="lg:col-span-1">
                        <div className="mb-10 flex items-center gap-4">
                            {logoUrl ? (
                                <img src={logoUrl} alt={brandName} className="h-16 w-16 object-contain" />
                            ) : (
                                <div className="flex h-16 w-16 items-center justify-center rounded-md bg-white/10">
                                    <span className="material-symbols-outlined">shield</span>
                                </div>
                            )}
                            <div className="font-display text-3xl font-bold uppercase leading-none tracking-tight text-white">
                                {renderBrandLockup(brandName)}
                            </div>
                        </div>
                        <p className="mb-10 max-w-md text-sm leading-relaxed text-white/70">
                            {description}
                        </p>
                        <div className="flex gap-4">
                            {socialLinks.map((link) => (
                                <a
                                    key={`${link.label}-${link.url}`}
                                    href={link.url}
                                    className="group flex h-12 w-12 items-center justify-center rounded-md border border-white/10 bg-white/10 transition-all hover:bg-accent"
                                    aria-label={link.label}
                                >
                                    <span className="material-symbols-outlined text-white transition-transform group-hover:scale-110">
                                        {link.icon ?? 'share'}
                                    </span>
                                </a>
                            ))}
                        </div>
                    </div>
                    {footerColumns.map((column) => (
                        <div key={column.title}>
                            <h4 className="mb-10 border-b border-white/10 pb-4 font-display text-xl font-bold uppercase text-accent">
                                {column.title}
                            </h4>
                            <ul className="space-y-5">
                                {column.items.map((item) => (
                                    <li key={`${column.title}-${item.label}`}>
                                        <FooterLink item={item} />
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                    <div>
                        <h4 className="mb-10 border-b border-white/10 pb-4 font-display text-xl font-bold uppercase text-accent">
                            Boletin indio
                        </h4>
                        <p className="mb-8 text-xs leading-relaxed text-white/70">
                            Recibe noticias y ofertas exclusivas directamente en tu correo.
                        </p>
                        <div className="space-y-6">
                            <input
                                type="email"
                                placeholder="Correo electronico"
                                className="w-full border-b border-white/20 bg-transparent px-0 py-3 text-sm text-white placeholder:text-white/30 focus:border-accent focus:ring-0"
                            />
                            <CTAButton variant="primary" className="w-full py-4 text-xs tracking-[0.2em] shadow-lg">
                                Suscribirse
                            </CTAButton>
                        </div>
                    </div>
                </div>
                <div className="flex flex-col items-center justify-between gap-8 border-t border-white/10 pt-12 md:flex-row">
                    <p className="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">
                        (c) {new Date().getFullYear()} Veraguas United FC. Todos los derechos reservados.
                    </p>
                    <div className="flex flex-wrap items-center gap-10">
                        {legalMenu.map((item) => (
                            <FooterLink
                                key={item.label}
                                item={item}
                                className="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40 transition-colors hover:text-accent"
                            />
                        ))}
                    </div>
                </div>
            </div>
        </footer>
    );
}

function FooterLink({ item, className = 'text-sm font-bold uppercase tracking-wide text-white/70 transition-colors hover:text-accent' }) {
    if (item.url) {
        return (
            <a href={item.url} className={className}>
                {item.label}
            </a>
        );
    }

    return (
        <span className={`${className} cursor-default opacity-70`}>
            {item.label}
            {item.pendingLabel ? (
                <span className="ml-2 text-[10px] font-bold uppercase tracking-[0.18em] text-white/35">
                    {item.pendingLabel}
                </span>
            ) : null}
        </span>
    );
}

function normalizeFooterMenu(footerMenu) {
    if (!footerMenu.length) {
        return [];
    }

    if ('items' in footerMenu[0]) {
        return footerMenu;
    }

    const midpoint = Math.ceil(footerMenu.length / 2);

    return [
        {
            title: 'Club',
            items: footerMenu.slice(0, midpoint),
        },
        {
            title: 'Comunidad',
            items: footerMenu.slice(midpoint),
        },
    ];
}

function renderBrandLockup(brandName) {
    const parts = brandName.split(' ');

    if (parts.length <= 2) {
        return (
            <>
                {parts[0]}
                <br />
                {parts.slice(1).join(' ')}
            </>
        );
    }

    return (
        <>
            {parts.slice(0, 2).join(' ')}
            <br />
            {parts.slice(2).join(' ')}
        </>
    );
}
