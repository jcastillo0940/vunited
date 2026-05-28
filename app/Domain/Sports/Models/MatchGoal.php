<?php

namespace App\Domain\Sports\Models;

use App\Domain\Squad\Models\Player;
use App\Domain\Ticketing\Models\MatchEvent;
use Database\Factories\MatchGoalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'match_event_id',
    'club_id',
    'player_id',
    'scorer_name',
    'minute',
    'is_own_goal',
    'is_penalty',
    'sort_order',
])]
class MatchGoal extends Model
{
    /** @use HasFactory<MatchGoalFactory> */
    use HasFactory;

    protected static function newFactory(): MatchGoalFactory
    {
        return MatchGoalFactory::new();
    }

    protected function casts(): array
    {
        return [
            'minute' => 'integer',
            'is_own_goal' => 'boolean',
            'is_penalty' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function matchEvent(): BelongsTo
    {
        return $this->belongsTo(MatchEvent::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->player?->name ?? $this->scorer_name ?? 'Desconocido';
    }
}
