<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\MatchEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\MatchEventResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MatchController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MatchEvent::query()
            ->with(['homeClub', 'awayClub', 'goals.club'])
            ->where('is_active', true);

        if ($status = trim($request->string('status')->toString())) {
            $query->where('status', $status);
        }

        if ($season = trim($request->string('season')->toString())) {
            $query->whereYear('match_date', $season);
        }

        return MatchEventResource::collection(
            $query->orderBy('match_date')->get(),
        );
    }

    public function featured(): JsonResponse
    {
        $match = MatchEvent::query()
            ->with(['homeClub', 'awayClub', 'goals.club', 'ticketZones' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where('match_date', '>=', now())
            ->orderBy('match_date')
            ->first();

        if ($match === null) {
            return response()->json(['error' => 'No hay partido destacado.'], 404);
        }

        return (new MatchEventResource($match))->response();
    }

    public function show(string $code): JsonResponse
    {
        $match = MatchEvent::query()
            ->with(['homeClub', 'awayClub', 'goals.club', 'ticketZones' => fn ($q) => $q->where('is_active', true)])
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if ($match === null) {
            return response()->json(['error' => 'Partido no encontrado.'], 404);
        }

        return (new MatchEventResource($match))->response();
    }
}
