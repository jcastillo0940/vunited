export default function PlayerBioCard({ player }) {
    return (
        <section className="space-y-12">
            <div className="flex items-center gap-4 border-b border-gray-200 pb-4">
                <h2 className="font-display text-3xl font-bold uppercase tracking-tight text-primary">
                    Biografia
                </h2>
            </div>
            <article className="space-y-8 rounded-xl border border-gray-200 bg-white p-10 shadow-md">
                <p className="text-lg leading-relaxed text-gray-600">{player.profile.biography}</p>

                <div className="space-y-5 border-t border-gray-100 pt-6">
                    <DetailRow label="Edad" value={`${player.profile.age} Anos`} />
                    <DetailRow label="Estatura" value={player.profile.height} />
                    <DetailRow label="Nacionalidad" value={player.nationality} />
                    <DetailRow label="Pie Dominante" value={player.profile.dominantFoot} />
                </div>

                <div className="flex gap-4 pt-6">
                    {player.profile.socialActions.map((action) => (
                        <button
                            key={action.id}
                            type="button"
                            aria-label={action.label}
                            className="flex h-12 w-12 items-center justify-center rounded-full border border-gray-200 text-gray-400 transition-all hover:border-accent hover:bg-accent hover:text-white"
                        >
                            <span className="material-symbols-outlined text-lg">{action.icon}</span>
                        </button>
                    ))}
                </div>
            </article>
        </section>
    );
}

function DetailRow({ label, value }) {
    return (
        <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-gray-400">{label}</span>
            <span className="font-bold text-text-main">{value}</span>
        </div>
    );
}
