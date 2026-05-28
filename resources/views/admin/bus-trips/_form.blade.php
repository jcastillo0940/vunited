<div style="display:grid;gap:1rem;grid-template-columns:repeat(2,minmax(0,1fr));">

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Título *</span>
        <input type="text" name="title" value="{{ old('title', $trip?->title) }}" required class="admin-button" style="width:100%;">
        @error('title')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Partido vinculado (opcional)</span>
        <select name="match_event_id" class="admin-button" style="width:100%;">
            <option value="">Sin partido vinculado</option>
            @foreach ($matches as $match)
                <option value="{{ $match->id }}" @selected(old('match_event_id', $trip?->match_event_id) == $match->id)>
                    {{ $match->home_team }} vs {{ $match->away_team }} — {{ $match->match_date?->format('Y-m-d') }}
                </option>
            @endforeach
        </select>
    </label>

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Lugar de salida *</span>
        <input type="text" name="departure_location" value="{{ old('departure_location', $trip?->departure_location) }}" placeholder="Santiago de Veraguas, Terminal David" required class="admin-button" style="width:100%;">
        @error('departure_location')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Fecha y hora de salida *</span>
        <input type="datetime-local" name="departure_time" value="{{ old('departure_time', $trip?->departure_time?->format('Y-m-d\TH:i')) }}" required class="admin-button" style="width:100%;">
        @error('departure_time')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Hora de regreso estimada</span>
        <input type="datetime-local" name="return_time" value="{{ old('return_time', $trip?->return_time?->format('Y-m-d\TH:i')) }}" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Precio *</span>
        <input type="number" name="price" value="{{ old('price', $trip?->price ?? '10.00') }}" min="0" step="0.01" required class="admin-button" style="width:100%;">
        @error('price')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Moneda</span>
        <input type="text" name="currency" value="{{ old('currency', $trip?->currency ?? 'USD') }}" maxlength="3" class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Capacidad total *</span>
        <input type="number" name="capacity" value="{{ old('capacity', $trip?->capacity ?? 40) }}" min="1" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Cupos disponibles *</span>
        <input type="number" name="available_seats" value="{{ old('available_seats', $trip?->available_seats ?? 40) }}" min="0" required class="admin-button" style="width:100%;">
    </label>

    <div style="display:flex;gap:1rem;align-items:center;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $trip?->is_active ?? true))>
            <span>Viaje activo</span>
        </label>
    </div>

</div>
