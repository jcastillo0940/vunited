import MatchCard from '@/components/calendar/MatchCard';

export default function MatchList({ matches }) {
    return (
        <div className="grid grid-cols-1 gap-6 xl:grid-cols-2">
            {matches.map((match) => (
                <MatchCard key={match.id} match={match} />
            ))}
        </div>
    );
}
