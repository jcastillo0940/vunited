@extends('layouts.admin.app')

@section('title', 'Store Order Detail')

@section('content')
    <h2>{{ $order->order_number }}</h2>

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;">
        <div>
            <h3>Customer</h3>
            <p><strong>Name:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
            <p><strong>Phone:</strong> {{ $order->customer_phone ?? '—' }}</p>
            <p><strong>Status:</strong> {{ $order->status->value }}</p>
            <p><strong>Subtotal:</strong> {{ number_format((float) $order->subtotal, 2) }} {{ $order->currency }}</p>
            <p><strong>Discount:</strong> {{ number_format((float) $order->discount_total, 2) }} {{ $order->currency }}</p>
            <p><strong>Tax:</strong> {{ number_format((float) $order->tax_total, 2) }} {{ $order->currency }}</p>
            <p><strong>Total:</strong> {{ number_format((float) $order->total, 2) }} {{ $order->currency }}</p>
            <p><strong>Paid At:</strong> {{ $order->paid_at?->format('Y-m-d H:i:s') ?? '—' }}</p>
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
                    <th style="padding:0.75rem;">Product</th>
                    <th style="padding:0.75rem;">SKU</th>
                    <th style="padding:0.75rem;">Qty</th>
                    <th style="padding:0.75rem;">Unit Price</th>
                    <th style="padding:0.75rem;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">{{ $item->product_name }}</td>
                        <td style="padding:0.75rem;">{{ $item->product_sku ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $item->quantity }}</td>
                        <td style="padding:0.75rem;">{{ number_format((float) $item->unit_price, 2) }} {{ $order->currency }}</td>
                        <td style="padding:0.75rem;">{{ number_format((float) $item->line_total, 2) }} {{ $order->currency }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h3 style="margin-top:1.5rem;">Safe Metadata</h3>
    <pre style="white-space:pre-wrap;">{{ json_encode($safeMetadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
@endsection
