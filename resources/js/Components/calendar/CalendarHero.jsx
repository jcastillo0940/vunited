import { useLayoutSettings } from '@/context/LayoutContext';

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

export default function CalendarHero({ hero }) {
    const settings = useLayoutSettings();
    const videoId = extractYouTubeId(settings.hero_video_url);

    const backgroundStyle = !videoId && hero?.imageUrl
        ? { backgroundImage: `linear-gradient(135deg,rgba(29,66,138,0.95),rgba(29,66,138,0.72)), url(${hero.imageUrl})` }
        : undefined;

    return (
        <section
            className="relative flex min-h-[56vh] items-end overflow-hidden bg-primary pt-28"
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
                        style={{ background: 'linear-gradient(135deg,rgba(29,66,138,0.95),rgba(29,66,138,0.72))' }}
                    />
                </>
            )}

            <div className="page-shell relative z-10 mx-auto w-full max-w-7xl px-margin-mobile pb-16 md:px-margin-desktop md:pb-20">
                <div className="max-w-3xl">
                    <div className="mb-6 h-1.5 w-24 bg-accent" />
                    <h1 className="font-display text-5xl font-black uppercase leading-none tracking-tight text-white md:text-7xl">
                        {hero.title}
                        <br />
                        <span className="text-accent">{hero.highlight}</span>
                    </h1>
                    <p className="mt-6 max-w-2xl border-l-4 border-accent pl-6 text-lg leading-relaxed text-white/90 md:text-xl">
                        {hero.description}
                    </p>
                </div>
            </div>
        </section>
    );
}
