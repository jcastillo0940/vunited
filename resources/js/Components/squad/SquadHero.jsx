export default function SquadHero({ hero }) {
    return (
        <section className="relative overflow-hidden bg-primary pb-16 pt-40 md:pb-20 md:pt-44">
            <img
                src={hero.imageUrl}
                alt={hero.highlight}
                className="absolute inset-0 h-full w-full object-cover opacity-25"
            />
            <div className="absolute inset-0 bg-[linear-gradient(115deg,rgba(29,66,138,0.94)_0%,rgba(29,66,138,0.82)_58%,rgba(29,66,138,0.74)_100%)]" />

            <div className="page-shell relative z-10 max-w-7xl">
                <div className="max-w-4xl">
                    <div className="mb-4 inline-block border-l-4 border-accent pl-4">
                        <span className="text-sm font-bold uppercase tracking-[0.2em] text-white/90">
                            {hero.eyebrow}
                        </span>
                    </div>
                    <h1 className="font-display text-5xl font-bold uppercase tracking-tight text-white md:text-7xl">
                        {hero.title} <span className="text-accent">{hero.highlight}</span>
                    </h1>
                    <p className="mt-8 max-w-2xl text-base leading-8 text-white/85 md:text-lg">
                        {hero.description}
                    </p>
                </div>
            </div>
        </section>
    );
}
