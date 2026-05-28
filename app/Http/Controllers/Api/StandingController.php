<?php

namespace App\Http\Controllers\Api;

use App\Domain\Sports\Models\LeagueStanding;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeagueStandingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StandingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $season      = $request->string('season', date('Y'))->toString();
        $competition = $request->string('competition', 'LPF')->toString();

        return LeagueStandingResource::collection(
            LeagueStanding::query()
                ->with('club')
                ->where('is_active', true)
                ->where('season', $season)
                ->where('competition', $competition)
                ->orderBy('position')
                ->get(),
        );
    }
}
