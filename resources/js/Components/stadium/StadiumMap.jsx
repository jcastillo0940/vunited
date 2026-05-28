import CTAButton from '@/components/common/CTAButton';

export default function StadiumMap({ map }) {
    return (
        <section className="section-space bg-background">
            <div className="page-shell max-w-7xl">
                <div className="grid grid-cols-1 gap-10 lg:grid-cols-[minmax(0,1fr)_360px]">
                    <div className="rounded-xl border border-slate-200 bg-surface p-6 shadow-md">
                        {map.embedUrl ? (
                            <iframe
                                src={map.embedUrl}
                                title={map.title}
                                className="h-full min-h-[280px] w-full rounded-xl border-0"
                                loading="lazy"
                                allowFullScreen
                            />
                        ) : (
                            <div className="relative flex aspect-[16/9] items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-white">
                                <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(91,194,231,0.16),transparent_58%)]" />
                                <div className="relative z-10 text-center">
                                    <div className="mx-auto grid h-20 w-20 place-items-center rounded-full bg-primary text-white shadow-lg">
                                        <span className="material-symbols-outlined text-4xl">location_on</span>
                                    </div>
                                    <p className="mt-4 font-display text-3xl font-black uppercase text-primary">
                                        {map.pinLabel}
                                    </p>
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="rounded-xl border border-slate-200 bg-white p-8 shadow-md">
                        <p className="text-xs font-bold uppercase tracking-[0.28em] text-accent">
                            Cómo llegar
                        </p>
                        <h3 className="mt-3 font-display text-4xl font-black uppercase text-primary">
                            {map.title}
                        </h3>
                        <p className="mt-5 text-base leading-7 text-slate-500">{map.description}</p>

                        <CTAButton
                            as="a"
                            href={map.actionHref}
                            size="lg"
                            className="mt-8 w-full justify-center font-display text-lg tracking-[0.14em]"
                        >
                            {map.actionLabel}
                        </CTAButton>
                    </div>
                </div>
            </div>
        </section>
    );
}
