export default function BoardMembersGrid({ members }) {
    return (
        <section className="section-space border-y border-gray-100 bg-background">
            <div className="page-shell max-w-7xl">
                <div className="mb-16 text-center">
                    <h2 className="font-display text-4xl font-bold uppercase tracking-tight text-primary">
                        Junta Directiva <span className="text-accent">&amp; Accionistas</span>
                    </h2>
                    <p className="mt-4 text-lg text-gray-500">
                        El respaldo solido que impulsa nuestro crecimiento institucional.
                    </p>
                </div>

                <div className="grid grid-cols-2 gap-6 md:grid-cols-4">
                    {members.map((member) => (
                        <article
                            key={member.id}
                            className="group rounded-xl border-t-4 border-primary bg-surface p-6 transition-all hover:bg-white hover:shadow-md"
                        >
                            <h3 className="truncate font-display text-lg font-bold uppercase text-primary group-hover:text-accent">
                                {member.name}
                            </h3>
                            <p className="mt-1 text-[10px] font-bold uppercase tracking-[0.24em] text-gray-500">
                                {member.role}
                            </p>
                            <p className="mt-4 text-xs font-semibold uppercase tracking-[0.2em] text-primary/60">
                                {member.category}
                            </p>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
