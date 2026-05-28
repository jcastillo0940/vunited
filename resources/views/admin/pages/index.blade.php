@extends('layouts.admin.app')

@section('title', 'Pages')

@section('content')
    <section>
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <h2 style="margin: 0;">Pages</h2>
                <p style="margin: 0.5rem 0 0; color: #475569;">Manage site pages and their editable sections.</p>
            </div>

            <a href="{{ route('admin.pages.create') }}" class="admin-button">Create page</a>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Title</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Slug</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Status</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Sections</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pages as $page)
                    <tr>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">
                            <a href="{{ route('admin.pages.edit', $page) }}">{{ $page->title }}</a>
                        </td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $page->slug }}</td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $page->status }}</td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $page->sections_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 0.75rem;">No pages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
