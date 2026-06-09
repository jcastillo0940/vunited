export default function PlayerBioCard({ player }) {
    const { profile } = player;

    // Split multiline biography into paragraphs
    const bioParagraphs = profile.biography
        ? profile.biography.split('\n').filter(Boolean)
        : [];

    const details = [
        profile.age           && { label: 'Edad',          value: `${profile.age} años` },
        profile.height        && { label: 'Estatura',       value: profile.height },
        player.nationality    && { label: 'Nacionalidad',   value: player.nationality },
        profile.dominantFoot  && { label: 'Pie dominante',  value: profile.dominantFoot },
        player.number         && { label: 'Dorsal',         value: `#${player.number}` },
        profile.team          && { label: 'Equipo',         value: profile.team },
    ].filter(Boolean);

    return (
        <section className="space-y-8 lg:sticky lg:top-28">
            <div className="flex items-center gap-4 border-b border-gray-200 pb-4">
                <h2 className="font-display text-3xl font-bold uppercase tracking-tight text-primary">
                    Perfil
                </h2>
            </div>

            <article className="space-y-8 rounded-xl border border-gray-200 bg-white p-8 shadow-md">
                {/* Bio paragraphs */}
                {bioParagraphs.length > 0 && (
                    <div className="space-y-2">
                        {bioParagraphs.map((line, i) => (
                            <p key={i} className="text-sm leading-relaxed text-gray-600">{line}</p>
                        ))}
                    </div>
                )}

                {/* Detail rows */}
                {details.length > 0 && (
                    <div className="space-y-4 border-t border-gray-100 pt-6">
                        {details.map(({ label, value }) => (
                            <DetailRow key={label} label={label} value={value} />
                        ))}
                    </div>
                )}

                {/* Social actions */}
                {profile.socialActions?.length > 0 && (
                    <div className="flex gap-3 border-t border-gray-100 pt-6">
                        {profile.socialActions.map((action) => (
                            <button
                                key={action.id}
                                type="button"
                                aria-label={action.label}
                                className="flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 text-gray-400 transition-all hover:border-accent hover:bg-accent hover:text-white"
                            >
                                <span className="material-symbols-outlined text-lg">{action.icon}</span>
                            </button>
                        ))}
                    </div>
                )}
            </article>
        </section>
    );
}

function DetailRow({ label, value }) {
    return (
        <div className="flex items-center justify-between gap-4">
            <span className="text-xs font-bold uppercase tracking-wider text-gray-400 shrink-0">{label}</span>
            <span className="text-right font-semibold text-text-main">{value}</span>
        </div>
    );
}
