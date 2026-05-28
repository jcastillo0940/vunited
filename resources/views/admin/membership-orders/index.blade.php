@extends('layouts.admin.app')

@section('title', 'Membership Orders')

@section('content')
    <h2>Membership Orders</h2>

    <form method="GET" action="{{ route('admin.membership-orders.index') }}" style="display:grid;grid-template-columns:1fr 1fr auto;gap:1rem;margin-bottom:1.5rem;">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search order, name or email" class="admin-button" style="text-align:left;">
        <select name="status" class="admin-button">
            <option value="">All statuses</option>
            @foreach (['pending_payment', 'paid', 'failed', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-button">Filter</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th align="left">Order</th>
                    <th align="left">Member</th>
                    <th align="left">Email</th>
                    <th align="left">Status</th>
                    <th align="left">Price</th>
                    <th align="left">Paid At</th>
                    <th align="left">Created</th>
                    <th align="left"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr style="border-top:1px solid #e2e8f0;">
                        <td style="padding:0.85rem 0;">{{ $order->order_number }}</td>
                        <td>{{ $order->full_name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->status->value }}</td>
                        <td>{{ number_format((float) $order->membership_price, 2) }} {{ $order->currency }}</td>
                        <td>{{ $order->paid_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                        <td><a href="{{ route('admin.membership-orders.show', $order) }}">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:1rem 0;">No membership orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
