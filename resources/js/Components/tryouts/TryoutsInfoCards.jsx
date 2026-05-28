export default function TryoutsInfoCards({ cards }) {
    return (
        <section className="border-t border-gray-200 bg-white py-24">
            <div className="mx-auto grid max-w-7xl grid-cols-1 gap-16 px-margin-mobile md:grid-cols-3 md:px-margin-desktop">
                {cards.map((card) => (
                    <article key={card.id} className="flex flex-col items-center text-center">
                        <div className="mb-6 rounded-full bg-accent p-5 text-white">
                            <span className="material-symbols-outlined text-3xl">{card.icon}</span>
                        </div>
                        <h4 className="mb-4 font-display text-2xl font-bold uppercase text-primary">
                            {card.title}
                        </h4>
                        <p className="leading-relaxed text-text-main">{card.description}</p>
                    </article>
                ))}
            </div>
        </section>
    );
}
