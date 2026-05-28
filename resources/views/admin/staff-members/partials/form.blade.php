<div style="display:grid;gap:1rem;grid-template-columns:repeat(2,minmax(0,1fr));">

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Nombre completo *</span>
        <input type="text" name="name" value="{{ old('name', $member?->name) }}" required class="admin-button" style="width:100%;">
        @error('name')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Slug (URL)</span>
        <input type="text" name="slug" value="{{ old('slug', $member?->slug) }}" placeholder="auto-generado si vacío" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Rol / Cargo *</span>
        <input type="text" name="role" value="{{ old('role', $member?->role) }}" placeholder="Director Técnico" required class="admin-button" style="width:100%;">
        @error('role')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Categoría *</span>
        <select name="category" class="admin-button" style="width:100%;" required>
            @foreach(['first-team' => 'Primer Equipo', 'women-team' => 'Equipo Femenino', 'academy' => 'Cantera', 'general' => 'General'] as $key => $label)
                <option value="{{ $key }}" @selected(old('category', $member?->category ?? 'first-team') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>URL Foto</span>
        <input type="text" name="photo_path" value="{{ old('photo_path', $member?->photo_path) }}" placeholder="https://..." class="admin-button" style="width:100%;">
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Biografía</span>
        <textarea name="biography" rows="4" class="admin-button" style="width:100%;resize:vertical;">{{ old('biography', $member?->biography) }}</textarea>
    </label>

    <div style="display:flex;gap:1rem;align-items:center;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member?->is_active ?? true))>
            <span>Activo</span>
        </label>
    </div>

    <label style="display:grid;gap:0.3rem;">
        <span>Orden (0 = principal)</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $member?->sort_order ?? 0) }}" min="0" class="admin-button" style="width:100%;">
    </label>

</div>
