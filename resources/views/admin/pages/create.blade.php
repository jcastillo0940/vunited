@extends('layouts.admin.app')

@section('title', 'Create Page')

@section('content')
    <section>
        <h2 style="margin-top: 0;">Create Page</h2>
        <p style="color: #475569;">Pages are saved with optional sections.</p>

        <form method="POST" action="{{ route('admin.pages.store') }}" enctype="multipart/form-data" style="display: grid; gap: 1rem;">
            @csrf
            <label style="display: grid; gap: 0.35rem;">
                <span>Title</span>
                <input type="text" name="title" value="{{ old('title') }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Slug</span>
                <input type="text" name="slug" value="{{ old('slug') }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Excerpt</span>
                <textarea name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Status</span>
                <select name="status">
                    @foreach (['draft', 'published', 'scheduled', 'archived'] as $status)
                        <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Published at</span>
                <input type="datetime-local" name="published_at" value="{{ old('published_at') }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>SEO title</span>
                <input type="text" name="seo_title" value="{{ old('seo_title') }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>SEO description</span>
                <textarea name="seo_description" rows="3">{{ old('seo_description') }}</textarea>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Page image upload</span>
                <input type="file" name="page_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            </label>

            <label style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="hidden" name="is_home" value="0">
                <input type="checkbox" name="is_home" value="1" @checked(old('is_home'))>
                <span>Mark as home page</span>
            </label>

            <div>
                <button type="submit" class="admin-button">Save page</button>
            </div>
        </form>
    </section>
@endsection
