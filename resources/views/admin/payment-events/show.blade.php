@extends('layouts.admin.app')

@section('title', 'Payment Event Detail')

@section('content')
    <h2>{{ $event->provider_event_id }}</h2>

    <p><strong>Type:</strong> {{ $event->event_type }}</p>
    <p><strong>Verification:</strong> {{ $event->verification_status->value }}</p>
    <p><strong>Processing:</strong> {{ $event->processing_status->value }}</p>
    <p><strong>Provider Order ID:</strong> {{ $event->provider_order_id ?? '—' }}</p>
    <p><strong>Provider Capture ID:</strong> {{ $event->provider_capture_id ?? '—' }}</p>
    <p><strong>Received At:</strong> {{ $event->received_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
    <p><strong>Processed At:</strong> {{ $event->processed_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
    <p><strong>Error Message:</strong> {{ $event->error_message ?? '—' }}</p>

    @if ($event->payment)
        <p><a href="{{ route('admin.payments.show', $event->payment) }}">Open related payment</a></p>
    @endif

    <h3 style="margin-top:1.5rem;">Safe Headers</h3>
    <pre style="white-space:pre-wrap;">{{ json_encode($safeHeaders, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

    <h3 style="margin-top:1.5rem;">Safe Payload</h3>
    <pre style="white-space:pre-wrap;">{{ json_encode($safePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
@endsection
