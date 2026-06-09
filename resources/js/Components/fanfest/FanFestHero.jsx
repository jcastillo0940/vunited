import { useLayoutSettings } from '@/context/LayoutContext';
import VideoBackground, { extractYouTubeId } from '@/components/common/VideoBackground';

const GRADIENT = 'linear-gradient(120deg, rgba(29,66,138,0.95) 0%, rgba(29,66,138,0.85) 100%)';

export default function FanFestHero({ event, dateLabel }) {
    const settings = useLayoutSettings();
    const videoId = extractYouTubeId(settings.fanfest_hero_video_url);

    return (
        <section className="relative overflow-hidden bg-primary pb-20 pt-40 md:pb-28 md:pt-52">
            {videoId ? (
                <VideoBackground videoId={videoId} gradient={GRADIENT} />
            ) : (
                <>
                    {event.hero_image_path && (
                        <img
                            src={event.hero_image_path}
                            alt={event.title}
                            className="absolute inset-0 h-full w-full object-cover opacity-30"
                        />
                    )}
                    <div className="absolute inset-0 bg-[linear-gradient(120deg,rgba(29,66,138,0.95)_0%,rgba(29,66,138,0.85)_100%)]" />
                </>
            )}

            <div className="page-shell relative z-10 max-w-7xl">
                <span className="inline-flex rounded-sm bg-accent px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-white">
                    FANFEST 2026
                </span>

                <h1 className="mt-6 font-display text-5xl font-bold uppercase leading-[0.9] text-white md:text-7xl lg:text-8xl">
                    {event.title}
                </h1>

                {(dateLabel || event.location) && (
                    <div className="mt-8 flex flex-wrap gap-6 text-sm font-bold uppercase tracking-[0.2em] text-white/70">
                        {dateLabel && (
                            <span className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent text-lg">calendar_today</span>
                                {dateLabel}
                            </span>
                        )}
                        {event.location && (
                            <span className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent text-lg">location_on</span>
                                {event.location}
                            </span>
                        )}
                    </div>
                )}

                {event.description && (
                    <p className="mt-8 max-w-2xl border-l-2 border-accent/80 pl-6 text-base leading-8 text-white/80">
                        {event.description}
                    </p>
                )}

                <div className="mt-10">
                    <a
                        href="/registro-tribu"
                        className="inline-flex items-center gap-2 rounded-md bg-accent px-8 py-4 font-display text-lg font-bold uppercase tracking-[0.15em] text-white transition hover:bg-white hover:text-primary"
                    >
                        <span className="material-symbols-outlined text-xl">confirmation_number</span>
                        Unirme a La Tribu
                    </a>
                </div>
            </div>
        </section>
    );
}
