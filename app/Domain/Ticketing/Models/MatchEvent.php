<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Sports\Models\Club;
use App\Domain\Sports\Models\MatchGoal;
use Database\Factories\MatchEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'home_team',
    'away_team',
    'home_club_id',
    'away_club_id',
    'competition',
    'round_label',
    'match_date',
    'stadium_name',
    'stadium_location',
    'status',
    'home_score',
    'away_score',
    'is_featured',
    'is_active',
    'metadata',
])]
class MatchEvent extends Model
{
    /** @use HasFactory<MatchEventFactory> */
    use HasFactory;

    protected static function newFactory(): MatchEventFactory
    {
        return MatchEventFactory::new();
    }

    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
            'home_score' => 'integer',
            'away_score' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function ticketZones(): HasMany
    {
        return $this->hasMany(TicketZone::class)->orderBy('sort_order')->orderBy('name');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(MatchGoal::class)->orderBy('sort_order')->orderBy('minute');
    }

    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }
}
