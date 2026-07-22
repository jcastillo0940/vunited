<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Exceptions\InsufficientCapacityException;
use App\Domain\Ticketing\Exceptions\OrderException;
use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Ticketing\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
                $request->user()->id,
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

    public function show(Request $request, string $publicId): OrderResource|JsonResponse
    {
        $order = Order::query()->where('public_id', $publicId)->with(['items.zone', 'items.seat'])->first();

        if (! $order) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        if ($order->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Esta orden no te pertenece.'], 403);
        }

        return new OrderResource($order);
    }

    public function requestPayment(Request $request, string $publicId): OrderResource|JsonResponse
    {
        $order = Order::query()->where('public_id', $publicId)->first();

        if (! $order) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        if ($order->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Esta orden no te pertenece.'], 403);
        }

        $method = $request->validate(['payment_method' => 'nullable|in:tilopay,cash'])['payment_method'] ?? 'tilopay';

        try {
            $order = $this->orders->requestPayment($order, $method);
        } catch (OrderException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new OrderResource($order);
    }

    public function tickets(Request $request, string $publicId): AnonymousResourceCollection|JsonResponse
    {
        $order = Order::query()->where('public_id', $publicId)->first();

        if (! $order) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        if ($order->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Esta orden no te pertenece.'], 403);
        }

        return TicketResource::collection($order->tickets()->with(['zone', 'seat'])->get());
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        $orders = Order::query()
            ->where('customer_id', $request->user()->id)
            ->with(['event'])
            ->latest()
            ->limit(100)
            ->get();

        return OrderResource::collection($orders);
    }

    public function myTickets(Request $request): AnonymousResourceCollection
    {
        $tickets = Ticket::query()
            ->whereHas('order', fn ($q) => $q->where('customer_id', $request->user()->id))
            ->with(['zone', 'seat'])
            ->latest('issued_at')
            ->limit(200)
            ->get();

        return TicketResource::collection($tickets);
    }
}
