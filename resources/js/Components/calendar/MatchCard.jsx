import CTAButton from '@/components/common/CTAButton';

const statusStyles = {
    proximo: 'bg-primary/10 text-primary',
    finalizado: 'bg-slate-100 text-slate-500',
    envivo: 'bg-accent/15 text-accent',
};

export default function MatchCard({ match }) {
    return (
        <article className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-md transition-transform hover:-translate-y-1">
            <div className="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div>
                    <p className="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">
                        {match.competition}
                    </p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">
                        {match.dateLabel} · {match.timeLabel}
                    </p>
                </div>
                <span className={`rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] ${statusStyles[match.status]}`}>
                    {match.statusLabel}
                </span>
            </div>

            <div className="px-6 py-8">
                <div className="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                    <div className="text-center">
                        <div className="mb-3 grid h-16 w-16 place-items-center rounded-full bg-surface font-display text-xl font-bold text-primary">
                            {match.homeShort}
                        </div>
                        <p className="font-display text-base font-bold uppercase text-primary">
                            {match.homeTeam}
                        </p>
                    </div>

                    <div className="text-center">
                        {match.status === 'finalizado' ? (
                            <div className="font-display text-4xl font-black text-primary">
                                {match.homeScore} <span className="text-accent">-</span> {match.awayScore}
                            </div>
                        ) : (
                            <div className="font-display text-3xl font-black italic text-accent">VS</div>
                        )}
                        <p className="mt-2 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">
                            {match.venueType}
                        </p>
                    </div>

                    <div className="text-center">
                        <div className="mb-3 grid h-16 w-16 place-items-center rounded-full bg-surface font-display text-xl font-bold text-primary">
                            {match.awayShort}
                        </div>
                        <p className="font-display text-base font-bold uppercase text-primary">
                            {match.awayTeam}
                        </p>
                    </div>
                </div>

                <div className="mt-6 border-t border-slate-100 pt-5">
                    <p className="font-semibold text-text-main">{match.headline}</p>
                    <div className="mt-3 flex items-center gap-2 text-sm text-slate-500">
                        <span className="material-symbols-outlined text-accent">stadium</span>
                        {match.stadium}
                    </div>
                </div>
            </div>

            <div className="border-t border-slate-100 px-6 py-5">
                {match.status === 'finalizado' ? (
                    <p className="text-center text-xs font-bold uppercase tracking-[0.2em] text-slate-400">
                        Partido finalizado
                    </p>
                ) : (
                    <CTAButton
                        href={match.ctaHref}
                        variant={match.status === 'proximo' ? 'primary' : 'outline'}
                        className="w-full justify-center"
                    >
                        {match.ctaLabel}
                    </CTAButton>
                )}
            </div>
        </article>
    );
}
