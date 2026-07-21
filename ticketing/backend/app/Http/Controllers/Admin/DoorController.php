<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\Door;
use App\Domain\Ticketing\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoorController
{
    public function index(string $eventPublicId): JsonResponse
    {
        $event = Event::query()->where('public_id', $eventPublicId)->firstOrFail();

        // Uso interno de operadores/backoffice: aqui SI se expone el id
        // interno (no public_id), porque /validate necesita el id numerico
        // de la puerta y estas rutas ya estan detras de auth:sanctum -
        // "puertas" no son un recurso enumerable-sensible como ordenes/tickets.
        return response()->json(['data' => $event->doors()->get(['id', 'name', 'is_active'])]);
    }

    public function store(Request $request, string $eventPublicId): JsonResponse
    {
        $event = Event::query()->where('public_id', $eventPublicId)->firstOrFail();
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);

        $door = Door::create(['event_id' => $event->id, 'name' => $data['name']]);

        return response()->json(['id' => $door->public_id, 'name' => $door->name], 201);
    }
}
