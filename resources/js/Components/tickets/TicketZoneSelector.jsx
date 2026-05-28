function zoneCardClasses(zone, isActive) {
    if (isActive) {
        return 'border-primary shadow-panel';
    }

    return zone.tone === 'featured'
        ? 'border-slate-200 hover:border-primary hover:shadow-card'
        : 'border-slate-100 hover:border-accent hover:shadow-card';
}

export default function TicketZoneSelector({ zones, selectedZoneId, onSelectZone }) {
    const selectedZone = zones.find((zone) => zone.id === selectedZoneId) ?? zones[0];

    return (
        <section className="rounded-xl border border-slate-100 bg-white p-8 shadow-card">
            <h2 className="mb-8 flex items-center gap-3 font-display text-3xl font-bold uppercase text-primary">
                <span className="material-symbols-outlined text-accent">event_seat</span>
                Selecciona tu zona
            </h2>

            <div className="mb-10 rounded-xl border-2 border-slate-200 bg-surface p-8 text-center">
                <div className="relative flex aspect-video w-full flex-col items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-white">
                    <div className="grid h-1/2 w-4/5 grid-cols-3 gap-4 md:gap-6">
                        {zones.map((zone) => (
                            <button
                                key={zone.id}
                                type="button"
                                disabled={zone.outOfStock}
                                onClick={() => onSelectZone(zone.id)}
                                className={`rounded-lg border-2 px-3 text-[10px] font-bold uppercase shadow-sm transition md:text-xs ${
                                    selectedZoneId === zone.id
                                        ? 'border-primary bg-accent/5 text-primary'
                                        : zone.outOfStock
                                            ? 'cursor-not-allowed border-slate-200 text-slate-300'
                                            : 'border-slate-200 text-slate-400 hover:border-accent hover:bg-accent/5'
                                }`}
                            >
                                {zone.name} ${zone.price}
                            </button>
                        ))}
                    </div>
                    <div className="absolute inset-x-0 bottom-0 flex h-1/4 items-center justify-center border-t border-slate-200 bg-slate-100 font-display text-xl tracking-[0.28em] text-slate-300">
                        CAMPO DE JUEGO
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                {zones.map((zone) => {
                    const isActive = selectedZoneId === zone.id;

                    return (
                        <button
                            key={zone.id}
                            type="button"
                            disabled={zone.outOfStock}
                            onClick={() => onSelectZone(zone.id)}
                            className={`rounded-lg border-2 bg-white p-6 text-left shadow-sm transition ${zoneCardClasses(zone, isActive)} ${zone.outOfStock ? 'cursor-not-allowed opacity-60' : ''}`}
                        >
                            <div className="mb-6 flex items-start justify-between gap-4">
                                <span
                                    className={`rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] ${
                                        isActive || zone.tone === 'featured'
                                            ? 'bg-accent text-white'
                                            : 'bg-surface text-slate-500'
                                    }`}
                                >
                                    {zone.area}
                                </span>
                                <span className="font-display text-2xl font-bold text-primary">
                                    ${zone.price}
                                </span>
                            </div>
                            <h3 className="font-display text-xl font-bold uppercase text-primary">
                                {zone.displayName}
                            </h3>
                            <p className="mt-2 text-sm leading-6 text-slate-500">
                                {zone.description}
                            </p>
                            {zone.availableQuantity !== null ? (
                                <p className="mt-4 text-xs font-bold uppercase tracking-[0.24em] text-slate-400">
                                    Disponibles: {zone.availableQuantity}
                                </p>
                            ) : null}
                            {zone.outOfStock ? (
                                <p className="mt-2 text-xs font-bold uppercase tracking-[0.24em] text-rose-500">
                                    Agotado
                                </p>
                            ) : null}
                            {isActive ? (
                                <p className="mt-4 text-xs font-bold uppercase tracking-[0.24em] text-accent">
                                    Zona seleccionada
                                </p>
                            ) : null}
                        </button>
                    );
                })}
            </div>

            <p className="mt-6 text-sm text-slate-500">
                Seleccion actual: <span className="font-bold text-primary">{selectedZone.displayName}</span>
            </p>
        </section>
    );
}
