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
        <span>Cargo / Rol *</span>
        <input type="text" name="role" value="{{ old('role', $member?->role) }}" placeholder="Presidente Ejecutivo" required class="admin-button" style="width:100%;">
        @error('role')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Grupo *</span>
        <select name="group" class="admin-button" style="width:100%;" required>
            @foreach(['presidency' => 'Presidencia', 'executive_staff' => 'Staff Ejecutivo', 'board' => 'Junta Directiva', 'transparency' => 'Gobernanza'] as $key => $label)
                <option value="{{ $key }}" @selected(old('group', $member?->group ?? 'board') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('group')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>URL Foto</span>
        <input type="text" name="photo_path" value="{{ old('photo_path', $member?->photo_path) }}" placeholder="https://..." class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email', $member?->email) }}" placeholder="contacto@veraguasunited.test" class="admin-button" style="width:100%;">
        @error('email')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Biografía / Descripción</span>
        <textarea name="biography" rows="4" class="admin-button" style="width:100%;resize:vertical;">{{ old('biography', $member?->biography) }}</textarea>
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Metadata (JSON opcional)</span>
        <textarea name="metadata" rows="5" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.8rem;">{{ old('metadata', $member ? json_encode($member->metadata, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        <small style="color:#64748b;">Ej: {"area":"Operaciones","tone":"primary","icons":["groups","mail"],"category":"Junta Directiva"}</small>
    </label>

    <div style="display:flex;gap:1rem;align-items:center;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member?->is_active ?? true))>
            <span>Activo</span>
        </label>
    </div>

    <label style="display:grid;gap:0.3rem;">
        <span>Orden</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $member?->sort_order ?? 0) }}" min="0" class="admin-button" style="width:100%;">
    </label>

</div>
