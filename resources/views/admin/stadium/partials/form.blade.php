<div style="display:grid;gap:1rem;grid-template-columns:repeat(2,minmax(0,1fr));">

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Nombre *</span>
        <input type="text" name="name" value="{{ old('name', $stadium?->name) }}" required class="admin-button" style="width:100%;">
        @error('name')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Subtítulo</span>
        <input type="text" name="subtitle" value="{{ old('subtitle', $stadium?->subtitle) }}" class="admin-button" style="width:100%;" placeholder="Casa oficial de...">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Ubicación</span>
        <input type="text" name="location" value="{{ old('location', $stadium?->location) }}" class="admin-button" style="width:100%;" placeholder="Ciudad, Provincia">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Dirección</span>
        <input type="text" name="address" value="{{ old('address', $stadium?->address) }}" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Capacidad</span>
        <input type="text" name="capacity" value="{{ old('capacity', $stadium?->capacity) }}" class="admin-button" style="width:100%;" placeholder="8,500">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Tipo de sede</span>
        <input type="text" name="venue_type" value="{{ old('venue_type', $stadium?->venue_type) }}" class="admin-button" style="width:100%;" placeholder="Sede principal del club">
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>URL imagen hero</span>
        <input type="text" name="hero_image_path" value="{{ old('hero_image_path', $stadium?->hero_image_path) }}" class="admin-button" style="width:100%;" placeholder="https://...">
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>URL embed mapa (iframe src)</span>
        <input type="url" name="map_embed_url" value="{{ old('map_embed_url', $stadium?->map_embed_url) }}" class="admin-button" style="width:100%;" placeholder="https://maps.google.com/embed?...">
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Zonas (JSON)</span>
        <textarea name="zones" rows="5" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.82rem;">{{ old('zones', $stadium?->zones ? json_encode($stadium->zones, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        @error('zones')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Experiencia matchday (JSON)</span>
        <textarea name="matchday" rows="5" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.82rem;">{{ old('matchday', $stadium?->matchday ? json_encode($stadium->matchday, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Reglas (JSON — array de strings)</span>
        <textarea name="rules" rows="4" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.82rem;">{{ old('rules', $stadium?->rules ? json_encode($stadium->rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Metadata (JSON — hero, mapa, CTA)</span>
        <textarea name="metadata" rows="8" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.82rem;">{{ old('metadata', $stadium?->metadata ? json_encode($stadium->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    </label>

    <div style="display:flex;align-items:center;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $stadium?->is_active ?? true))>
            <span>Activo</span>
        </label>
    </div>

</div>
