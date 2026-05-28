@extends('layouts.admin.app')

@section('title', 'Membership Order Detail')

@section('content')
    <h2>{{ $order->order_number }}</h2>

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;">
        <div>
            <h3>Member</h3>
            <p><strong>Name:</strong> {{ $order->full_name }}</p>
            <p><strong>Email:</strong> {{ $order->email }}</p>
            <p><strong>Status:</strong> {{ $order->status->value }}</p>
            <p><strong>Plan:</strong> {{ $order->membership_plan }}</p>
            <p><strong>Price:</strong> {{ number_format((float) $order->membership_price, 2) }} {{ $order->currency }}</p>
            <p><strong>Paid At:</strong> {{ $order->paid_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Starts At:</strong> {{ $order->starts_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Expires At:</strong> {{ $order->expires_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
            <p><strong>Cancelled At:</strong> {{ $order->cancelled_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
        </div>

        <div>
            <h3>Payment</h3>
            @if ($payment)
                <p><strong>Payment ID:</strong> {{ $payment->id }}</p>
                <p><strong>Status:</strong> {{ $payment->status->value }}</p>
                <p><strong>Provider:</strong> {{ $payment->provider }}</p>
                <p><strong>Provider Order ID:</strong> {{ $payment->provider_order_id ?? '—' }}</p>
                <p><strong>Provider Capture ID:</strong> {{ $payment->provider_capture_id ?? '—' }}</p>
                <p><strong>Amount:</strong> {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</p>
                <p><a href="{{ route('admin.payments.show', $payment) }}">Open payment detail</a></p>
            @else
                <p>No payment associated.</p>
            @endif
        </div>
    </div>

    <h3 style="margin-top:1.5rem;">Safe Metadata</h3>
    <pre style="white-space:pre-wrap;">{{ json_encode($safeMetadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

    @if ($payment)
        <p><a href="{{ route('admin.payment-events.index', ['search' => $payment->provider_order_id]) }}">View related events</a></p>
    @endif
@endsection
