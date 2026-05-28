export default function StadiumRules({ rules }) {
    return (
        <section className="section-space bg-surface">
            <div className="page-shell max-w-7xl">
                <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-md md:p-12">
                    <div className="mb-10">
                        <p className="text-sm font-bold uppercase tracking-[0.3em] text-accent">
                            Reglas y recomendaciones
                        </p>
                        <h2 className="mt-2 font-display text-5xl font-black uppercase text-primary">
                            Antes de ingresar
                        </h2>
                    </div>

                    <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                        {rules.map((rule) => (
                            <div key={rule} className="flex items-start gap-4 rounded-lg border border-slate-100 bg-surface px-5 py-5">
                                <span className="material-symbols-outlined text-accent">verified</span>
                                <p className="text-sm leading-6 text-text-main">{rule}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
