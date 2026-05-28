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
        <span>Code</span>
        <input type="text" name="code" value="{{ old('code', $plan->code) }}">
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Name</span>
        <input type="text" name="name" value="{{ old('name', $plan->name) }}">
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Headline</span>
        <input type="text" name="headline" value="{{ old('headline', $plan->headline) }}">
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Description</span>
        <textarea name="description" rows="4">{{ old('description', $plan->description) }}</textarea>
    </label>

    <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));">
        <label style="display:grid; gap:0.35rem;">
            <span>Price</span>
            <input type="number" step="0.01" min="0.01" name="price" value="{{ old('price', $plan->price) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Currency</span>
            <input type="text" maxlength="3" name="currency" value="{{ old('currency', $plan->currency) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Duration (months)</span>
            <input type="number" min="1" name="duration_months" value="{{ old('duration_months', $plan->duration_months) }}">
        </label>

        <label style="display:grid; gap:0.35rem;">
            <span>Sort order</span>
            <input type="number" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}">
        </label>
    </div>

    <label style="display:grid; gap:0.35rem;">
        <span>Benefits (one per line)</span>
        <textarea name="benefits" rows="5">{{ old('benefits', implode(PHP_EOL, $plan->benefits ?? [])) }}</textarea>
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Kit items (one per line)</span>
        <textarea name="kit_items" rows="5">{{ old('kit_items', implode(PHP_EOL, $plan->kit_items ?? [])) }}</textarea>
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Partner discounts (one per line)</span>
        <textarea name="partner_discounts" rows="5">{{ old('partner_discounts', implode(PHP_EOL, $plan->partner_discounts ?? [])) }}</textarea>
    </label>

    <label style="display:grid; gap:0.35rem;">
        <span>Metadata JSON (optional)</span>
        <textarea name="metadata" rows="6">{{ old('metadata', $plan->metadata ? json_encode($plan->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
    </label>

    <label style="display:flex; align-items:center; gap:0.5rem;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active))>
        <span>Active public plan</span>
    </label>

    <div>
        <button type="submit" class="admin-button">Save plan</button>
    </div>
</form>
