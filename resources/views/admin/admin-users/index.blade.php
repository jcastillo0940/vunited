@extends('layouts.admin.app')

@section('title', 'Admin Users')

@section('content')
    <section>
        <h2 style="margin-top: 0;">Admin Users</h2>
        <p>Basic administrative user listing for Phase B.</p>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Name</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($adminUsers as $adminUser)
                    <tr>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $adminUser->name }}</td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $adminUser->email }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="padding: 0.75rem;">No admin users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
