export default function ProductFilters({ filters, selectedFilter, onSelect }) {
    return (
        <div className="flex flex-wrap gap-3">
            {filters.map((filter) => {
                const active = selectedFilter === filter.value;

                return (
                    <button
                        key={filter.value}
                        type="button"
                        onClick={() => onSelect(filter.value)}
                        className={`rounded-full px-5 py-2 text-sm font-semibold uppercase tracking-[0.18em] transition ${
                            active
                                ? 'bg-primary text-white shadow-card'
                                : 'border border-slate-200 bg-white text-slate-500 hover:border-accent hover:text-accent'
                        }`}
                    >
                        {filter.label}
                    </button>
                );
            })}
        </div>
    );
}
