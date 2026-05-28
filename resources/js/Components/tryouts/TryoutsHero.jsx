export default function TryoutsHero({ hero }) {
    return (
        <section className="bg-primary py-20 text-center text-white">
            <div className="mx-auto max-w-4xl px-margin-mobile md:px-margin-desktop">
                <h1 className="font-display text-4xl font-bold uppercase leading-tight tracking-tight md:text-6xl">
                    {hero.title}
                </h1>
                <p className="mx-auto mt-6 max-w-2xl text-xl text-white/90 md:text-2xl">
                    {hero.description}
                </p>
            </div>
        </section>
    );
}
