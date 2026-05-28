import CTAButton from '@/components/common/CTAButton';

export default function StoreHero({ hero, membershipBanner, cartCount }) {
    return (
        <section className="bg-primary pb-16 pt-32 text-white">
            <div className="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop">
                <div className="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div className="max-w-3xl">
                        <p className="mb-3 font-body text-sm font-semibold uppercase tracking-[0.35em] text-accent">
                            Catalogo United
                        </p>
                        <h1 className="font-display text-5xl font-black uppercase leading-none md:text-7xl">
                            {hero.title}
                        </h1>
                        <p className="mt-5 max-w-2xl text-base leading-7 text-white/80 md:text-lg">
                            {hero.description}
                        </p>
                    </div>
                    <div className="rounded-xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur-sm">
                        <p className="text-xs font-semibold uppercase tracking-[0.3em] text-accent">
                            Tu carrito
                        </p>
                        <p className="mt-2 font-display text-3xl font-bold uppercase">
                            {cartCount} item{cartCount === 1 ? '' : 's'}
                        </p>
                    </div>
                </div>

                <div className="mt-8">
                    <CTAButton href={hero.ctaHref} variant="accent" size="lg">
                        {hero.ctaLabel}
                    </CTAButton>
                </div>

                <div className="relative mt-10 overflow-hidden rounded-xl bg-white p-8 text-primary shadow-panel">
                    <div className="absolute inset-y-0 right-0 hidden w-56 bg-gradient-to-l from-accent/20 to-transparent lg:block" />
                    <div className="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div className="flex items-start gap-4">
                            <span
                                className="material-symbols-outlined text-5xl text-accent"
                                style={{ fontVariationSettings: "'FILL' 1" }}
                            >
                                star
                            </span>
                            <div>
                                <h2 className="font-display text-2xl font-bold uppercase md:text-3xl">
                                    {membershipBanner.title}
                                </h2>
                                <p className="mt-2 max-w-2xl text-sm leading-6 text-primary/75 md:text-base">
                                    {membershipBanner.description}
                                </p>
                            </div>
                        </div>
                        <CTAButton href={membershipBanner.ctaHref} variant="primary">
                            {membershipBanner.ctaLabel}
                        </CTAButton>
                    </div>
                </div>
            </div>
        </section>
    );
}
