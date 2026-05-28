import CTAButton from '@/components/common/CTAButton';

export default function HeroSection({
    eyebrow,
    title,
    highlight,
    description,
    primaryAction,
    secondaryAction,
    imageUrl,
}) {
    const backgroundStyle = imageUrl
        ? {
              backgroundImage: `linear-gradient(rgba(29, 66, 138, 0.82), rgba(29, 66, 138, 0.94)), url(${imageUrl})`,
          }
        : undefined;

    return (
        <section
            className="relative flex min-h-[88svh] items-center overflow-hidden bg-primary bg-cover bg-center pt-24"
            style={backgroundStyle}
        >
            <div className="page-shell relative z-10 py-20">
                <div className="max-w-4xl">
                    {eyebrow ? (
                        <div className="mb-6 inline-flex items-center gap-4">
                            <span className="rounded-sm bg-accent px-4 py-1 font-body text-xs font-bold uppercase tracking-athletic text-white">
                                {eyebrow}
                            </span>
                        </div>
                    ) : null}
                    <h1 className="font-display text-5xl font-black uppercase leading-[0.95] tracking-tight text-white md:text-8xl">
                        {title}
                        {highlight ? <span className="block text-accent">{highlight}</span> : null}
                    </h1>
                    {description ? (
                        <p className="mt-8 max-w-2xl text-xl leading-relaxed text-white/90 md:text-2xl">
                            {description}
                        </p>
                    ) : null}
                    <div className="mt-10 flex flex-wrap gap-4">
                        {primaryAction ? (
                            <CTAButton
                                as={primaryAction.href ? 'a' : 'button'}
                                href={primaryAction.href ?? undefined}
                                type={primaryAction.href ? undefined : 'button'}
                                variant="primary"
                                size="lg"
                            >
                                {primaryAction.label}
                            </CTAButton>
                        ) : null}
                        {secondaryAction ? (
                            <CTAButton
                                as={secondaryAction.href ? 'a' : 'button'}
                                href={secondaryAction.href ?? undefined}
                                type={secondaryAction.href ? undefined : 'button'}
                                variant="ghost"
                                size="lg"
                                className="border-2 border-white"
                            >
                                {secondaryAction.label}
                            </CTAButton>
                        ) : null}
                    </div>
                </div>
            </div>
        </section>
    );
}
