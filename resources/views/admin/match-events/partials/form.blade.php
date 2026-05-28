<form method="POST" action="{{ $action }}" style="display:grid;gap:1rem;">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <label>
        <span>Code</span>
        <input type="text" name="code" value="{{ old('code', $matchEvent->code) }}" class="admin-input">
    </label>
    <label>
        <span>Home Team</span>
        <input type="text" name="home_team" value="{{ old('home_team', $matchEvent->home_team) }}" class="admin-input" required>
    </label>
    <label>
        <span>Away Team</span>
        <input type="text" name="away_team" value="{{ old('away_team', $matchEvent->away_team) }}" class="admin-input" required>
    </label>
    <label>
        <span>Competition</span>
        <input type="text" name="competition" value="{{ old('competition', $matchEvent->competition) }}" class="admin-input">
    </label>
    <label>
        <span>Round Label</span>
        <input type="text" name="round_label" value="{{ old('round_label', $matchEvent->round_label) }}" class="admin-input">
    </label>
    <label>
        <span>Match Date</span>
        <input type="datetime-local" name="match_date" value="{{ old('match_date', optional($matchEvent->match_date)->format('Y-m-d\TH:i')) }}" class="admin-input" required>
    </label>
    <label>
        <span>Stadium Name</span>
        <input type="text" name="stadium_name" value="{{ old('stadium_name', $matchEvent->stadium_name) }}" class="admin-input">
    </label>
    <label>
        <span>Stadium Location</span>
        <input type="text" name="stadium_location" value="{{ old('stadium_location', $matchEvent->stadium_location) }}" class="admin-input">
    </label>
    <label>
        <span>Status</span>
        <select name="status" class="admin-input">
            @foreach(['scheduled', 'live', 'finished', 'postponed', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(old('status', $matchEvent->status) === $status)>{{ strtoupper($status) }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span>Home Score</span>
        <input type="number" min="0" name="home_score" value="{{ old('home_score', $matchEvent->home_score) }}" class="admin-input">
    </label>
    <label>
        <span>Away Score</span>
        <input type="number" min="0" name="away_score" value="{{ old('away_score', $matchEvent->away_score) }}" class="admin-input">
    </label>
    <label>
        <span>Metadata (JSON)</span>
        <textarea name="metadata" rows="5" class="admin-input">{{ old('metadata', $matchEvent->metadata ? json_encode($matchEvent->metadata, JSON_PRETTY_PRINT) : '') }}</textarea>
    </label>
    <label style="display:flex;gap:0.5rem;align-items:center;">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $matchEvent->is_featured))>
        <span>Featured</span>
    </label>
    <label style="display:flex;gap:0.5rem;align-items:center;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $matchEvent->is_active ?? true))>
        <span>Active</span>
    </label>

    @if($errors->any())
        <div style="padding:0.75rem 1rem;border-radius:0.75rem;background:#fee2e2;color:#991b1b;">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button type="submit" class="admin-button" style="width:max-content;">Save</button>
</form>
