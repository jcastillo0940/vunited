<form method="POST" action="{{ $action }}" style="display:grid;gap:1rem;">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <label>
        <span>Name</span>
        <input type="text" name="name" value="{{ old('name', $ticketZone->name) }}" class="admin-input" required>
    </label>
    <label>
        <span>Slug</span>
        <input type="text" name="slug" value="{{ old('slug', $ticketZone->slug) }}" class="admin-input">
    </label>
    <label>
        <span>Description</span>
        <textarea name="description" rows="4" class="admin-input">{{ old('description', $ticketZone->description) }}</textarea>
    </label>
    <label>
        <span>Price</span>
        <input type="number" min="0.01" step="0.01" name="price" value="{{ old('price', $ticketZone->price) }}" class="admin-input" required>
    </label>
    <label>
        <span>Currency</span>
        <input type="text" maxlength="3" name="currency" value="{{ old('currency', $ticketZone->currency ?? 'USD') }}" class="admin-input" required>
    </label>
    <label>
        <span>Capacity</span>
        <input type="number" min="0" name="capacity" value="{{ old('capacity', $ticketZone->capacity) }}" class="admin-input">
    </label>
    <label>
        <span>Available Quantity</span>
        <input type="number" min="0" name="available_quantity" value="{{ old('available_quantity', $ticketZone->available_quantity) }}" class="admin-input">
    </label>
    <label>
        <span>Sort Order</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $ticketZone->sort_order ?? 0) }}" class="admin-input" required>
    </label>
    <label>
        <span>Metadata (JSON)</span>
        <textarea name="metadata" rows="5" class="admin-input">{{ old('metadata', $ticketZone->metadata ? json_encode($ticketZone->metadata, JSON_PRETTY_PRINT) : '') }}</textarea>
    </label>
    <label style="display:flex;gap:0.5rem;align-items:center;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ticketZone->is_active ?? true))>
        <span>Active</span>
    </label>

    @if($errors->any())
        <div style="padding:0.75rem 1rem;border-radius:0.75rem;background:#fee2e2;color:#991b1b;">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button type="submit" class="admin-button" style="width:max-content;">Save</button>
</form>
