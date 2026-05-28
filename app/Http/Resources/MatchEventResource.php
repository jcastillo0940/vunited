<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Ticketing\Models\MatchEvent */
class MatchEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code'             => $this->code,
            'home_team'        => $this->home_team,
            'away_team'        => $this->away_team,
            'home_club'        => new ClubResource($this->whenLoaded('homeClub')),
            'away_club'        => new ClubResource($this->whenLoaded('awayClub')),
            'competition'      => $this->competition,
            'round_label'      => $this->round_label,
            'match_date'       => $this->match_date?->toIso8601String(),
            'date_label'       => $this->match_date?->timezone(config('app.timezone'))->translatedFormat('d \d\e F, Y'),
            'time_label'       => $this->match_date?->timezone(config('app.timezone'))->format('H:i'),
            'stadium_name'     => $this->stadium_name,
            'stadium_location' => $this->stadium_location,
            'status'           => $this->status,
            'home_score'       => $this->home_score,
            'away_score'       => $this->away_score,
            'is_featured'      => $this->is_featured,
            'metadata'         => $this->metadata,
            'goals'            => MatchGoalResource::collection($this->whenLoaded('goals')),
            'zones'            => TicketZoneResource::collection($this->whenLoaded('ticketZones')),
        ];
    }
}
