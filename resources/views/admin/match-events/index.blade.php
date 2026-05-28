@extends('layouts.admin.app')

@section('title', 'Match Events')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <div>
            <h2 style="margin:0;">Match Events</h2>
            <p style="margin:0.35rem 0 0;color:#64748b;">Gestiona partidos publicos para el catalogo de boletos.</p>
        </div>
        <a class="admin-button" href="{{ route('admin.match-events.create') }}">New Match Event</a>
    </div>

    @if(session('error'))
        <div style="margin-bottom:1rem;padding:0.75rem 1rem;border-radius:0.75rem;background:#fee2e2;color:#991b1b;">
            {{ session('error') }}
        </div>
    @endif

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:0.75rem;">Code</th>
                    <th style="padding:0.75rem;">Match</th>
                    <th style="padding:0.75rem;">Status</th>
                    <th style="padding:0.75rem;">Date</th>
                    <th style="padding:0.75rem;">Featured</th>
                    <th style="padding:0.75rem;">Zones</th>
                    <th style="padding:0.75rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matchEvents as $matchEvent)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:0.75rem;">{{ $matchEvent->code }}</td>
                        <td style="padding:0.75rem;">{{ $matchEvent->home_team }} vs {{ $matchEvent->away_team }}</td>
                        <td style="padding:0.75rem;">{{ strtoupper($matchEvent->status) }}</td>
                        <td style="padding:0.75rem;">{{ optional($matchEvent->match_date)->format('Y-m-d H:i') }}</td>
                        <td style="padding:0.75rem;">{{ $matchEvent->is_featured ? 'Yes' : 'No' }}</td>
                        <td style="padding:0.75rem;">{{ $matchEvent->ticket_zones_count }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                            <a class="admin-button" href="{{ route('admin.match-events.edit', $matchEvent) }}">Edit</a>
                            <a class="admin-button" href="{{ route('admin.ticket-zones.index', $matchEvent) }}">Zones</a>
                            <form method="POST" action="{{ route('admin.match-events.destroy', $matchEvent) }}">
                                @csrf
                                @method('DELETE')
                                <button class="admin-button" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:1rem;color:#64748b;">No match events yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
