export default function CartHero({ hero }) {
    return (
        <section className="bg-primary pb-12 pt-32 text-white">
            <div className="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop">
                <div className="border-l-8 border-accent pl-6">
                    <h1 className="font-display text-4xl font-black uppercase leading-none md:text-6xl">
                        {hero.title}
                    </h1>
                    <p className="mt-3 max-w-2xl text-sm font-semibold uppercase tracking-[0.24em] text-white/70 md:text-base md:tracking-[0.3em]">
                        Veraguas United Store / Checkout Visual
                    </p>
                    <p className="mt-5 max-w-3xl text-base leading-7 text-white/80">
                        {hero.description}
                    </p>
                </div>
            </div>
        </section>
    );
}
