import SponsorCard from '@/components/sponsors/SponsorCard';

export default function SponsorTierSection({ tier }) {
    if (tier.variant === 'main') {
        return (
            <section className="space-y-10">
                <div className="flex items-center gap-6">
                    <span className="hidden h-px flex-1 bg-accent/50 md:block" />
                    <h2 className="font-display text-4xl font-bold uppercase tracking-tight text-primary md:text-5xl">
                        {tier.title}
                    </h2>
                    <span className="hidden h-px flex-1 bg-accent/50 md:block" />
                </div>
                <div className="grid gap-8 lg:grid-cols-2">
                    {tier.sponsors.map((sponsor) => (
                        <SponsorCard key={sponsor.id} sponsor={sponsor} variant="main" />
                    ))}
                </div>
            </section>
        );
    }

    if (tier.variant === 'alliance') {
        return (
            <section className="space-y-6">
                <div className="space-y-3">
                    <p className="text-[10px] font-bold uppercase tracking-[0.35em] text-accent">
                        Ecosistema comercial
                    </p>
                    <h2 className="font-display text-3xl font-bold uppercase tracking-tight text-primary md:text-4xl">
                        {tier.title}
                    </h2>
                </div>
                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {tier.sponsors.map((sponsor) => (
                        <SponsorCard key={sponsor.id} sponsor={sponsor} variant="alliance" />
                    ))}
                </div>
            </section>
        );
    }

    return (
        <section className="space-y-8">
            <div className="space-y-4">
                <p className="text-[10px] font-bold uppercase tracking-[0.35em] text-accent">
                    Red comercial
                </p>
                <h2 className="inline-flex border-b-2 border-accent pb-4 font-display text-3xl font-bold uppercase tracking-tight text-primary md:text-4xl">
                    {tier.title}
                </h2>
            </div>
            <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                {tier.sponsors.map((sponsor) => (
                    <SponsorCard key={sponsor.id} sponsor={sponsor} variant="official" />
                ))}
            </div>
        </section>
    );
}
