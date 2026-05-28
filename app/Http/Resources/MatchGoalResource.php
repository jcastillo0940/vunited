<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Sports\Models\MatchGoal */
class MatchGoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'club'         => new ClubResource($this->whenLoaded('club')),
            'scorer_name'  => $this->display_name,
            'minute'       => $this->minute,
            'is_own_goal'  => $this->is_own_goal,
            'is_penalty'   => $this->is_penalty,
        ];
    }
}
