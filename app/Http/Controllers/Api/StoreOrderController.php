<?php

namespace App\Http\Controllers\Api;

use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Domain\Store\Models\StoreOrder;
use App\Domain\Store\Services\StoreOrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreStoreOrderRequest;
use App\Http\Resources\StoreOrderResource;
use Illuminate\Http\JsonResponse;

class StoreOrderController extends Controller
{
    public function __construct(
        private readonly StoreOrderService $service,
    ) {}

    public function store(StoreStoreOrderRequest $request): JsonResponse
    {
        try {
            $result = $this->service->createOrder($request->validated());
        } catch (PaymentProviderException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'order_number' => $result['order']->order_number,
            'status' => $result['order']->status->value,
            'approve_url' => $result['approve_url'],
            'payment_id' => $result['payment']->id,
            'total' => $result['order']->total,
            'currency' => $result['order']->currency,
        ], 201);
    }

    public function show(string $orderNumber): JsonResponse
    {
        $order = StoreOrder::query()
            ->with(['items', 'payment'])
            ->where('order_number', $orderNumber)
            ->first();

        if ($order === null) {
            return response()->json([
                'error' => 'Orden de tienda no encontrada.',
            ], 404);
        }

        return (new StoreOrderResource($order))->response();
    }
}
