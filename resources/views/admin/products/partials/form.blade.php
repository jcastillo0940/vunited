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
        <span>Category</span>
        <select name="product_category_id">
            <option value="">No category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('product_category_id', $product->product_category_id) === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </label>

    <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));">
        <label style="display:grid; gap:0.35rem;">
            <span>SKU</span>
            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Name</span>
            <input type="text" name="name" value="{{ old('name', $product->name) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Slug</span>
            <input type="text" name="slug" value="{{ old('slug', $product->slug) }}">
        </label>
    </div>

    <label style="display:grid; gap:0.35rem;">
        <span>Short description</span>
        <textarea name="short_description" rows="3">{{ old('short_description', $product->short_description) }}</textarea>
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Description</span>
        <textarea name="description" rows="5">{{ old('description', $product->description) }}</textarea>
    </label>

    <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));">
        <label style="display:grid; gap:0.35rem;">
            <span>Price</span>
            <input type="number" step="0.01" min="0.01" name="price" value="{{ old('price', $product->price) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Compare at price</span>
            <input type="number" step="0.01" min="0" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Currency</span>
            <input type="text" maxlength="3" name="currency" value="{{ old('currency', $product->currency) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Sort order</span>
            <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}">
        </label>
    </div>

    <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));">
        <label style="display:grid; gap:0.35rem;">
            <span>Stock quantity</span>
            <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Badge</span>
            <input type="text" name="badge" value="{{ old('badge', $product->badge) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Image path or URL</span>
            <input type="text" name="image_path" value="{{ old('image_path', $product->image_path) }}">
        </label>
    </div>

    <label style="display:grid; gap:0.35rem;">
        <span>Gallery (JSON array or one URL/path per line)</span>
        <textarea name="gallery" rows="5">{{ old('gallery', $product->gallery ? json_encode($product->gallery, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Metadata JSON</span>
        <textarea name="metadata" rows="5">{{ old('metadata', $product->metadata ? json_encode($product->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
    </label>

    <div style="display:grid; gap:0.75rem;">
        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="hidden" name="track_stock" value="0">
            <input type="checkbox" name="track_stock" value="1" @checked(old('track_stock', $product->track_stock))>
            <span>Track stock</span>
        </label>

        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))>
            <span>Featured product</span>
        </label>

        <label style="display:flex; align-items:center; gap:0.5rem;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
            <span>Active product</span>
        </label>
    </div>

    <div>
        <button type="submit" class="admin-button">Save product</button>
    </div>
</form>
