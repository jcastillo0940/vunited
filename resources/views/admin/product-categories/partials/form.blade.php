@if ($errors->any())
    <div style="margin-bottom: 1rem; padding: 1rem; border-radius: 0.75rem; background: #fee2e2; color: #991b1b;">
        Please correct the highlighted fields.
    </div>
@endif

<form method="POST" action="{{ $action }}" style="display:grid; gap:1rem;">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <label style="display:grid; gap:0.35rem;">
        <span>Name</span>
        <input type="text" name="name" value="{{ old('name', $category->name) }}">
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Slug</span>
        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}">
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Description</span>
        <textarea name="description" rows="4">{{ old('description', $category->description) }}</textarea>
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Sort order</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Metadata JSON</span>
        <textarea name="metadata" rows="5">{{ old('metadata', $category->metadata ? json_encode($category->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
    </label>

    <label style="display:flex; align-items:center; gap:0.5rem;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
        <span>Active category</span>
    </label>

    <div>
        <button type="submit" class="admin-button">Save category</button>
    </div>
</form>
