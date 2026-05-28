import CTAButton from '@/components/common/CTAButton';

export default function AcademyCTA({ cta }) {
    return (
        <section className="bg-surface py-24">
            <div className="mx-auto flex max-w-7xl flex-col items-center gap-16 px-margin-mobile md:flex-row md:px-margin-desktop">
                <div className="md:w-1/2">
                    <h2 className="font-display text-5xl font-bold uppercase text-primary">
                        {cta.title}
                    </h2>
                    <p className="mb-10 mt-6 text-lg leading-relaxed text-text-main/80">
                        {cta.description}
                    </p>
                    <div className="flex flex-col gap-4 sm:flex-row">
                        <CTAButton as="a" href={cta.primaryHref} variant="primary" size="md">
                            {cta.primaryLabel}
                        </CTAButton>
                        <CTAButton as="a" href={cta.secondaryHref} variant="outline" size="md">
                            {cta.secondaryLabel}
                        </CTAButton>
                    </div>
                </div>
                <div className="md:w-1/2">
                    <div className="group relative aspect-video overflow-hidden rounded-xl bg-primary shadow-2xl">
                        <img
                            alt={cta.title}
                            className="h-full w-full object-cover opacity-70 transition-transform duration-700 group-hover:scale-105"
                            src={cta.imageUrl}
                        />
                        <div className="absolute inset-0 flex items-center justify-center bg-primary/20 transition-colors hover:bg-transparent">
                            <span className="material-symbols-outlined text-7xl text-white opacity-90 transition-transform hover:scale-110">
                                play_circle
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
