<div style="display:grid;gap:1rem;grid-template-columns:repeat(2,minmax(0,1fr));">

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Nombre completo *</span>
        <input type="text" name="name" value="{{ old('name', $player?->name) }}" required class="admin-button" style="width:100%;">
        @error('name')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Slug (URL)</span>
        <input type="text" name="slug" value="{{ old('slug', $player?->slug) }}" placeholder="auto-generado si vacío" class="admin-button" style="width:100%;">
        @error('slug')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Número</span>
        <input type="text" name="number" value="{{ old('number', $player?->number) }}" placeholder="01" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Posición (display)</span>
        <input type="text" name="position" value="{{ old('position', $player?->position) }}" placeholder="Portero" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Posición key</span>
        <select name="position_key" class="admin-button" style="width:100%;">
            <option value="">Sin especificar</option>
            @foreach(['goalkeeper' => 'Portero', 'defender' => 'Defensa', 'midfielder' => 'Volante', 'forward' => 'Delantero'] as $key => $label)
                <option value="{{ $key }}" @selected(old('position_key', $player?->position_key) === $key)>{{ $label }} ({{ $key }})</option>
            @endforeach
        </select>
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Categoría *</span>
        <select name="category" class="admin-button" style="width:100%;" required>
            @foreach(['first-team' => 'Primer Equipo (LPF)', 'women-team' => 'Equipo Femenino (LFF)', 'academy' => 'Cantera'] as $key => $label)
                <option value="{{ $key }}" @selected(old('category', $player?->category ?? 'first-team') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Fecha de nacimiento</span>
        <input type="date" name="birth_date" value="{{ old('birth_date', $player?->birth_date?->toDateString()) }}" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Nacionalidad</span>
        <input type="text" name="nationality" value="{{ old('nationality', $player?->nationality) }}" placeholder="Panameño" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Estatura</span>
        <input type="text" name="height" value="{{ old('height', $player?->height) }}" placeholder="1.80 m" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Peso</span>
        <input type="text" name="weight" value="{{ old('weight', $player?->weight) }}" placeholder="75 kg" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Pie dominante</span>
        <select name="dominant_foot" class="admin-button" style="width:100%;">
            <option value="">Sin especificar</option>
            @foreach(['Derecho', 'Zurdo', 'Ambidiestro'] as $foot)
                <option value="{{ $foot }}" @selected(old('dominant_foot', $player?->dominant_foot) === $foot)>{{ $foot }}</option>
            @endforeach
        </select>
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>URL Foto</span>
        <input type="text" name="photo_path" value="{{ old('photo_path', $player?->photo_path) }}" placeholder="https://..." class="admin-button" style="width:100%;">
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Biografía</span>
        <textarea name="biography" rows="4" class="admin-button" style="width:100%;resize:vertical;">{{ old('biography', $player?->biography) }}</textarea>
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Stats (JSON array)</span>
        <textarea name="stats" rows="6" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.8rem;">{{ old('stats', $player ? json_encode($player->stats, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        <small style="color:#64748b;">Ej: [{"key":"matches","label":"Partidos Jugados","value":"18","tone":"primary"}]</small>
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Attributes (JSON array)</span>
        <textarea name="attributes" rows="6" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.8rem;">{{ old('attributes', $player ? json_encode($player->attributes, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        <small style="color:#64748b;">Ej: [{"key":"speed","label":"Velocidad","value":90}]</small>
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Galería (JSON array)</span>
        <textarea name="gallery" rows="4" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.8rem;">{{ old('gallery', $player ? json_encode($player->gallery, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        <small style="color:#64748b;">Ej: [{"id":1,"type":"image","label":"Celebración","imageUrl":"https://..."}]</small>
    </label>

    <div style="display:flex;gap:1rem;align-items:center;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $player?->is_active ?? true))>
            <span>Activo</span>
        </label>
    </div>

    <label style="display:grid;gap:0.3rem;">
        <span>Orden</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $player?->sort_order ?? 0) }}" min="0" class="admin-button" style="width:100%;">
    </label>

</div>
