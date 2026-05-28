export default function ConfirmationHero({ hero }) {
    return (
        <section className="mb-12">
            <div className="mx-auto flex max-w-4xl flex-col items-center text-center">
                <span className="mb-6 inline-flex items-center justify-center rounded-full bg-accent/20 p-4">
                    <span
                        className="material-symbols-outlined text-4xl text-accent"
                        style={{ fontVariationSettings: "'FILL' 1" }}
                    >
                        check_circle
                    </span>
                </span>
                <h1 className="font-display text-4xl font-bold uppercase italic tracking-tight text-primary md:text-6xl">
                    {hero.title}
                </h1>
                <p className="mx-auto mt-4 max-w-xl text-lg leading-relaxed text-text-main/70">
                    {hero.description}
                </p>
            </div>
        </section>
    );
}
