<?php

namespace App\Domain\Sports\Models;

use Database\Factories\ClubFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'short_name',
    'slug',
    'logo_path',
    'city',
    'primary_color',
    'secondary_color',
    'is_active',
    'sort_order',
    'metadata',
])]
class Club extends Model
{
    /** @use HasFactory<ClubFactory> */
    use HasFactory;

    protected static function newFactory(): ClubFactory
    {
        return ClubFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(\App\Domain\Ticketing\Models\MatchEvent::class, 'home_club_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(\App\Domain\Ticketing\Models\MatchEvent::class, 'away_club_id');
    }

    public function standings(): HasMany
    {
        return $this->hasMany(LeagueStanding::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(MatchGoal::class);
    }
}
