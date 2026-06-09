import { Link } from '@inertiajs/react';
import { useLayoutSettings } from '@/context/LayoutContext';
import VideoBackground, { extractYouTubeId } from '@/components/common/VideoBackground';

const GRADIENT = 'linear-gradient(rgba(29,66,138,0.85), rgba(29,66,138,0.95))';

export default function NewsHero() {
    const settings = useLayoutSettings();
    const videoId = extractYouTubeId(settings.news_hero_video_url);

    if (videoId) {
        return (
            <section className="relative overflow-hidden bg-primary pb-12 pt-40">
                <VideoBackground videoId={videoId} gradient={GRADIENT} />
                <div className="page-shell relative z-10 max-w-7xl">
                    <p className="text-xs font-bold uppercase tracking-[0.3em] text-accent">
                        newsroom
                    </p>
                    <div className="mt-5 flex flex-col gap-6 border-b border-white/20 pb-8 lg:flex-row lg:items-end lg:justify-between">
                        <div className="max-w-3xl">
                            <h1 className="font-display text-5xl font-bold uppercase tracking-tight text-white md:text-7xl">
                                CENTRO DE NOTICIAS
                            </h1>
                            <p className="mt-4 text-lg leading-relaxed text-white/80">
                                Actualidad, fichajes, cronicas y vida institucional del club en una sola portada editorial.
                            </p>
                        </div>
                        <Link
                            href="/"
                            className="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-white"
                        >
                            Volver al inicio
                            <span className="material-symbols-outlined text-sm">arrow_back</span>
                        </Link>
                    </div>
                </div>
            </section>
        );
    }

    return (
        <section className="bg-surface pb-12 pt-40">
            <div className="page-shell max-w-7xl">
                <p className="text-xs font-bold uppercase tracking-[0.3em] text-accent">
                    newsroom
                </p>
                <div className="mt-5 flex flex-col gap-6 border-b border-gray-200 pb-8 lg:flex-row lg:items-end lg:justify-between">
                    <div className="max-w-3xl">
                        <h1 className="font-display text-5xl font-bold uppercase tracking-tight text-primary md:text-7xl">
                            CENTRO DE NOTICIAS
                        </h1>
                        <p className="mt-4 text-lg leading-relaxed text-gray-600">
                            Actualidad, fichajes, cronicas y vida institucional del club en una sola portada editorial.
                        </p>
                    </div>
                    <Link
                        href="/"
                        className="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                    >
                        Volver al inicio
                        <span className="material-symbols-outlined text-sm">arrow_back</span>
                    </Link>
                </div>
            </div>
        </section>
    );
}
