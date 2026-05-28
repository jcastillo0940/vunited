@extends('layouts.admin.app')

@section('title', 'Payment Events')

@section('content')
    <h2>Payment Events</h2>

    <form method="GET" action="{{ route('admin.payment-events.index') }}" style="display:grid;grid-template-columns:1fr 1fr auto;gap:1rem;margin-bottom:1.5rem;">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search event, type, order or capture" class="admin-button" style="text-align:left;">
        <select name="processing_status" class="admin-button">
            <option value="">All processing statuses</option>
            @foreach (['received', 'processed', 'ignored', 'failed'] as $status)
                <option value="{{ $status }}" @selected($filters['processing_status'] === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-button">Filter</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th align="left">Event ID</th>
                    <th align="left">Type</th>
                    <th align="left">Verification</th>
                    <th align="left">Processing</th>
                    <th align="left">Provider Order ID</th>
                    <th align="left">Provider Capture ID</th>
                    <th align="left">Received</th>
                    <th align="left">Processed</th>
                    <th align="left"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr style="border-top:1px solid #e2e8f0;">
                        <td style="padding:0.85rem 0;">{{ $event->provider_event_id }}</td>
                        <td>{{ $event->event_type }}</td>
                        <td>{{ $event->verification_status->value }}</td>
                        <td>{{ $event->processing_status->value }}</td>
                        <td>{{ $event->provider_order_id ?? '—' }}</td>
                        <td>{{ $event->provider_capture_id ?? '—' }}</td>
                        <td>{{ $event->received_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>{{ $event->processed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td><a href="{{ route('admin.payment-events.show', $event) }}">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding:1rem 0;">No payment events found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
