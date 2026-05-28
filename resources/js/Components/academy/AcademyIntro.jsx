export default function AcademyIntro({ intro }) {
    return (
        <section className="section-space">
            <div className="page-shell max-w-7xl">
                <div className="grid items-center gap-10 lg:grid-cols-[1fr_0.95fr]">
                    <div className="space-y-5">
                        <p className="display-kicker">{intro.eyebrow}</p>
                        <h2 className="font-display text-4xl font-bold uppercase tracking-tight text-primary md:text-5xl">
                            {intro.title}
                        </h2>
                        <p className="max-w-2xl text-lg leading-relaxed text-gray-600">
                            {intro.description}
                        </p>
                    </div>
                    <div className="overflow-hidden rounded-[28px] border border-primary/10 bg-white shadow-panel">
                        <img
                            alt={intro.title}
                            className="aspect-[5/4] h-full w-full object-cover"
                            src={intro.imageUrl}
                        />
                    </div>
                </div>
            </div>
        </section>
    );
}
