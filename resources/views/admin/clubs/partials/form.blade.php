<div style="display:grid;gap:1rem;grid-template-columns:repeat(2,minmax(0,1fr));">

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Nombre *</span>
        <input type="text" name="name" value="{{ old('name', $club?->name) }}" required class="admin-button" style="width:100%;">
        @error('name')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Código corto (máx. 10 chars)</span>
        <input type="text" name="short_name" value="{{ old('short_name', $club?->short_name) }}" maxlength="10" placeholder="VUA" class="admin-button" style="width:100%;">
        @error('short_name')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Slug (URL)</span>
        <input type="text" name="slug" value="{{ old('slug', $club?->slug) }}" placeholder="auto-generado" class="admin-button" style="width:100%;">
        @error('slug')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Ciudad</span>
        <input type="text" name="city" value="{{ old('city', $club?->city) }}" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>URL Logo</span>
        <input type="text" name="logo_path" value="{{ old('logo_path', $club?->logo_path) }}" placeholder="https://..." class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Color primario (#hex)</span>
        <input type="text" name="primary_color" value="{{ old('primary_color', $club?->primary_color) }}" placeholder="#1D428A" maxlength="7" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Color secundario (#hex)</span>
        <input type="text" name="secondary_color" value="{{ old('secondary_color', $club?->secondary_color) }}" placeholder="#FFFFFF" maxlength="7" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Orden</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $club?->sort_order ?? 0) }}" min="0" class="admin-button" style="width:100%;">
    </label>

    <div style="display:flex;gap:1rem;align-items:center;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $club?->is_active ?? true))>
            <span>Activo</span>
        </label>
    </div>

</div>
