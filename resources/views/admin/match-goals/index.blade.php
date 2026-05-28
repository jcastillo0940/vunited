@extends('layouts.admin.app')

@section('title', 'Goles — ' . $matchEvent->home_team . ' vs ' . $matchEvent->away_team)

@section('content')
    <div style="margin-bottom:1rem;">
        <a href="{{ route('admin.match-events.index') }}" style="color:#1D428A;font-size:0.875rem;">&larr; Match Events</a>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.3rem;">Goles del partido</h2>
            <p style="margin:0;color:#475569;">{{ $matchEvent->home_team }} {{ $matchEvent->home_score ?? '—' }} – {{ $matchEvent->away_score ?? '—' }} {{ $matchEvent->away_team }}</p>
            <p style="margin:0.2rem 0 0;color:#64748b;font-size:0.8rem;">{{ optional($matchEvent->match_date)->format('d/m/Y H:i') }} · {{ $matchEvent->competition }}</p>
        </div>
        <a href="{{ route('admin.match-events.goals.create', $matchEvent) }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">+ Agregar gol</a>
    </div>

    <div style="overflow:auto;margin-top:1.5rem;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Min.</th>
                    <th style="padding:0.75rem;">Jugador / Goleador</th>
                    <th style="padding:0.75rem;">Club</th>
                    <th style="padding:0.75rem;">Tipo</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($goals as $goal)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;font-weight:bold;">{{ $goal->minute ? $goal->minute . "'" : '—' }}</td>
                        <td style="padding:0.75rem;">{{ $goal->player?->name ?? $goal->scorer_name ?? 'Sin nombre' }}</td>
                        <td style="padding:0.75rem;">{{ $goal->club?->short_name ?? $goal->club?->name ?? '—' }}</td>
                        <td style="padding:0.75rem;">
                            @if($goal->is_own_goal)
                                <span style="color:#991b1b;">En propia</span>
                            @elseif($goal->is_penalty)
                                <span style="color:#1D428A;">Penalti</span>
                            @else
                                Normal
                            @endif
                        </td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.match-events.goals.edit', [$matchEvent, $goal]) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.match-events.goals.destroy', [$matchEvent, $goal]) }}" onsubmit="return confirm('¿Eliminar gol?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:1rem;color:#64748b;">Sin goles registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
