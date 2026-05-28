import CTAButton from '@/components/common/CTAButton';

export default function TicketSuccessMock({ successTicket, match, selectedZone, quantity, total, onReset }) {
    return (
        <section className="mx-auto max-w-3xl px-margin-mobile md:px-margin-desktop">
            <div className="mb-12 text-center">
                <div className="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-accent text-white shadow-xl">
                    <span className="material-symbols-outlined text-6xl">check_circle</span>
                </div>
                <h2 className="mb-4 font-display text-5xl font-black uppercase italic text-primary">
                    {successTicket.title}
                </h2>
                <p className="mx-auto max-w-2xl text-base leading-7 text-slate-500">
                    {successTicket.subtitle}
                </p>
            </div>

            <div className="overflow-hidden rounded-2xl border-2 border-slate-100 bg-white shadow-2xl">
                <div className="flex items-center justify-between bg-primary p-6">
                    <div className="flex items-center gap-3">
                        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 font-display text-sm font-bold text-white">
                            VU
                        </div>
                        <div>
                            <h3 className="font-display text-xl font-black uppercase italic text-white">
                                VERAGUAS UNITED
                            </h3>
                            <p className="mt-1 text-[9px] font-bold uppercase tracking-[0.2em] text-accent">
                                {successTicket.ticketType}
                            </p>
                        </div>
                    </div>
                    <span className="material-symbols-outlined text-4xl text-white/50">
                        confirmation_number
                    </span>
                </div>

                <div className="p-8 md:p-10">
                    <div className="grid grid-cols-1 gap-8 border-b border-slate-100 pb-8 md:grid-cols-2">
                        <div>
                            <p className="mb-2 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">
                                Partido
                            </p>
                            <p className="font-display text-3xl font-bold text-primary">
                                {match.homeTeam} VS {match.awayTeam}
                            </p>
                        </div>
                        <div className="md:text-right">
                            <p className="mb-2 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">
                                Fecha y hora
                            </p>
                            <p className="font-display text-3xl font-bold text-primary">
                                {match.dateLabel}
                            </p>
                            <p className="font-display text-xl uppercase text-slate-500">
                                {match.timeLabel}
                            </p>
                        </div>
                    </div>

                    <div className="mt-8 grid grid-cols-1 gap-8 md:grid-cols-[minmax(0,1fr)_220px]">
                        <div className="grid grid-cols-2 gap-6">
                            <div>
                                <p className="mb-2 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">
                                    Zona
                                </p>
                                <p className="font-display text-2xl font-bold text-primary">
                                    {selectedZone.displayName}
                                </p>
                            </div>
                            <div>
                                <p className="mb-2 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">
                                    Cantidad
                                </p>
                                <p className="font-display text-2xl font-bold text-primary">
                                    x{quantity}
                                </p>
                            </div>
                            <div>
                                <p className="mb-2 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">
                                    Acceso
                                </p>
                                <p className="font-display text-2xl font-bold text-primary">
                                    {successTicket.seatLabel}
                                </p>
                            </div>
                            <div>
                                <p className="mb-2 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">
                                    Total
                                </p>
                                <p className="font-display text-2xl font-bold text-primary">
                                    ${total.toFixed(2)}
                                </p>
                            </div>
                            <div className="col-span-2">
                                <p className="mb-2 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">
                                    Puerta
                                </p>
                                <p className="font-display text-2xl font-bold text-primary">
                                    {successTicket.gate}
                                </p>
                            </div>
                        </div>

                        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-surface p-6 text-center">
                            <div className="grid h-36 w-36 place-items-center rounded-lg bg-white shadow-sm">
                                <div className="space-y-1">
                                    <div className="grid grid-cols-6 gap-1">
                                        {Array.from({ length: 36 }).map((_, index) => (
                                            <span
                                                key={index}
                                                className={`h-3 w-3 rounded-[2px] ${
                                                    index % 2 === 0 || index % 5 === 0
                                                        ? 'bg-primary'
                                                        : 'bg-accent/30'
                                                }`}
                                            />
                                        ))}
                                    </div>
                                </div>
                            </div>
                            <p className="mt-4 text-xs font-bold uppercase tracking-[0.24em] text-slate-400">
                                {successTicket.qrLabel}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="mt-10 flex flex-col justify-center gap-4 md:flex-row">
                <CTAButton variant="outline" onClick={onReset}>
                    Volver a editar
                </CTAButton>
                <CTAButton href="/" variant="primary">
                    VOLVER AL INICIO
                </CTAButton>
            </div>
        </section>
    );
}
