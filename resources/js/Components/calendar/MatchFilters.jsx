export default function MatchFilters({ filters, selectedFilter, onSelect }) {
    return (
        <div className="flex flex-wrap gap-3">
            {filters.map((filter) => {
                const active = filter === selectedFilter;

                return (
                    <button
                        key={filter}
                        type="button"
                        onClick={() => onSelect(filter)}
                        className={`rounded-md px-5 py-3 text-xs font-bold uppercase tracking-[0.18em] transition-all ${
                            active
                                ? 'bg-accent text-white shadow-md'
                                : 'border border-outline bg-white text-text-main/60 hover:border-primary hover:text-primary'
                        }`}
                    >
                        {filter}
                    </button>
                );
            })}
        </div>
    );
}
