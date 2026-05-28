export default function SponsorCard({ sponsor, variant = 'official' }) {
    if (variant === 'alliance') {
        return (
            <div className="flex items-center justify-between rounded-full border border-primary/10 bg-white px-5 py-4 shadow-sm transition-transform duration-200 hover:-translate-y-0.5">
                <span className="font-body text-sm font-semibold uppercase tracking-[0.18em] text-primary">
                    {sponsor.name}
                </span>
                <span className="rounded-full bg-surface px-3 py-2 text-[10px] font-bold uppercase tracking-athletic text-accent">
                    {sponsor.shortLabel}
                </span>
            </div>
        );
    }

    if (variant === 'main') {
        return (
            <article className="surface-panel flex min-h-[250px] flex-col justify-between rounded-[28px] border border-white/70 bg-white/95 p-10">
                <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary text-2xl font-display font-bold uppercase tracking-tight text-white shadow-lg shadow-primary/20">
                    {sponsor.shortLabel}
                </div>
                <div className="space-y-4">
                    <p className="text-[10px] font-bold uppercase tracking-[0.35em] text-accent">
                        {sponsor.tier}
                    </p>
                    <h3 className="font-display text-3xl font-bold uppercase leading-none text-primary md:text-4xl">
                        {sponsor.name}
                    </h3>
                    <p className="max-w-md text-sm leading-relaxed text-gray-600">
                        {sponsor.tagline}
                    </p>
                </div>
            </article>
        );
    }

    return (
        <article className="surface-card group flex min-h-[220px] flex-col items-center justify-center rounded-[24px] border border-primary/10 bg-surface p-8 text-center transition-all duration-200 hover:bg-white hover:shadow-panel">
            <div className="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-white text-xl font-display font-bold uppercase tracking-tight text-primary shadow-sm">
                {sponsor.shortLabel}
            </div>
            <h3 className="font-display text-2xl font-bold uppercase leading-tight text-primary">
                {sponsor.name}
            </h3>
            <p className="mt-3 text-[10px] font-bold uppercase tracking-[0.28em] text-accent">
                {sponsor.tier}
            </p>
        </article>
    );
}
