export default function PlayerCard({ player }) {
    return (
        <article className="surface-card group overflow-hidden">
            <div className="aspect-[4/5] bg-surface">
                {player.imageUrl ? (
                    <img
                        src={player.imageUrl}
                        alt={player.name}
                        className="h-full w-full object-cover"
                    />
                ) : (
                    <div className="flex h-full items-center justify-center text-gray-300">
                        <span className="material-symbols-outlined text-7xl">sports_soccer</span>
                    </div>
                )}
            </div>
            <div className="space-y-2 p-6">
                <div className="flex items-center justify-between">
                    <p className="text-[10px] font-bold uppercase tracking-athletic text-accent">
                        {player.position}
                    </p>
                    <span className="font-display text-2xl font-bold text-primary">
                        {player.number}
                    </span>
                </div>
                <h3 className="font-display text-2xl font-bold uppercase text-primary">
                    {player.name}
                </h3>
                <p className="text-sm text-gray-600">{player.meta}</p>
            </div>
        </article>
    );
}
