import CTAButton from '@/components/common/CTAButton';

function extractYouTubeId(url) {
    if (!url) return null;
    const patterns = [
        /[?&]v=([^&#]+)/,
        /youtu\.be\/([^?#]+)/,
        /youtube\.com\/embed\/([^?#]+)/,
        /youtube\.com\/shorts\/([^?#]+)/,
    ];
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match) return match[1];
    }
    return null;
}

export default function HomeHero({ hero, videoUrl }) {
    const videoId = extractYouTubeId(videoUrl);

    const backgroundStyle = !videoId && hero?.imageUrl
        ? { backgroundImage: `linear-gradient(rgba(29, 66, 138, 0.85), rgba(29, 66, 138, 0.95)), url(${hero.imageUrl})` }
        : undefined;

    return (
        <section
            className="relative flex min-h-screen items-center justify-start overflow-hidden bg-primary bg-cover bg-center pt-20"
            style={backgroundStyle}
        >
            {videoId && (
                <>
                    {/* YouTube iframe posicionado para cubrir la sección */}
                    <div className="absolute inset-0 z-0 overflow-hidden">
                        <iframe
                            src={`https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&controls=0&rel=0&showinfo=0&playlist=${videoId}&playsinline=1&modestbranding=1&disablekb=1&iv_load_policy=3&fs=0`}
                            title="Hero video"
                            allow="autoplay; loop"
                            style={{
                                position: 'absolute',
                                top: '50%',
                                left: '50%',
                                transform: 'translate(-50%, -50%)',
                                width: '177.78vh',
                                minWidth: '100%',
                                height: '56.25vw',
                                minHeight: '100%',
                                border: 'none',
                                pointerEvents: 'none',
                            }}
                        />
                    </div>
                    {/* Degradado idéntico al original */}
                    <div
                        className="absolute inset-0 z-[1]"
                        style={{ background: 'linear-gradient(rgba(29, 66, 138, 0.85), rgba(29, 66, 138, 0.95))' }}
                    />
                </>
            )}

            <div className="page-shell relative z-10 w-full">
                <div className="max-w-4xl">
                    <div className="mb-6 inline-flex items-center gap-4">
                        <span className="rounded-sm bg-accent px-4 py-1 text-xs font-bold uppercase tracking-widest text-white">
                            {hero.badge}
                        </span>
                    </div>
                    <h1 className="mb-8 font-display text-5xl font-black uppercase leading-tight tracking-tight text-white md:text-8xl">
                        {hero.title}
                        <br />
                        <span className="text-accent">{hero.highlight}</span>
                    </h1>
                    <p className="mb-10 max-w-2xl font-body text-xl text-white/90 md:text-2xl">
                        {hero.description}
                    </p>
                    <div className="flex flex-wrap gap-4">
                        <CTAButton
                            as={hero.primaryAction.href ? 'a' : 'button'}
                            href={hero.primaryAction.href ?? undefined}
                            type={hero.primaryAction.href ? undefined : 'button'}
                            size="lg"
                            className="group px-10 py-5 font-display shadow-xl"
                        >
                            {hero.primaryAction.label}
                            <span className="material-symbols-outlined transition-transform group-hover:translate-x-2">
                                confirmation_number
                            </span>
                        </CTAButton>
                        <CTAButton
                            as={hero.secondaryAction.href ? 'a' : 'button'}
                            href={hero.secondaryAction.href ?? undefined}
                            type={hero.secondaryAction.href ? undefined : 'button'}
                            variant="ghost"
                            size="lg"
                            className="border-2 border-white px-10 py-5 font-display hover:bg-white/10"
                        >
                            {hero.secondaryAction.label}
                        </CTAButton>
                    </div>
                </div>
            </div>
        </section>
    );
}
