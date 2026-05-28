import CTAButton from '@/components/common/CTAButton';

export default function AcademyHero({ hero }) {
    return (
        <section className="relative flex h-[600px] items-center justify-start overflow-hidden bg-primary">
            <div className="absolute inset-0 z-0">
                <img
                    alt={hero.highlight}
                    className="h-full w-full object-cover opacity-30"
                    src={hero.imageUrl}
                />
            </div>
            <div className="page-shell relative z-10 max-w-5xl">
                <div className="mb-6 h-1.5 w-20 bg-accent" />
                <h1 className="font-display text-5xl font-bold uppercase leading-tight text-white md:text-7xl">
                    {hero.title}
                    <br />
                    <span className="text-accent">{hero.highlight}</span>
                </h1>
                <p className="mt-6 max-w-2xl border-l-4 border-accent pl-6 text-lg text-white/90">
                    {hero.description}
                </p>
                <div className="mt-10">
                    <CTAButton as="a" href={hero.ctaHref} variant="primary" size="md">
                        {hero.ctaLabel}
                    </CTAButton>
                </div>
            </div>
        </section>
    );
}
