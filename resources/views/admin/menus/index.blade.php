@extends('layouts.admin.app')

@section('title', 'Menus')

@section('content')
    <section>
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <h2 style="margin: 0;">Menus</h2>
                <p style="margin: 0.5rem 0 0; color: #475569;">Manage header and footer menus.</p>
            </div>

            <a href="{{ route('admin.menus.create') }}" class="admin-button">Create menu</a>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Name</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Location</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Items</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($menus as $menu)
                    <tr>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">
                            <a href="{{ route('admin.menus.edit', $menu) }}">{{ $menu->name }}</a>
                        </td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $menu->location }}</td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $menu->items_count }}</td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $menu->is_active ? 'Active' : 'Inactive' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 0.75rem;">No menus found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
