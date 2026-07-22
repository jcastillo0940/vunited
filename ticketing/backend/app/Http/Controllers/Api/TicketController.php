<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\Ticket;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController
{
    public function show(Request $request, string $publicId): TicketResource|JsonResponse
    {
        $ticket = Ticket::query()->where('public_id', $publicId)->with(['zone', 'seat', 'order'])->first();

        if (! $ticket) {
            return response()->json(['message' => 'Boleto no encontrado.'], 404);
        }

        if ($ticket->order->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Este boleto no te pertenece.'], 403);
        }

        return new TicketResource($ticket);
    }
}
