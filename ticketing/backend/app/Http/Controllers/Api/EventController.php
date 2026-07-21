<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Resources\ZoneResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController
{
    public function index(): AnonymousResourceCollection
    {
        $events = Event::query()
            ->where('is_visible', true)
            ->orderBy('starts_at')
            ->get();

        return EventResource::collection($events);
    }

    public function show(string $publicId): EventResource|JsonResponse
    {
        $event = Event::query()->where('public_id', $publicId)->where('is_visible', true)->first();

        if (! $event) {
            return response()->json(['message' => 'Evento no encontrado.'], 404);
        }

        return new EventResource($event);
    }

    public function zones(string $publicId): AnonymousResourceCollection|JsonResponse
    {
        $event = Event::query()->where('public_id', $publicId)->where('is_visible', true)->first();

        if (! $event) {
            return response()->json(['message' => 'Evento no encontrado.'], 404);
        }

        $zones = $event->zones()->where('is_active', true)->orderBy('sort_order')->get();

        return ZoneResource::collection($zones);
    }
}
