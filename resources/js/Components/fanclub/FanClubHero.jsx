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

export default function FanClubHero({ hero, videoUrl }) {
    const videoId = extractYouTubeId(videoUrl);

    const backgroundStyle = !videoId && hero?.imageUrl
        ? { backgroundImage: `linear-gradient(rgba(29,66,138,0.8),rgba(29,66,138,0.42)), url(${hero.imageUrl})` }
        : undefined;

    return (
        <section
            className="relative flex min-h-[70vh] items-center overflow-hidden pt-24"
            style={backgroundStyle}
        >
            {videoId && (
                <>
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
                    <div
                        className="absolute inset-0 z-[1]"
                        style={{ background: 'linear-gradient(rgba(29,66,138,0.8),rgba(29,66,138,0.42))' }}
                    />
                </>
            )}

            {!videoId && !hero?.imageUrl && (
                <div className="absolute inset-0 z-0 bg-primary" />
            )}

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
