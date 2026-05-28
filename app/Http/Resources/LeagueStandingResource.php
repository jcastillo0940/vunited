<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Sports\Models\LeagueStanding */
class LeagueStandingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'position'        => $this->position,
            'club'            => new ClubResource($this->whenLoaded('club')),
            'competition'     => $this->competition,
            'season'          => $this->season,
            'played'          => $this->played,
            'won'             => $this->won,
            'drawn'           => $this->drawn,
            'lost'            => $this->lost,
            'goals_for'       => $this->goals_for,
            'goals_against'   => $this->goals_against,
            'goal_difference' => $this->goal_difference,
            'points'          => $this->points,
        ];
    }
}
