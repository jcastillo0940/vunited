import CTAButton from '@/components/common/CTAButton';

export default function StandingsCard({ standings }) {
    return (
        <section className="rounded-lg border border-gray-200 bg-white p-8 shadow-md">
            <div className="mb-10 flex items-center justify-between">
                <h2 className="font-display text-2xl font-bold uppercase tracking-tight text-primary">
                    TABLA LPF - ESTE
                </h2>
                <span className="material-symbols-outlined text-accent">leaderboard</span>
            </div>
            <div className="space-y-3">
                <div className="grid grid-cols-12 gap-2 px-4 text-[11px] font-bold uppercase text-gray-400">
                    <span className="col-span-2">POS</span>
                    <span className="col-span-6">CLUB</span>
                    <span className="col-span-2 text-center">PJ</span>
                    <span className="col-span-2 text-right">PTS</span>
                </div>
                {standings.map((team) => (
                    <div
                        key={`${team.position}-${team.club}`}
                        className={[
                            'grid grid-cols-12 items-center gap-2 p-4 font-semibold',
                            team.featured
                                ? 'rounded-md bg-primary font-bold text-white shadow-md'
                                : 'border-b border-gray-100 text-gray-700',
                        ].join(' ')}
                    >
                        <span className={['col-span-2 text-center', team.featured ? '' : 'text-gray-400'].join(' ')}>
                            {team.position}
                        </span>
                        <span className="col-span-6">{team.club}</span>
                        <span className={['col-span-2 text-center', team.featured ? '' : 'text-gray-400'].join(' ')}>
                            {team.played}
                        </span>
                        <span className="col-span-2 text-right">{team.points}</span>
                    </div>
                ))}
            </div>
            <CTAButton
                as="a"
                href="/tabla-posiciones"
                variant="outline"
                className="mt-10 w-full justify-center border-accent text-xs tracking-[0.2em] text-accent hover:bg-accent hover:text-white"
            >
                Ver Detalles
            </CTAButton>
        </section>
    );
}
