@extends('layouts.admin.app')

@section('title', 'Membership Plans')

@section('content')
    <section>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem;">
            <div>
                <h2 style="margin:0;">Membership Plans</h2>
                <p style="margin:0.5rem 0 0; color:#475569;">Gestiona precio, beneficios y activacion del plan publico.</p>
            </div>

            @if(auth('admin')->user()->hasPermission('membership_plans.manage'))
                <a href="{{ route('admin.membership-plans.create') }}" class="admin-button">Create plan</a>
            @endif
        </div>

        @if (session('error'))
            <div style="margin-bottom: 1rem; padding: 1rem; border-radius: 0.75rem; background: #fee2e2; color: #991b1b;">
                {{ session('error') }}
            </div>
        @endif

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid #e2e8f0;">
                        <th style="padding:0.75rem;">Order</th>
                        <th style="padding:0.75rem;">Code</th>
                        <th style="padding:0.75rem;">Name</th>
                        <th style="padding:0.75rem;">Price</th>
                        <th style="padding:0.75rem;">Duration</th>
                        <th style="padding:0.75rem;">Status</th>
                        <th style="padding:0.75rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:0.75rem;">{{ $plan->sort_order }}</td>
                            <td style="padding:0.75rem;">{{ $plan->code }}</td>
                            <td style="padding:0.75rem;">{{ $plan->name }}</td>
                            <td style="padding:0.75rem;">{{ number_format((float) $plan->price, 2) }} {{ $plan->currency }}</td>
                            <td style="padding:0.75rem;">{{ $plan->duration_months }} meses</td>
                            <td style="padding:0.75rem;">
                                @if ($plan->is_active)
                                    <span style="display:inline-block; padding:0.25rem 0.6rem; border-radius:999px; background:#dcfce7; color:#166534; font-size:0.75rem; font-weight:700;">Active</span>
                                @else
                                    <span style="display:inline-block; padding:0.25rem 0.6rem; border-radius:999px; background:#e2e8f0; color:#475569; font-size:0.75rem; font-weight:700;">Inactive</span>
                                @endif
                            </td>
                            <td style="padding:0.75rem; display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                                @if(auth('admin')->user()->hasPermission('membership_plans.manage'))
                                    <a href="{{ route('admin.membership-plans.edit', $plan) }}" class="admin-button">Edit</a>
                                    <form method="POST" action="{{ route('admin.membership-plans.destroy', $plan) }}" onsubmit="return confirm('Delete this membership plan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-button">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:1rem; color:#64748b;">No membership plans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
