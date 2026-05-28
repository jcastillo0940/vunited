export default function AcademyStats({ stats }) {
    return (
        <section className="border-b border-outline bg-white py-16">
            <div className="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-margin-mobile md:grid-cols-2 md:px-margin-desktop xl:grid-cols-4">
                {stats.map((stat) => (
                    <article
                        key={stat.id}
                        className="flex flex-col items-center rounded-xl border border-outline bg-surface p-8 text-center shadow-sm transition-transform hover:-translate-y-1"
                    >
                        <span className="material-symbols-outlined mb-4 text-5xl text-accent">
                            {stat.icon}
                        </span>
                        <span className="font-display text-5xl font-bold text-primary">
                            {stat.value}
                        </span>
                        <span className="mt-2 text-xs font-bold uppercase tracking-widest text-text-main/60">
                            {stat.label}
                        </span>
                    </article>
                ))}
            </div>
        </section>
    );
}
