@extends('layouts.admin.app')

@section('title', 'Ticket Zones')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <div>
            <h2 style="margin:0;">Ticket Zones</h2>
            <p style="margin:0.35rem 0 0;color:#64748b;">{{ $matchEvent->home_team }} vs {{ $matchEvent->away_team }}</p>
        </div>
        <div style="display:flex;gap:0.75rem;">
            <a class="admin-button" href="{{ route('admin.match-events.index') }}">Back to Matches</a>
            <a class="admin-button" href="{{ route('admin.ticket-zones.create', $matchEvent) }}">New Zone</a>
        </div>
    </div>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:0.75rem;">Name</th>
                    <th style="padding:0.75rem;">Slug</th>
                    <th style="padding:0.75rem;">Price</th>
                    <th style="padding:0.75rem;">Capacity</th>
                    <th style="padding:0.75rem;">Available</th>
                    <th style="padding:0.75rem;">Active</th>
                    <th style="padding:0.75rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ticketZones as $ticketZone)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:0.75rem;">{{ $ticketZone->name }}</td>
                        <td style="padding:0.75rem;">{{ $ticketZone->slug }}</td>
                        <td style="padding:0.75rem;">{{ $ticketZone->currency }} {{ number_format((float) $ticketZone->price, 2) }}</td>
                        <td style="padding:0.75rem;">{{ $ticketZone->capacity ?? 'N/A' }}</td>
                        <td style="padding:0.75rem;">{{ $ticketZone->available_quantity ?? 'N/A' }}</td>
                        <td style="padding:0.75rem;">{{ $ticketZone->is_active ? 'Yes' : 'No' }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a class="admin-button" href="{{ route('admin.ticket-zones.edit', [$matchEvent, $ticketZone]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.ticket-zones.destroy', [$matchEvent, $ticketZone]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="admin-button" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:1rem;color:#64748b;">No ticket zones yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
