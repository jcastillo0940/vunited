import CTAButton from '@/components/common/CTAButton';

export default function StadiumInfo({ info }) {
    return (
        <section className="relative z-20 -mt-12 px-margin-mobile md:px-margin-desktop">
            <div className="mx-auto max-w-7xl rounded-xl bg-white px-8 py-10 shadow-2xl md:px-12 md:py-12">
                <div className="grid grid-cols-1 gap-10 lg:grid-cols-[minmax(0,1fr)_280px]">
                    <div>
                        <p className="text-xs font-bold uppercase tracking-[0.28em] text-accent">
                            Informacion principal
                        </p>
                        <h2 className="mt-3 font-display text-5xl font-black uppercase text-primary">
                            {info.name}
                        </h2>
                        <p className="mt-3 text-lg font-semibold text-slate-500">{info.subtitle}</p>

                        <div className="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <InfoRow icon="location_on" label="Ubicacion" value={info.location} />
                            <InfoRow icon="groups" label="Capacidad" value={info.capacity} />
                            <InfoRow icon="stadium" label="Sede" value={info.venueType} />
                            <InfoRow icon="route" label="Direccion" value={info.address} />
                        </div>
                    </div>

                    <div className="rounded-xl border border-slate-100 bg-surface p-8 shadow-sm">
                        <div className="mb-8 grid h-40 place-items-center rounded-xl bg-white shadow-inner">
                            <div className="text-center">
                                <span className="material-symbols-outlined text-6xl text-accent">stadium</span>
                                <p className="mt-3 text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">
                                    Casa del Veraguas United
                                </p>
                            </div>
                        </div>

                        <CTAButton
                            as="a"
                            href={info.actionHref}
                            variant="secondary"
                            size="lg"
                            className="w-full justify-center font-display text-lg tracking-[0.14em]"
                        >
                            {info.actionLabel}
                        </CTAButton>
                    </div>
                </div>
            </div>
        </section>
    );
}

function InfoRow({ icon, label, value }) {
    return (
        <div className="rounded-lg border border-slate-100 bg-surface px-5 py-5">
            <div className="flex items-start gap-4">
                <span className="material-symbols-outlined text-accent">{icon}</span>
                <div>
                    <p className="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">
                        {label}
                    </p>
                    <p className="mt-2 font-semibold text-text-main">{value}</p>
                </div>
            </div>
        </div>
    );
}
