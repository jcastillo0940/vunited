import { useLayoutSettings } from '@/context/LayoutContext';
import VideoBackground, { extractYouTubeId } from '@/components/common/VideoBackground';

const GRADIENT = 'linear-gradient(115deg, rgba(29,66,138,0.94) 0%, rgba(29,66,138,0.86) 52%, rgba(29,66,138,0.75) 100%)';

export default function SponsorsHero({ hero }) {
    const settings = useLayoutSettings();
    const videoId = extractYouTubeId(settings.sponsors_hero_video_url);

    return (
        <section className="relative overflow-hidden bg-primary pb-20 pt-40 md:pb-24 md:pt-48">
            {videoId ? (
                <VideoBackground videoId={videoId} gradient={GRADIENT} />
            ) : (
                <>
                    <img
                        src={hero.imageUrl}
                        alt={hero.title}
                        className="absolute inset-0 h-full w-full object-cover opacity-30"
                    />
                    <div className="absolute inset-0 bg-[linear-gradient(115deg,rgba(29,66,138,0.94)_0%,rgba(29,66,138,0.86)_52%,rgba(29,66,138,0.75)_100%)]" />
                </>
            )}

            <div className="page-shell relative z-10 max-w-7xl">
                <div className="max-w-4xl space-y-8">
                    <span className="inline-flex rounded-sm bg-accent px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-white">
                        {hero.badge}
                    </span>
                    <h1 className="font-display text-5xl font-bold uppercase leading-[0.9] tracking-tight text-white md:text-7xl lg:text-[5.5rem]">
                        {hero.title}
                    </h1>
                    <div className="max-w-2xl border-l-2 border-accent/80 pl-6">
                        <p className="text-base leading-8 text-white/85 md:text-lg">
                            {hero.description}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    );
}
