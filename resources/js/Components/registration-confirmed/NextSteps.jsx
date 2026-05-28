import CTAButton from '@/components/common/CTAButton';

export default function NextSteps({ steps, actions }) {
    return (
        <section className="w-full max-w-5xl">
            <div className="mb-10 text-center">
                <h2 className="font-display text-4xl font-bold uppercase tracking-tight text-primary">
                    Proximos Pasos
                </h2>
                <p className="mt-3 text-text-main/70">
                    Tu registro es visual por ahora, pero este es el flujo esperado cuando la base real este activa.
                </p>
            </div>

            <div className="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-5">
                {steps.map((step) => (
                    <article key={step.id} className="rounded-xl border border-outline bg-white p-6 text-left shadow-sm">
                        <span className="material-symbols-outlined mb-4 text-3xl text-accent">
                            {step.icon}
                        </span>
                        <h3 className="font-display text-lg uppercase text-primary">{step.title}</h3>
                        <p className="mt-3 text-sm leading-relaxed text-text-main/70">{step.description}</p>
                    </article>
                ))}
            </div>

            <div className="mt-12 flex flex-col gap-4 md:flex-row md:justify-center">
                <CTAButton type="button" size="lg" className="justify-center font-label-bold text-base shadow-md">
                    {actions.download.label}
                </CTAButton>
                {actions.account.href ? (
                    <CTAButton
                        as="a"
                        href={actions.account.href}
                        variant="outline"
                        size="lg"
                        className="justify-center border-outline bg-white font-label-bold text-base hover:border-primary hover:bg-surface"
                    >
                        {actions.account.label}
                    </CTAButton>
                ) : (
                    <CTAButton
                        type="button"
                        variant="outline"
                        size="lg"
                        className="justify-center border-outline bg-white font-label-bold text-base opacity-70"
                    >
                        {actions.account.label}
                    </CTAButton>
                )}
                <CTAButton
                    as="a"
                    href={actions.home.href}
                    variant="secondary"
                    size="lg"
                    className="justify-center font-label-bold text-base"
                >
                    {actions.home.label}
                </CTAButton>
            </div>

            <p className="mt-6 text-center text-[11px] font-medium uppercase tracking-wide text-text-main/50">
                No hay descarga real, no hay pago real y no existe persistencia en esta pantalla.
            </p>
            {!actions.account.href ? (
                <p className="mt-2 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-text-main/40">
                    Mi cuenta estara disponible cuando exista la base real de membresias.
                </p>
            ) : null}
        </section>
    );
}
