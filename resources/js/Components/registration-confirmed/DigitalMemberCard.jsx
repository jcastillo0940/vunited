export default function DigitalMemberCard({ card }) {
    return (
        <section className="mb-12 w-full max-w-2xl overflow-hidden rounded-xl border border-outline bg-white p-6 shadow-md md:p-12">
            <div className="grid items-center gap-8 md:grid-cols-2">
                <div className="text-left">
                    <div className="mb-8 flex items-center gap-4">
                        <div className="flex h-16 w-16 items-center justify-center rounded-xl bg-primary text-2xl font-black text-white shadow-sm">
                            {card.crestLabel}
                        </div>
                        <div>
                            <h2 className="font-display text-2xl leading-tight text-primary">
                                {card.membershipTitle}
                            </h2>
                            <p className="text-[12px] font-bold uppercase tracking-[0.18em] text-accent">
                                {card.membershipSubtitle}
                            </p>
                        </div>
                    </div>

                    <div className="space-y-6">
                        <div>
                            <label className="mb-1 block text-[10px] font-bold uppercase tracking-[0.2em] text-text-main/50">
                                Nombre del Socio
                            </label>
                            <p className="font-display text-2xl text-text-main">{card.memberName}</p>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="mb-1 block text-[10px] font-bold uppercase tracking-[0.2em] text-text-main/50">
                                    ID Membresia
                                </label>
                                <p className="text-lg font-bold text-primary">{card.membershipId}</p>
                            </div>
                            <div>
                                <label className="mb-1 block text-[10px] font-bold uppercase tracking-[0.2em] text-text-main/50">
                                    Desde
                                </label>
                                <p className="text-lg font-bold text-primary">{card.validFrom}</p>
                            </div>
                        </div>
                        <div>
                            <label className="mb-1 block text-[10px] font-bold uppercase tracking-[0.2em] text-text-main/50">
                                Vencimiento
                            </label>
                            <p className="text-lg font-bold text-primary">{card.validUntil}</p>
                        </div>
                    </div>
                </div>

                <div className="rounded-xl border border-outline bg-surface p-6">
                    <img
                        src={card.qrImageUrl}
                        alt="QR acceso mock"
                        className="aspect-square w-full rounded-lg bg-white object-cover p-2 shadow-sm"
                    />
                    <p className="mt-4 text-center text-[11px] font-bold uppercase leading-tight text-primary">
                        {card.accessLabel}
                    </p>
                </div>
            </div>
        </section>
    );
}
