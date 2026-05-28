@extends('layouts.admin.app')

@section('title', 'Ticket — ' . $ticket->seat_label)

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div>
            <h2 style="margin:0 0 0.25rem;">{{ $ticket->seat_label ?? 'Ticket #' . $ticket->id }}</h2>
            <span style="
                padding:0.2rem 0.7rem;
                border-radius:0.4rem;
                font-size:0.85rem;
                background:{{ $ticket->status->value === 'issued' ? '#dcfce7' : ($ticket->status->value === 'used' ? '#dbeafe' : '#fee2e2') }};
                color:{{ $ticket->status->value === 'issued' ? '#166534' : ($ticket->status->value === 'used' ? '#1e40af' : '#991b1b') }};
            ">{{ strtoupper($ticket->status->value) }}</span>
        </div>
        <a href="{{ route('admin.issued-tickets.index') }}" class="admin-button">← All Tickets</a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1.5rem;">
        <div>
            <h3>Ticket Details</h3>
            <p><strong>Zone:</strong> {{ $ticket->zone_name }}</p>
            <p><strong>Seat Label:</strong> {{ $ticket->seat_label ?? '—' }}</p>
            <p><strong>Status:</strong> {{ $ticket->status->value }}</p>
            <p><strong>Issued At:</strong> {{ $ticket->issued_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Used At:</strong> {{ $ticket->used_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Voided At:</strong> {{ $ticket->voided_at?->format('Y-m-d H:i:s') ?? '—' }}</p>

            <h3 style="margin-top:1rem;">Token</h3>
            <p style="font-family:monospace;font-size:0.85rem;word-break:break-all;background:#f8fafc;border:1px solid #e2e8f0;padding:0.75rem;border-radius:0.5rem;">{{ $ticket->token }}</p>
            <p><strong>QR Payload:</strong> <code style="font-size:0.8rem;">{{ $ticket->qr_payload }}</code></p>
        </div>

        <div>
            <h3>Order</h3>
            @if ($ticket->ticketOrder)
                <p><strong>Order:</strong> <a href="{{ route('admin.ticket-orders.show', $ticket->ticketOrder) }}">{{ $ticket->ticketOrder->order_number }}</a></p>
                <p><strong>Customer:</strong> {{ $ticket->ticketOrder->customer_name ?? '—' }}</p>
                <p><strong>Email:</strong> {{ $ticket->ticketOrder->customer_email }}</p>
                <p><strong>Order Status:</strong> {{ $ticket->ticketOrder->status->value }}</p>
            @else
                <p>Order not available.</p>
            @endif

            <h3 style="margin-top:1rem;">Match</h3>
            @if ($ticket->ticketOrder?->matchEvent)
                @php $match = $ticket->ticketOrder->matchEvent; @endphp
                <p><strong>Match:</strong> {{ $match->home_team }} vs {{ $match->away_team }}</p>
                <p><strong>Competition:</strong> {{ $match->competition }}</p>
                <p><strong>Date:</strong> {{ $match->match_date?->format('Y-m-d H:i') ?? '—' }}</p>
                <p><strong>Stadium:</strong> {{ $match->stadium_name }}</p>
            @else
                <p>Match not available.</p>
            @endif
        </div>
    </div>
@endsection
