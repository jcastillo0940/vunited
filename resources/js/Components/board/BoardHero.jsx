export default function BoardHero({ hero }) {
    return (
        <section className="relative flex h-[500px] items-center overflow-hidden bg-primary pt-28">
            <img
                src={hero.imageUrl}
                alt={hero.title}
                className="absolute inset-0 h-full w-full object-cover opacity-20"
            />
            <div className="absolute inset-0 bg-[linear-gradient(180deg,rgba(29,66,138,0.84),rgba(29,66,138,0.95))]" />

            <div className="page-shell relative z-10 mx-auto max-w-7xl">
                <div className="max-w-3xl">
                    <span className="mb-6 inline-block rounded-sm bg-accent px-4 py-1 text-xs font-bold uppercase tracking-[0.3em] text-white">
                        {hero.badge}
                    </span>
                    <h1 className="font-display text-5xl font-bold uppercase tracking-tight text-white md:text-7xl">
                        {hero.title} <span className="text-accent">{hero.highlight}</span>
                    </h1>
                    <p className="mt-6 max-w-2xl border-l-4 border-accent pl-6 text-xl leading-relaxed text-white/90">
                        {hero.description}
                    </p>
                </div>
            </div>
        </section>
    );
}
