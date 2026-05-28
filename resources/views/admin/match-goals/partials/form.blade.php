<div style="display:grid;gap:1rem;grid-template-columns:repeat(2,minmax(0,1fr));">

    <label style="display:grid;gap:0.3rem;">
        <span>Club *</span>
        <select name="club_id" class="admin-button" style="width:100%;" required>
            <option value="">— Seleccionar —</option>
            @foreach($clubs as $club)
                <option value="{{ $club->id }}" @selected(old('club_id', $goal?->club_id) == $club->id)>{{ $club->name }}</option>
            @endforeach
        </select>
        @error('club_id')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Minuto</span>
        <input type="number" name="minute" value="{{ old('minute', $goal?->minute) }}" min="1" max="120" class="admin-button" style="width:100%;" placeholder="45">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Jugador (de la plantilla)</span>
        <select name="player_id" class="admin-button" style="width:100%;">
            <option value="">— Sin jugador vinculado —</option>
            @foreach($players as $player)
                <option value="{{ $player->id }}" @selected(old('player_id', $goal?->player_id) == $player->id)>{{ $player->name }}</option>
            @endforeach
        </select>
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Nombre goleador (texto libre)</span>
        <input type="text" name="scorer_name" value="{{ old('scorer_name', $goal?->scorer_name) }}" class="admin-button" style="width:100%;" placeholder="Si no está en la plantilla">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Orden visual</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $goal?->sort_order ?? 0) }}" min="0" class="admin-button" style="width:100%;">
    </label>

    <div style="display:flex;flex-direction:column;gap:0.5rem;justify-content:flex-end;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_own_goal" value="0">
            <input type="checkbox" name="is_own_goal" value="1" @checked(old('is_own_goal', $goal?->is_own_goal))>
            <span>En propia puerta</span>
        </label>
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_penalty" value="0">
            <input type="checkbox" name="is_penalty" value="1" @checked(old('is_penalty', $goal?->is_penalty))>
            <span>Penalti</span>
        </label>
    </div>

</div>
