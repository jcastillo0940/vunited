@extends('layouts.admin.app')

@section('title', 'Edit News Article')

@section('content')
    <section style="display: grid; gap: 1rem;">
        <h2 style="margin-top: 0;">Edit News Article</h2>

        <form method="POST" action="{{ route('admin.news.update', $newsArticle) }}" enctype="multipart/form-data" style="display: grid; gap: 1rem;">
            @csrf
            @method('PUT')

            <label style="display: grid; gap: 0.35rem;">
                <span>Category</span>
                <select name="news_category_id">
                    <option value="">No category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('news_category_id', $newsArticle->news_category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Title</span>
                <input type="text" name="title" value="{{ old('title', $newsArticle->title) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Slug</span>
                <input type="text" name="slug" value="{{ old('slug', $newsArticle->slug) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Summary</span>
                <textarea name="summary" rows="3">{{ old('summary', $newsArticle->summary) }}</textarea>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Body</span>
                <textarea name="body" rows="6">{{ old('body', $newsArticle->body) }}</textarea>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Featured image path</span>
                <input type="text" name="featured_image_path" value="{{ old('featured_image_path', $newsArticle->featured_image_path) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Featured image upload</span>
                <input type="file" name="featured_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Status</span>
                <select name="status">
                    @foreach (['draft', 'published', 'scheduled', 'archived'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $newsArticle->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Published at</span>
                <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($newsArticle->published_at)->format('Y-m-d\\TH:i')) }}">
            </label>

            <label style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $newsArticle->is_featured))>
                <span>Featured article</span>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>SEO title</span>
                <input type="text" name="seo_title" value="{{ old('seo_title', $newsArticle->seo_title) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>SEO description</span>
                <textarea name="seo_description" rows="3">{{ old('seo_description', $newsArticle->seo_description) }}</textarea>
            </label>

            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="admin-button">Update article</button>
            </div>
        </form>
    </section>
@endsection
