<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\Ticket;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;

class TicketController
{
    public function show(string $publicId): TicketResource|JsonResponse
    {
        $ticket = Ticket::query()->where('public_id', $publicId)->with(['zone', 'seat'])->first();

        if (! $ticket) {
            return response()->json(['message' => 'Boleto no encontrado.'], 404);
        }

        return new TicketResource($ticket);
    }
}
