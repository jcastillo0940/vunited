<?php

namespace App\Domain\Sports\Models;

use Database\Factories\LeagueStandingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'club_id',
    'competition',
    'season',
    'position',
    'played',
    'won',
    'drawn',
    'lost',
    'goals_for',
    'goals_against',
    'goal_difference',
    'points',
    'is_active',
])]
class LeagueStanding extends Model
{
    /** @use HasFactory<LeagueStandingFactory> */
    use HasFactory;

    protected static function newFactory(): LeagueStandingFactory
    {
        return LeagueStandingFactory::new();
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'played' => 'integer',
            'won' => 'integer',
            'drawn' => 'integer',
            'lost' => 'integer',
            'goals_for' => 'integer',
            'goals_against' => 'integer',
            'goal_difference' => 'integer',
            'points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
