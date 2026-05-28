export default function BenefitsSummary({ benefits }) {
    return (
        <section className="mb-16 grid w-full grid-cols-1 gap-gutter text-left md:grid-cols-3">
            {benefits.slice(0, 3).map((benefit) => (
                <article key={benefit.id} className="rounded-xl border border-outline bg-surface p-8 shadow-sm">
                    <span className="material-symbols-outlined mb-4 text-3xl text-accent">
                        {benefit.icon}
                    </span>
                    <h3 className="mb-2 font-display text-lg uppercase text-primary">{benefit.title}</h3>
                    <p className="text-text-main/70">{benefit.description}</p>
                </article>
            ))}
        </section>
    );
}
