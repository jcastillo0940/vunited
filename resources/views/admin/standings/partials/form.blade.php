<div style="display:grid;gap:1rem;grid-template-columns:repeat(3,minmax(0,1fr));">

    <label style="grid-column:1/-1;display:grid;gap:0.3rem;">
        <span>Club *</span>
        <select name="club_id" class="admin-button" style="width:100%;" required>
            <option value="">— Seleccionar —</option>
            @foreach($clubs as $club)
                <option value="{{ $club->id }}" @selected(old('club_id', $standing?->club_id) == $club->id)>{{ $club->name }}</option>
            @endforeach
        </select>
        @error('club_id')<small style="color:#991b1b;">{{ $message }}</small>@enderror
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Competición *</span>
        <input type="text" name="competition" value="{{ old('competition', $standing?->competition ?? 'LPF') }}" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Temporada *</span>
        <input type="text" name="season" value="{{ old('season', $standing?->season ?? date('Y')) }}" required class="admin-button" style="width:100%;" placeholder="2026">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Posición *</span>
        <input type="number" name="position" value="{{ old('position', $standing?->position ?? 1) }}" min="1" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>PJ *</span>
        <input type="number" name="played" value="{{ old('played', $standing?->played ?? 0) }}" min="0" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Ganados *</span>
        <input type="number" name="won" value="{{ old('won', $standing?->won ?? 0) }}" min="0" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Empates *</span>
        <input type="number" name="drawn" value="{{ old('drawn', $standing?->drawn ?? 0) }}" min="0" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Perdidos *</span>
        <input type="number" name="lost" value="{{ old('lost', $standing?->lost ?? 0) }}" min="0" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Goles a favor *</span>
        <input type="number" name="goals_for" value="{{ old('goals_for', $standing?->goals_for ?? 0) }}" min="0" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Goles en contra *</span>
        <input type="number" name="goals_against" value="{{ old('goals_against', $standing?->goals_against ?? 0) }}" min="0" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Diferencia de goles *</span>
        <input type="number" name="goal_difference" value="{{ old('goal_difference', $standing?->goal_difference ?? 0) }}" required class="admin-button" style="width:100%;">
    </label>

    <label style="display:grid;gap:0.3rem;">
        <span>Puntos *</span>
        <input type="number" name="points" value="{{ old('points', $standing?->points ?? 0) }}" min="0" required class="admin-button" style="width:100%;">
    </label>

    <div style="display:flex;align-items:flex-end;">
        <label style="display:flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $standing?->is_active ?? true))>
            <span>Activo</span>
        </label>
    </div>

</div>
