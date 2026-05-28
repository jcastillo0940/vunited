export default function CartItem({ item, onIncrease, onDecrease, onRemove }) {
    const subtotal = item.price * item.quantity;

    return (
        <article className="flex flex-col gap-6 rounded-xl border border-slate-100 bg-surface p-6 shadow-card transition hover:shadow-panel md:flex-row md:items-center">
            <div className="h-32 w-full overflow-hidden rounded-lg bg-white p-2 shadow-sm md:w-32 md:flex-shrink-0">
                <img
                    src={item.imageUrl}
                    alt={item.name}
                    className="h-full w-full object-cover object-center"
                />
            </div>

            <div className="flex-grow text-center md:text-left">
                <h3 className="font-display text-2xl font-bold uppercase text-primary">
                    {item.name}
                </h3>
                <p className="mt-1 text-sm text-slate-500">{item.variant}</p>
            </div>

            <div className="flex items-center justify-center gap-4 rounded-md border border-slate-200 bg-white px-4 py-2">
                <button
                    type="button"
                    onClick={() => onDecrease(item.id)}
                    className="flex h-8 w-8 items-center justify-center text-primary transition hover:text-accent"
                    aria-label={`Reducir cantidad de ${item.name}`}
                >
                    <span className="material-symbols-outlined">remove</span>
                </button>
                <span className="min-w-6 text-center font-bold text-text-main">{item.quantity}</span>
                <button
                    type="button"
                    onClick={() => onIncrease(item.id)}
                    className="flex h-8 w-8 items-center justify-center text-primary transition hover:text-accent"
                    aria-label={`Aumentar cantidad de ${item.name}`}
                >
                    <span className="material-symbols-outlined">add</span>
                </button>
            </div>

            <div className="text-center md:text-right">
                <p className="font-display text-2xl font-bold text-primary">
                    ${subtotal.toFixed(2)}
                </p>
                <p className="mt-1 text-xs uppercase tracking-[0.22em] text-slate-400">
                    ${item.price.toFixed(2)} c/u
                </p>
                <button
                    type="button"
                    onClick={() => onRemove(item.id)}
                    className="mt-3 inline-flex items-center gap-1 text-xs font-bold uppercase text-red-600 transition hover:underline"
                >
                    <span className="material-symbols-outlined text-[16px]">delete</span>
                    Eliminar
                </button>
            </div>
        </article>
    );
}
