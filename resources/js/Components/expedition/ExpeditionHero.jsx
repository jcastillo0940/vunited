import { useLayoutSettings } from '@/context/LayoutContext';
import VideoBackground, { extractYouTubeId } from '@/components/common/VideoBackground';

const GRADIENT = 'linear-gradient(120deg, rgba(29,66,138,0.97) 0%, rgba(29,66,138,0.88) 100%)';

export default function ExpeditionHero() {
    const settings = useLayoutSettings();
    const videoId = extractYouTubeId(settings.expedition_hero_video_url);

    return (
        <section className="relative overflow-hidden bg-primary pb-20 pt-40 md:pb-28 md:pt-52">
            {videoId ? (
                <VideoBackground videoId={videoId} gradient={GRADIENT} />
            ) : (
                <div className="absolute inset-0 bg-[linear-gradient(120deg,rgba(29,66,138,0.97)_0%,rgba(29,66,138,0.88)_100%)]" />
            )}

            <div className="page-shell relative z-10 max-w-7xl">
                <span className="inline-flex rounded-sm bg-accent px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-white">
                    EXPEDICIÓN INDIANA
                </span>

                <h1 className="mt-6 font-display text-5xl font-bold uppercase leading-[0.9] text-white md:text-7xl lg:text-8xl">
                    VIAJA CON<br />
                    <span className="text-accent">LA TRIBU</span>
                </h1>

                <p className="mt-8 max-w-2xl border-l-2 border-accent/80 pl-6 text-base leading-8 text-white/80">
                    Buses organizados por el club para acompañar a los Indios en cada partido de visitante. Únete a la caravana india y vive el fútbol en su máxima expresión.
                </p>

                <div className="mt-8 flex flex-wrap gap-4 text-sm font-bold uppercase tracking-[0.2em] text-white/60">
                    <span className="flex items-center gap-2">
                        <span className="material-symbols-outlined text-accent text-lg">directions_bus</span>
                        Transporte ida y vuelta
                    </span>
                    <span className="flex items-center gap-2">
                        <span className="material-symbols-outlined text-accent text-lg">ac_unit</span>
                        Bus con A/C
                    </span>
                    <span className="flex items-center gap-2">
                        <span className="material-symbols-outlined text-accent text-lg">groups</span>
                        Comunidad india
                    </span>
                </div>
            </div>
        </section>
    );
}
