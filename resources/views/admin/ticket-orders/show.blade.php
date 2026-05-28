@extends('layouts.admin.app')

@section('title', 'Ticket Order Detail')

@section('content')
    <h2>{{ $order->order_number }}</h2>

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;">
        <div>
            <h3>Customer</h3>
            <p><strong>Name:</strong> {{ $order->customer_name ?? '—' }}</p>
            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
            <p><strong>Phone:</strong> {{ $order->customer_phone ?? '—' }}</p>
            <p><strong>Status:</strong> {{ $order->status->value }}</p>
            <p><strong>Subtotal:</strong> {{ number_format((float) $order->subtotal, 2) }} {{ $order->currency }}</p>
            <p><strong>Total:</strong> {{ number_format((float) $order->total, 2) }} {{ $order->currency }}</p>
            <p><strong>Paid At:</strong> {{ $order->paid_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Cancelled At:</strong> {{ $order->cancelled_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
        </div>

        <div>
            <h3>Match</h3>
            @if ($order->matchEvent)
                <p><strong>Code:</strong> {{ $order->matchEvent->code }}</p>
                <p><strong>Match:</strong> {{ $order->matchEvent->home_team }} vs {{ $order->matchEvent->away_team }}</p>
                <p><strong>Competition:</strong> {{ $order->matchEvent->competition }}</p>
                <p><strong>Date:</strong> {{ $order->matchEvent->match_date?->format('Y-m-d H:i') }}</p>
                <p><strong>Stadium:</strong> {{ $order->matchEvent->stadium_name }}</p>
            @else
                <p>Match event not available.</p>
            @endif

            <h3 style="margin-top:1rem;">Payment</h3>
            @if ($payment)
                <p><strong>Payment ID:</strong> {{ $payment->id }}</p>
                <p><strong>Status:</strong> {{ $payment->status->value }}</p>
                <p><strong>Provider:</strong> {{ $payment->provider }}</p>
                <p><strong>Provider Order ID:</strong> {{ $payment->provider_order_id ?? '—' }}</p>
                <p><strong>Provider Capture ID:</strong> {{ $payment->provider_capture_id ?? '—' }}</p>
                <p><strong>Amount:</strong> {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</p>
                @if ($payment->provider_order_id)
                    <p><a href="{{ route('admin.payment-events.index', ['search' => $payment->provider_order_id]) }}">View related events</a></p>
                @endif
            @else
                <p>No payment associated.</p>
            @endif
        </div>
    </div>

    <h3 style="margin-top:1.5rem;">Items</h3>
    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Zone</th>
                    <th style="padding:0.75rem;">Zone ID</th>
                    <th style="padding:0.75rem;">Qty</th>
                    <th style="padding:0.75rem;">Unit Price</th>
                    <th style="padding:0.75rem;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">{{ $item->zone_name }}</td>
                        <td style="padding:0.75rem;">{{ $item->ticketZone?->slug ?? $item->ticket_zone_id ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $item->quantity }}</td>
                        <td style="padding:0.75rem;">{{ number_format((float) $item->unit_price, 2) }} {{ $order->currency }}</td>
                        <td style="padding:0.75rem;">{{ number_format((float) $item->line_total, 2) }} {{ $order->currency }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php $issuedTickets = $order->issuedTickets ?? collect(); @endphp

    <h3 style="margin-top:1.5rem;">
        Issued Tickets
        @if ($issuedTickets->isNotEmpty())
            <a href="{{ route('admin.issued-tickets.index', ['search' => $order->order_number]) }}" style="font-size:0.85rem;font-weight:normal;margin-left:0.5rem;">View all</a>
        @endif
    </h3>

    @if ($issuedTickets->isEmpty())
        <p style="color:#64748b;font-size:0.9rem;">
            @if ($order->status->value === 'paid')
                No tickets issued yet.
            @else
                Tickets will be issued automatically when the order is paid.
            @endif
        </p>
    @else
        <div style="overflow:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                        <th style="padding:0.75rem;">Seat</th>
                        <th style="padding:0.75rem;">Zone</th>
                        <th style="padding:0.75rem;">Status</th>
                        <th style="padding:0.75rem;">Token (partial)</th>
                        <th style="padding:0.75rem;">Issued At</th>
                        <th style="padding:0.75rem;">Used At</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($issuedTickets as $ticket)
                        <tr style="border-bottom:1px solid #eef2f7;">
                            <td style="padding:0.75rem;">{{ $ticket->seat_label ?? '—' }}</td>
                            <td style="padding:0.75rem;">{{ $ticket->zone_name }}</td>
                            <td style="padding:0.75rem;">{{ $ticket->status->value }}</td>
                            <td style="padding:0.75rem;font-family:monospace;font-size:0.8rem;">{{ substr($ticket->token, 0, 8) }}…</td>
                            <td style="padding:0.75rem;">{{ $ticket->issued_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td style="padding:0.75rem;">{{ $ticket->used_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td style="padding:0.75rem;"><a href="{{ route('admin.issued-tickets.show', $ticket) }}">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
