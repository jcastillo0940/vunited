@extends('layouts.admin.app')

@section('title', 'Roles')

@section('content')
    <section>
        <h2 style="margin-top: 0;">Roles</h2>
        <p>Basic role listing for Phase B.</p>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Name</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Permissions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $role->label }}</td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $role->permissions->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="padding: 0.75rem;">No roles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
