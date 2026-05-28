export default function EmptyState({
    title = 'Sin contenido disponible',
    description = 'Este modulo se completara en una siguiente fase.',
}) {
    return (
        <div className="surface-card border-dashed p-8 text-center">
            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-surface text-primary">
                <span className="material-symbols-outlined">inventory_2</span>
            </div>
            <h3 className="mt-4 font-display text-2xl font-bold uppercase text-primary">
                {title}
            </h3>
            <p className="mt-3 text-sm leading-relaxed text-gray-600">
                {description}
            </p>
        </div>
    );
}
