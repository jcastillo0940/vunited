export default function SquadFilters({
    squadFilters,
    activeSquadId,
    onSquadChange,
    positionFilters,
    activePositionId,
    onPositionChange,
}) {
    return (
        <section className="mb-16">
            <div className="flex flex-wrap gap-4">
                {squadFilters.map((filter) => {
                    const active = filter.id === activeSquadId;

                    return (
                        <button
                            key={filter.id}
                            type="button"
                            onClick={() => onSquadChange(filter.id)}
                            className={[
                                'rounded-md px-8 py-3 text-sm font-bold uppercase tracking-widest transition-all',
                                active
                                    ? 'bg-primary text-white shadow-lg'
                                    : 'bg-surface text-text-main/60 hover:bg-gray-200 hover:text-text-main',
                            ].join(' ')}
                        >
                            {filter.label}
                        </button>
                    );
                })}
            </div>

            <div className="mt-12 flex gap-3 overflow-x-auto pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                {positionFilters.map((filter) => {
                    const active = filter.id === activePositionId;

                    return (
                        <button
                            key={filter.id}
                            type="button"
                            onClick={() => onPositionChange(filter.id)}
                            className={[
                                'whitespace-nowrap rounded-full border px-6 py-2 text-xs font-bold uppercase transition-colors',
                                active
                                    ? 'border-primary bg-primary text-white shadow-sm'
                                    : 'border-gray-200 bg-surface text-text-main/60 hover:border-accent hover:text-primary',
                            ].join(' ')}
                        >
                            {filter.label}
                        </button>
                    );
                })}
            </div>
        </section>
    );
}
