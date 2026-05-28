<label style="display:grid;gap:0.3rem;">
    <span>Nombre *</span>
    <input type="text" name="name" value="{{ old('name', $zone?->name) }}" required class="admin-button" style="width:100%;">
    @error('name')<small style="color:#991b1b;">{{ $message }}</small>@enderror
</label>

<label style="display:grid;gap:0.3rem;">
    <span>Descripción</span>
    <textarea name="description" rows="3" class="admin-button" style="width:100%;resize:vertical;">{{ old('description', $zone?->description) }}</textarea>
</label>

<div style="display:grid;gap:1rem;grid-template-columns:1fr 1fr;">
    <label style="display:grid;gap:0.3rem;">
        <span>Icono (Material Symbol)</span>
        <input type="text" name="icon" value="{{ old('icon', $zone?->icon ?? 'stadium') }}" placeholder="stadium" class="admin-button" style="width:100%;">
        <small style="color:#64748b;">Nombre del icono: family_restroom, restaurant, sports_soccer, music_note, star, handshake…</small>
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Orden</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $zone?->sort_order ?? 0) }}" min="0" class="admin-button" style="width:100%;">
    </label>
</div>

<label style="display:flex;gap:0.5rem;align-items:center;">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $zone?->is_active ?? true))>
    <span>Zona activa</span>
</label>
