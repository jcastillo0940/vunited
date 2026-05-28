@extends('layouts.admin.app')

@section('title', 'News')

@section('content')
    <section>
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <h2 style="margin: 0;">News</h2>
                <p style="margin: 0.5rem 0 0; color: #475569;">Manage news articles and optional categories.</p>
            </div>

            <a href="{{ route('admin.news.create') }}" class="admin-button">Create article</a>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Title</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Category</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Status</th>
                    <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Featured</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($articles as $article)
                    <tr>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">
                            <a href="{{ route('admin.news.edit', $article) }}">{{ $article->title }}</a>
                        </td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ optional($article->category)->name ?? 'No category' }}</td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $article->status }}</td>
                        <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $article->is_featured ? 'Yes' : 'No' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 0.75rem;">No news articles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
