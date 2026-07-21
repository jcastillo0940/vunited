<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Exceptions\InsufficientCapacityException;
use App\Domain\Ticketing\Exceptions\OrderException;
use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController
{
    public function __construct(private readonly OrderService $orders) {}

    public function store(StoreOrderRequest $request, string $eventPublicId): JsonResponse
    {
        $event = Event::query()->where('public_id', $eventPublicId)->first();

        if (! $event) {
            return response()->json(['message' => 'Evento no encontrado.'], 404);
        }

        $items = array_map(fn (array $item) => [
            'zone_public_id' => $item['zone_id'],
            'quantity' => $item['quantity'] ?? count($item['seat_ids'] ?? []),
            'seat_public_ids' => $item['seat_ids'] ?? [],
        ], $request->validated('items'));

        try {
            $order = $this->orders->createOrder(
                $event,
                $items,
                $request->validated('customer_email'),
                $request->validated('customer_name'),
                $request->validated('customer_phone'),
                $request->validated('idempotency_key'),
            );
        } catch (InsufficientCapacityException|OrderException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return (new OrderResource($order->load(['items.zone', 'items.seat'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $publicId): OrderResource|JsonResponse
    {
        $order = Order::query()->where('public_id', $publicId)->with(['items.zone', 'items.seat'])->first();

        if (! $order) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        return new OrderResource($order);
    }

    public function requestPayment(string $publicId): OrderResource|JsonResponse
    {
        $order = Order::query()->where('public_id', $publicId)->first();

        if (! $order) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        try {
            $order = $this->orders->requestPayment($order);
        } catch (OrderException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new OrderResource($order);
    }

    public function tickets(string $publicId): AnonymousResourceCollection|JsonResponse
    {
        $order = Order::query()->where('public_id', $publicId)->first();

        if (! $order) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        return TicketResource::collection($order->tickets()->with(['zone', 'seat'])->get());
    }
}
