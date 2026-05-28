<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\MatchEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\MatchEventResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MatchEventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MatchEvent::query()->where('is_active', true);

        if ($status = trim($request->string('status')->toString())) {
            $query->where('status', $status);
        }

        return MatchEventResource::collection(
            $query->orderBy('match_date')->get(),
        );
    }

    public function featured(): JsonResponse
    {
        $matchEvent = MatchEvent::query()
            ->with(['ticketZones' => fn ($builder) => $builder->where('is_active', true)])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where('match_date', '>=', now())
            ->orderBy('match_date')
            ->first();

        if ($matchEvent === null) {
            return response()->json([
                'error' => 'No hay un partido destacado disponible.',
            ], 404);
        }

        return (new MatchEventResource($matchEvent))->response();
    }

    public function show(string $code): JsonResponse
    {
        $matchEvent = MatchEvent::query()
            ->with(['ticketZones' => fn ($builder) => $builder->where('is_active', true)])
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if ($matchEvent === null) {
            return response()->json([
                'error' => 'Partido no encontrado.',
            ], 404);
        }

        return (new MatchEventResource($matchEvent))->response();
    }
}
