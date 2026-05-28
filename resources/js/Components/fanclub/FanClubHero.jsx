import CTAButton from '@/components/common/CTAButton';

export default function FanClubHero({ hero }) {
    return (
        <section className="relative flex min-h-[70vh] items-center overflow-hidden pt-24">
            <div className="absolute inset-0 z-0">
                <img src={hero.imageUrl} alt={hero.title} className="h-full w-full object-cover" />
                <div className="absolute inset-0 bg-[linear-gradient(rgba(29,66,138,0.8),rgba(29,66,138,0.42))]" />
            </div>

            <div className="page-shell relative z-30 mx-auto max-w-7xl">
                <div className="max-w-2xl rounded-r-lg border-l-8 border-accent bg-white/10 p-10 backdrop-blur-md">
                    <h1 className="font-display text-5xl font-black uppercase leading-none tracking-tighter text-white md:text-7xl">
                        {hero.title}
                        <br />
                        <span className="text-accent">{hero.highlight}</span>
                    </h1>
                    <p className="mb-10 mt-6 max-w-lg text-xl leading-relaxed text-white/90">
                        {hero.description}
                    </p>
                    <div className="flex flex-wrap gap-4">
                        <CTAButton as="a" href={hero.primaryAction.href} size="lg" className="font-display text-xl shadow-xl">
                            {hero.primaryAction.label}
                        </CTAButton>
                        <CTAButton
                            as="a"
                            href={hero.secondaryAction.href}
                            variant="ghost"
                            size="md"
                            className="border border-white/30 px-6 py-4 text-white hover:bg-white/10"
                        >
                            {hero.secondaryAction.label}
                        </CTAButton>
                    </div>
                </div>
            </div>
        </section>
    );
}
