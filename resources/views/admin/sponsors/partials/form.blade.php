<div style="display:grid;gap:1rem;grid-template-columns:repeat(2,minmax(0,1fr));">

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Nombre *</span>
        <input type="text" name="name" value="{{ old('name', $sponsor?->name) }}" required class="admin-button" style="width:100%;">
        @error('name')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Slug (URL)</span>
        <input type="text" name="slug" value="{{ old('slug', $sponsor?->slug) }}" placeholder="auto-generado si vacío" class="admin-button" style="width:100%;">
        @error('slug')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Nivel (Tier) *</span>
        <select name="tier" class="admin-button" style="width:100%;" required>
            @foreach(['main_partner' => 'Main Partner', 'official_sponsor' => 'Official Sponsor', 'strategic_ally' => 'Alianza Estratégica'] as $key => $label)
                <option value="{{ $key }}" @selected(old('tier', $sponsor?->tier ?? 'official_sponsor') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('tier')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>URL Logo / Imagen</span>
        <input type="text" name="logo_path" value="{{ old('logo_path', $sponsor?->logo_path) }}" placeholder="https://..." class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Sitio web</span>
        <input type="url" name="website_url" value="{{ old('website_url', $sponsor?->website_url) }}" placeholder="https://ejemplo.com" class="admin-button" style="width:100%;">
        @error('website_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Descripción / Tagline</span>
        <textarea name="description" rows="3" class="admin-button" style="width:100%;resize:vertical;">{{ old('description', $sponsor?->description) }}</textarea>
    </label>

    <div style="display:flex;gap:1rem;align-items:center;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $sponsor?->is_active ?? true))>
            <span>Activo</span>
        </label>
    </div>

    <label style="display:grid;gap:0.3rem;">
        <span>Orden</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $sponsor?->sort_order ?? 0) }}" min="0" class="admin-button" style="width:100%;">
    </label>

</div>
