<?php

namespace App\Http\Controllers\Internal;

use App\Domain\Ticketing\Exceptions\OrderException;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Services\OrderService;
use App\Jobs\IssueTicketsForOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint interno que Payments llama cuando confirma o rechaza un pago
 * (saga descrita en docs/architecture/target-monorepo.md "Comunicacion").
 * Nunca lo llama el navegador: protegido por VerifyInternalSecret.
 */
class PaymentEventController
{
    public function __construct(private readonly OrderService $orders) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_public_id' => ['required', 'string'],
            'event' => ['required', 'in:payment_confirmed,payment_failed'],
            'payment_intent_id' => ['nullable', 'string'],
        ]);

        $order = Order::query()->where('public_id', $validated['order_public_id'])->first();

        if (! $order) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        try {
            if ($validated['event'] === 'payment_confirmed') {
                $order = $this->orders->markPaid($order);
                IssueTicketsForOrder::dispatch($order->id);
            } else {
                $order = $this->orders->markFailed($order, 'payment_failed_webhook');
            }
        } catch (OrderException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['order_status' => $order->status]);
    }
}
