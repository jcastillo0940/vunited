export default function PartnersCarousel({ partners = [] }) {
    if (!partners.length) return null;

    const loop = [...partners, ...partners];

    return (
        <section className="overflow-hidden border-y border-gray-200 bg-surface py-24">
            <div className="mb-12 text-center">
                <h2 className="text-xs font-bold uppercase tracking-[0.4em] text-gray-400">
                    ALIADOS ESTRATÉGICOS
                </h2>
            </div>
            <div className="flex animate-scroll whitespace-nowrap">
                <div className="flex items-center gap-24 px-12">
                    {loop.map((partner, index) => (
                        <PartnerItem key={index} partner={partner} />
                    ))}
                </div>
            </div>
        </section>
    );
}

function PartnerItem({ partner }) {
    const name = typeof partner === 'string' ? partner : (partner.name ?? '');
    const logo = typeof partner === 'string' ? null    : (partner.logo_path ?? null);
    const href = typeof partner === 'string' ? null    : (partner.website_url ?? null);

    const inner = logo ? (
        <img
            src={logo}
            alt={name}
            className="h-10 max-w-[160px] object-contain opacity-50 transition-opacity hover:opacity-90"
        />
    ) : (
        <span className="font-display text-4xl font-bold uppercase text-gray-300 transition-colors hover:text-primary">
            {name}
        </span>
    );

    if (href) {
        return (
            <a href={href} target="_blank" rel="noopener noreferrer" aria-label={name}>
                {inner}
            </a>
        );
    }

    return <span className="cursor-default">{inner}</span>;
}
