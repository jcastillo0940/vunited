export default function StaffGrid({ staff }) {
    return (
        <section className="mb-24 rounded-xl bg-surface p-10 md:p-16">
            <h2 className="mb-10 flex items-center gap-4 font-display text-3xl font-bold uppercase text-primary">
                Cuerpo tecnico
                <div className="h-px flex-grow bg-gray-300" />
            </h2>

            <div className="grid grid-cols-1 gap-gutter md:grid-cols-12">
                <article className="flex flex-col items-center gap-10 rounded-xl border border-gray-100 bg-white p-10 shadow-md md:col-span-8 md:flex-row">
                    <img
                        alt={staff.featured.name}
                        className="h-56 w-56 rounded-lg border border-gray-100 object-cover shadow-sm"
                        src={staff.featured.imageUrl}
                    />
                    <div className="text-center md:text-left">
                        <span className="mb-2 block text-xs font-bold uppercase tracking-widest text-accent">
                            {staff.featured.role}
                        </span>
                        <h3 className="mb-6 font-display text-4xl font-bold uppercase text-primary md:text-5xl">
                            {staff.featured.firstName}{' '}
                            <span className="text-text-main">{staff.featured.lastName}</span>
                        </h3>
                        <p className="max-w-lg text-base leading-relaxed text-text-main/70">
                            {staff.featured.description}
                        </p>
                    </div>
                </article>

                <div className="grid gap-gutter md:col-span-4">
                    {staff.assistants.map((member) => (
                        <article
                            key={member.id}
                            className="flex flex-col items-center rounded-xl border border-gray-100 bg-white p-8 text-center shadow-md transition-shadow hover:shadow-lg"
                        >
                            <img
                                alt={member.name}
                                className="mb-6 h-32 w-32 rounded-full border-4 border-surface object-cover"
                                src={member.imageUrl}
                            />
                            <span className="mb-1 text-[10px] font-bold uppercase tracking-[0.2em] text-text-main/50">
                                {member.role}
                            </span>
                            <h4 className="font-display text-2xl font-bold uppercase text-primary">
                                {member.name}
                            </h4>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
