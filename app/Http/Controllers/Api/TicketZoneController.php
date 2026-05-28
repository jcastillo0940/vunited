<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\MatchEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketZoneResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketZoneController extends Controller
{
    public function index(string $code): JsonResponse|AnonymousResourceCollection
    {
        $matchEvent = MatchEvent::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if ($matchEvent === null) {
            return response()->json([
                'error' => 'Partido no encontrado.',
            ], 404);
        }

        return TicketZoneResource::collection(
            $matchEvent->ticketZones()->where('is_active', true)->get(),
        );
    }
}
