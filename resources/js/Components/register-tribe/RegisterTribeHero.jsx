export default function RegisterTribeHero({ hero }) {
    return (
        <section className="relative mb-16 flex h-[450px] items-center overflow-hidden">
            <img
                src={hero.imageUrl}
                alt={hero.title}
                className="absolute inset-0 h-full w-full object-cover"
            />
            <div className="absolute inset-0 bg-white/20" />

            <div className="page-shell relative z-10 mx-auto max-w-7xl">
                <div className="max-w-2xl rounded-xl border border-outline bg-white p-10 shadow-2xl md:p-14">
                    <h1 className="mb-6 font-display text-4xl font-bold uppercase leading-tight text-primary md:text-5xl">
                        {hero.title}
                    </h1>
                    <p className="text-lg text-on-surface-variant">{hero.description}</p>
                    <div className="mt-8 h-2 w-20 bg-accent" />
                </div>
            </div>
        </section>
    );
}
