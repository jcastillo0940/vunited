export default function PlayerStats({ stats }) {
    return (
        <div className="grid grid-cols-2 gap-6 md:grid-cols-4">
            {stats.map((stat) => (
                <article
                    key={stat.key}
                    className="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-md transition-colors hover:border-accent"
                >
                    <p className="mb-4 text-xs font-bold uppercase tracking-widest text-gray-400">
                        {stat.label}
                    </p>
                    <p
                        className={[
                            'font-display text-5xl font-bold',
                            stat.tone === 'accent'
                                ? 'text-accent'
                                : stat.tone === 'neutral'
                                  ? 'text-text-main'
                                  : 'text-primary',
                        ].join(' ')}
                    >
                        {stat.value}
                    </p>
                </article>
            ))}
        </div>
    );
}
