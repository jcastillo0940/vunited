export default function CalendarHero({ hero }) {
    return (
        <section className="relative flex min-h-[56vh] items-end overflow-hidden bg-primary pt-28">
            <div className="absolute inset-0">
                <img src={hero.imageUrl} alt={hero.title} className="h-full w-full object-cover opacity-35" />
                <div className="absolute inset-0 bg-[linear-gradient(135deg,rgba(29,66,138,0.95),rgba(29,66,138,0.72))]" />
            </div>

            <div className="page-shell relative z-10 mx-auto w-full max-w-7xl px-margin-mobile pb-16 md:px-margin-desktop md:pb-20">
                <div className="max-w-3xl">
                    <div className="mb-6 h-1.5 w-24 bg-accent" />
                    <h1 className="font-display text-5xl font-black uppercase leading-none tracking-tight text-white md:text-7xl">
                        {hero.title}
                        <br />
                        <span className="text-accent">{hero.highlight}</span>
                    </h1>
                    <p className="mt-6 max-w-2xl border-l-4 border-accent pl-6 text-lg leading-relaxed text-white/90 md:text-xl">
                        {hero.description}
                    </p>
                </div>
            </div>
        </section>
    );
}
