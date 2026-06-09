import { Link } from '@inertiajs/react';

export default function SquadGrid({ players }) {
    return (
        <section>
            <h2 className="mb-8 flex items-center gap-4 font-display text-3xl font-bold uppercase text-primary">
                Jugadores
                <div className="h-px flex-grow bg-gray-200" />
            </h2>

            <div className="grid grid-cols-1 gap-gutter sm:grid-cols-2 lg:grid-cols-4">
                {players.map((player) => (
                    <Link
                        key={player.id}
                        href={`/plantilla/${player.slug}`}
                        className="group flex flex-col overflow-hidden rounded-xl border border-gray-100 bg-white shadow-md transition-all duration-300 hover:shadow-xl"
                    >
                        <div className="relative h-[420px] overflow-hidden">
                            <div className="absolute left-4 top-4 z-20">
                                <span className="font-display text-5xl font-black italic text-primary/10">
                                    {player.number}
                                </span>
                            </div>
                            {player.imageUrl ? (
                                <img
                                    src={player.imageUrl}
                                    alt={player.name}
                                    className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                />
                            ) : (
                                <div className="flex h-full items-center justify-center bg-surface text-primary/20">
                                    <span className="material-symbols-outlined text-8xl">sports_soccer</span>
                                </div>
                            )}
                            <div className="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100" />
                        </div>

                        <div className="border-t border-gray-50 bg-white p-6 text-center">
                            <span className="mb-1 block text-[10px] font-bold uppercase tracking-widest text-accent">
                                {player.position}
                            </span>
                            <h3 className="font-display text-2xl font-bold uppercase tracking-tight text-primary">
                                {player.firstName} <span className="text-text-main">{player.lastName}</span>
                            </h3>
                            <p className="mt-2 text-sm text-gray-500">{player.nationality}</p>
                        </div>
                    </Link>
                ))}
            </div>
        </section>
    );
}
