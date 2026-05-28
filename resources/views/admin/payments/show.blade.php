@extends('layouts.admin.app')

@section('title', 'Payment Detail')

@section('content')
    <h2>Payment #{{ $payment->id }}</h2>

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;">
        <div>
            <h3>Core Data</h3>
            <p><strong>Provider:</strong> {{ $payment->provider }}</p>
            <p><strong>Status:</strong> {{ $payment->status->value }}</p>
            <p><strong>Amount:</strong> {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</p>
            <p><strong>Provider Order ID:</strong> {{ $payment->provider_order_id ?? '—' }}</p>
            <p><strong>Provider Capture ID:</strong> {{ $payment->provider_capture_id ?? '—' }}</p>
            <p><strong>Customer:</strong> {{ $payment->customer_name ?? '—' }} / {{ $payment->customer_email ?? '—' }}</p>
            <p><strong>Payable:</strong>
                @if ($payment->payable)
                    {{ class_basename($payment->payable_type) }} #{{ $payment->payable_id }}
                @else
                    —
                @endif
            </p>
            @if ($payment->payable_type === \App\Domain\Memberships\Models\MembershipOrder::class && $payment->payable)
                <p><a href="{{ route('admin.membership-orders.show', $payment->payable) }}">Open membership order</a></p>
            @endif
            @if ($payment->payable_type === \App\Domain\Store\Models\StoreOrder::class && $payment->payable)
                <p><a href="{{ route('admin.store-orders.show', $payment->payable) }}">Open store order</a></p>
            @endif
        </div>

        <div>
            <h3>Timestamps</h3>
            <p><strong>Approved At:</strong> {{ $payment->approved_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Captured At:</strong> {{ $payment->captured_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Failed At:</strong> {{ $payment->failed_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Cancelled At:</strong> {{ $payment->cancelled_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Refunded At:</strong> {{ $payment->refunded_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
        </div>
    </div>

    <h3 style="margin-top:1.5rem;">Safe Metadata</h3>
    <pre style="white-space:pre-wrap;">{{ json_encode($safeMetadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

    <h3 style="margin-top:1.5rem;">Safe Provider Payload</h3>
    <pre style="white-space:pre-wrap;">{{ json_encode($safeProviderPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

    <h3 style="margin-top:1.5rem;">Related Events</h3>
    <ul>
        @forelse ($payment->paymentEvents as $event)
            <li>
                <a href="{{ route('admin.payment-events.show', $event) }}">
                    {{ $event->provider_event_id }} - {{ $event->event_type }}
                </a>
            </li>
        @empty
            <li>No related events.</li>
        @endforelse
    </ul>
@endsection
