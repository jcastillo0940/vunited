<?php

namespace App\Http\Controllers\Api;

use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Domain\Ticketing\Models\TicketOrder;
use App\Domain\Ticketing\Services\TicketOrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTicketOrderRequest;
use App\Http\Resources\TicketOrderResource;
use Illuminate\Http\JsonResponse;

class TicketOrderController extends Controller
{
    public function __construct(
        private readonly TicketOrderService $service,
    ) {}

    public function store(StoreTicketOrderRequest $request): JsonResponse
    {
        try {
            $result = $this->service->createOrder($request->validated());

            return response()->json([
                'order_number' => $result['order']->order_number,
                'status'       => $result['order']->status->value,
                'approve_url'  => $result['approve_url'],
                'payment_id'   => $result['payment']->id,
                'total'        => $result['order']->total,
                'currency'     => $result['order']->currency,
            ], 201);
        } catch (PaymentProviderException $e) {
            return response()->json([
                'error'   => 'No se pudo crear la orden de boletos.',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $orderNumber): JsonResponse
    {
        $order = TicketOrder::query()
            ->where('order_number', $orderNumber)
            ->with(['matchEvent', 'items', 'payment'])
            ->first();

        if ($order === null) {
            return response()->json(['error' => 'Orden no encontrada.'], 404);
        }

        return (new TicketOrderResource($order))->response();
    }
}
