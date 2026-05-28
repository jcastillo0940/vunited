@extends('layouts.admin.app')

@section('title', 'Edit Page')

@section('content')
    <section style="display: grid; gap: 2rem;">
        <div>
            <h2 style="margin-top: 0;">Edit Page</h2>

            <form method="POST" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data" style="display: grid; gap: 1rem;">
                @csrf
                @method('PUT')

                <label style="display: grid; gap: 0.35rem;">
                    <span>Title</span>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}">
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Slug</span>
                    <input type="text" name="slug" value="{{ old('slug', $page->slug) }}">
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Excerpt</span>
                    <textarea name="excerpt" rows="3">{{ old('excerpt', $page->excerpt) }}</textarea>
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Status</span>
                    <select name="status">
                        @foreach (['draft', 'published', 'scheduled', 'archived'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $page->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Published at</span>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($page->published_at)->format('Y-m-d\\TH:i')) }}">
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>SEO title</span>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $page->seo_title) }}">
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>SEO description</span>
                    <textarea name="seo_description" rows="3">{{ old('seo_description', $page->seo_description) }}</textarea>
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Page image upload</span>
                    <input type="file" name="page_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                </label>

                <label style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="hidden" name="is_home" value="0">
                    <input type="checkbox" name="is_home" value="1" @checked(old('is_home', $page->is_home))>
                    <span>Mark as home page</span>
                </label>

                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" class="admin-button">Update page</button>
                </div>
            </form>
        </div>

        <div>
            <h3>Sections</h3>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Key</th>
                        <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Type</th>
                        <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Order</th>
                        <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Active</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($page->sections as $section)
                        <tr>
                            <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $section->section_key }}</td>
                            <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $section->type }}</td>
                            <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $section->sort_order }}</td>
                            <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $section->is_active ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 0.75rem;">No sections found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
