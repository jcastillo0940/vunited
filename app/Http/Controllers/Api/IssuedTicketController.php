<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\TicketOrder;
use App\Domain\Ticketing\Services\TicketValidationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssuedTicketController extends Controller
{
    public function __construct(
        private readonly TicketValidationService $validationService,
    ) {}

    public function forOrder(string $orderNumber): JsonResponse
    {
        $order = TicketOrder::query()
            ->where('order_number', $orderNumber)
            ->with(['issuedTickets', 'matchEvent'])
            ->first();

        if ($order === null) {
            return response()->json(['error' => 'Orden no encontrada.'], 404);
        }

        return response()->json([
            'order_number' => $order->order_number,
            'status'       => $order->status->value,
            'match'        => $order->matchEvent ? [
                'home_team'  => $order->matchEvent->home_team,
                'away_team'  => $order->matchEvent->away_team,
                'match_date' => $order->matchEvent->match_date?->toISOString(),
                'stadium'    => $order->matchEvent->stadium_name,
            ] : null,
            'tickets'      => $order->issuedTickets->map(fn ($t) => [
                'id'         => $t->id,
                'token'      => $t->token,
                'qr_payload' => $t->qr_payload,
                'zone_name'  => $t->zone_name,
                'seat_label' => $t->seat_label,
                'status'     => $t->status->value,
                'issued_at'  => $t->issued_at?->toISOString(),
            ]),
        ]);
    }

    public function validate(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string', 'size:40']]);

        $result = $this->validationService->validate($request->string('token')->toString());

        $statusCode = $result['valid'] ? 200 : 422;

        return response()->json($result, $statusCode);
    }
}
