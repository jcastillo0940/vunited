export default function AcademyBlock({ academy }) {
    return (
        <div className="group relative mt-20 overflow-hidden rounded-lg border-l-8 border-primary bg-surface p-12">
            <div className="relative z-10">
                <h2 className="mb-6 font-display text-2xl font-bold uppercase tracking-tight text-primary">
                    {academy.title}
                </h2>
                <p className="mb-10 max-w-2xl text-sm leading-relaxed text-gray-600">
                    {academy.description}
                </p>
                <div className="grid grid-cols-2 gap-6 md:grid-cols-4">
                    {academy.stats.map((stat) => (
                        <div
                            key={stat.label}
                            className="rounded-lg border border-gray-100 bg-white p-6 text-center shadow-sm"
                        >
                            <div className="font-display text-4xl font-bold text-primary">
                                {stat.value}
                            </div>
                            <div className="text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                {stat.label}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
