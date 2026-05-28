export default function TicketQuantitySelector({ quantity, limit, onDecrease, onIncrease }) {
    return (
        <section className="rounded-xl border border-slate-200 bg-surface p-8">
            <h2 className="mb-8 flex items-center gap-3 font-display text-3xl font-bold uppercase text-primary">
                <span className="material-symbols-outlined text-accent">confirmation_number</span>
                Cantidad de boletos
            </h2>

            <div className="flex flex-col gap-6 md:flex-row md:items-center md:gap-10">
                <div className="flex items-center rounded-lg border border-slate-200 bg-white shadow-sm">
                    <button
                        type="button"
                        onClick={onDecrease}
                        className="flex h-16 w-16 items-center justify-center text-slate-400 transition hover:text-accent"
                        aria-label="Reducir cantidad"
                    >
                        <span className="material-symbols-outlined text-3xl">remove</span>
                    </button>
                    <span className="min-w-[80px] text-center font-display text-5xl text-primary">
                        {quantity}
                    </span>
                    <button
                        type="button"
                        onClick={onIncrease}
                        className="flex h-16 w-16 items-center justify-center text-slate-400 transition hover:text-accent"
                        aria-label="Aumentar cantidad"
                    >
                        <span className="material-symbols-outlined text-3xl">add</span>
                    </button>
                </div>

                <div>
                    <p className="mb-1 text-xs font-bold uppercase tracking-[0.28em] text-primary">
                        Capacidad maxima
                    </p>
                    <p className="text-sm text-slate-500">
                        Máximo {limit} boletos por pedido.
                    </p>
                </div>
            </div>
        </section>
    );
}
