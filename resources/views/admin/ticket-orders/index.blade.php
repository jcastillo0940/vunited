@extends('layouts.admin.app')

@section('title', 'Ticket Orders')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Ticket Orders</h2>
            <p style="margin:0;color:#475569;">Readonly monitoring for ticket orders paid through PayPal. No QR issued yet.</p>
        </div>
    </div>

    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin:1.5rem 0;">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Order / name / email" class="admin-button" style="min-width:280px;">
        <select name="status" class="admin-button">
            <option value="">All statuses</option>
            @foreach (['draft', 'pending_payment', 'paid', 'cancelled', 'failed'] as $status)
                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-button">Filter</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Order</th>
                    <th style="padding:0.75rem;">Match</th>
                    <th style="padding:0.75rem;">Customer</th>
                    <th style="padding:0.75rem;">Status</th>
                    <th style="padding:0.75rem;">Total</th>
                    <th style="padding:0.75rem;">Paid At</th>
                    <th style="padding:0.75rem;">Created</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">{{ $order->order_number }}</td>
                        <td style="padding:0.75rem;">
                            @if ($order->matchEvent)
                                {{ $order->matchEvent->home_team }} vs {{ $order->matchEvent->away_team }}
                                <br><small>{{ $order->matchEvent->match_date?->format('Y-m-d') }}</small>
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:0.75rem;">{{ $order->customer_name ?? '—' }}<br><small>{{ $order->customer_email }}</small></td>
                        <td style="padding:0.75rem;">{{ $order->status->value }}</td>
                        <td style="padding:0.75rem;">{{ number_format((float) $order->total, 2) }} {{ $order->currency }}</td>
                        <td style="padding:0.75rem;">{{ $order->paid_at?->format('Y-m-d H:i:s') ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $order->created_at?->format('Y-m-d H:i:s') ?? '—' }}</td>
                        <td style="padding:0.75rem;"><a href="{{ route('admin.ticket-orders.show', $order) }}">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:1rem;">No ticket orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
