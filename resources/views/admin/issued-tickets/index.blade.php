@extends('layouts.admin.app')

@section('title', isset($order) ? 'Tickets — ' . $order->order_number : 'Issued Tickets')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">
                @isset($order)
                    Tickets — <a href="{{ route('admin.ticket-orders.show', $order) }}">{{ $order->order_number }}</a>
                @else
                    Issued Tickets
                @endisset
            </h2>
            <p style="margin:0;color:#475569;">Digital tickets emitted for paid orders.</p>
        </div>
    </div>

    @empty($order)
        <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin:1.5rem 0;">
            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Token / seat / order / email" class="admin-button" style="min-width:280px;">
            <select name="status" class="admin-button">
                <option value="">All statuses</option>
                @foreach (['issued', 'used', 'voided'] as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit" class="admin-button">Filter</button>
        </form>
    @endempty

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Seat</th>
                    <th style="padding:0.75rem;">Zone</th>
                    <th style="padding:0.75rem;">Status</th>
                    <th style="padding:0.75rem;">Order</th>
                    <th style="padding:0.75rem;">Token (partial)</th>
                    <th style="padding:0.75rem;">Issued At</th>
                    <th style="padding:0.75rem;">Used At</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">{{ $ticket->seat_label ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $ticket->zone_name }}</td>
                        <td style="padding:0.75rem;">
                            <span style="
                                padding:0.2rem 0.6rem;
                                border-radius:0.4rem;
                                font-size:0.8rem;
                                background:{{ $ticket->status->value === 'issued' ? '#dcfce7' : ($ticket->status->value === 'used' ? '#dbeafe' : '#fee2e2') }};
                                color:{{ $ticket->status->value === 'issued' ? '#166534' : ($ticket->status->value === 'used' ? '#1e40af' : '#991b1b') }};
                            ">{{ $ticket->status->value }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            @if ($ticket->ticketOrder)
                                <a href="{{ route('admin.ticket-orders.show', $ticket->ticketOrder) }}">{{ $ticket->ticketOrder->order_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:0.75rem;font-family:monospace;font-size:0.8rem;">{{ substr($ticket->token, 0, 8) }}…</td>
                        <td style="padding:0.75rem;">{{ $ticket->issued_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $ticket->used_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td style="padding:0.75rem;"><a href="{{ route('admin.issued-tickets.show', $ticket) }}">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:1rem;">No issued tickets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
