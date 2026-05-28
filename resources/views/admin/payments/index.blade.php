@extends('layouts.admin.app')

@section('title', 'Payments')

@section('content')
    <h2>Payments</h2>

    <form method="GET" action="{{ route('admin.payments.index') }}" style="display:grid;grid-template-columns:1fr 1fr auto;gap:1rem;margin-bottom:1.5rem;">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search order, capture, email or name" class="admin-button" style="text-align:left;">
        <select name="status" class="admin-button">
            <option value="">All statuses</option>
            @foreach (['pending', 'provider_created', 'approved', 'captured', 'failed', 'cancelled', 'refunded'] as $status)
                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-button">Filter</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th align="left">ID</th>
                    <th align="left">Provider</th>
                    <th align="left">Status</th>
                    <th align="left">Amount</th>
                    <th align="left">Payable</th>
                    <th align="left">Provider Order ID</th>
                    <th align="left">Provider Capture ID</th>
                    <th align="left">Created</th>
                    <th align="left"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr style="border-top:1px solid #e2e8f0;">
                        <td style="padding:0.85rem 0;">{{ $payment->id }}</td>
                        <td>{{ $payment->provider }}</td>
                        <td>{{ $payment->status->value }}</td>
                        <td>{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td>
                        <td>{{ class_basename($payment->payable_type ?? '—') }} {{ $payment->payable_id ?? '' }}</td>
                        <td>{{ $payment->provider_order_id ?? '—' }}</td>
                        <td>{{ $payment->provider_capture_id ?? '—' }}</td>
                        <td>{{ $payment->created_at?->format('Y-m-d H:i') }}</td>
                        <td><a href="{{ route('admin.payments.show', $payment) }}">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding:1rem 0;">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
