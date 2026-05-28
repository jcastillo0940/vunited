<label style="display:grid;gap:0.3rem;">
    <span>Título *</span>
    <input type="text" name="title" value="{{ old('title', $event?->title) }}" required class="admin-button" style="width:100%;">
    @error('title')<small style="color:#991b1b;">{{ $message }}</small>@enderror
</label>

<label style="display:grid;gap:0.3rem;">
    <span>Slug</span>
    <input type="text" name="slug" value="{{ old('slug', $event?->slug) }}" placeholder="auto-generado si vacío" class="admin-button" style="width:100%;">
</label>

<div style="display:grid;gap:1rem;grid-template-columns:1fr 1fr;">
    <label style="display:grid;gap:0.3rem;">
        <span>Fecha del evento</span>
        <input type="datetime-local" name="event_date" value="{{ old('event_date', $event?->event_date?->format('Y-m-d\TH:i')) }}" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Lugar</span>
        <input type="text" name="location" value="{{ old('location', $event?->location) }}" class="admin-button" style="width:100%;">
    </label>
</div>

<label style="display:grid;gap:0.3rem;">
    <span>Descripción</span>
    <textarea name="description" rows="4" class="admin-button" style="width:100%;resize:vertical;">{{ old('description', $event?->description) }}</textarea>
</label>

<label style="display:grid;gap:0.3rem;">
    <span>URL Imagen hero</span>
    <input type="text" name="hero_image_path" value="{{ old('hero_image_path', $event?->hero_image_path) }}" placeholder="https://..." class="admin-button" style="width:100%;">
</label>

<label style="display:grid;gap:0.3rem;">
    <span>Programa / Schedule (JSON)</span>
    <textarea name="schedule" rows="6" class="admin-button" style="width:100%;resize:vertical;font-family:monospace;font-size:0.8rem;">{{ old('schedule', $event ? json_encode($event->schedule, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    <small style="color:#64748b;">Ej: [{"time":"16:00","activity":"Apertura de puertas"},{"time":"19:00","activity":"Partido oficial"}]</small>
</label>

<label style="display:flex;gap:0.5rem;align-items:center;">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $event?->is_active ?? false))>
    <span>Evento activo (visible en el sitio)</span>
</label>
