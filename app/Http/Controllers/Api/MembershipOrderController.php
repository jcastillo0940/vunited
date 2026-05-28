<?php

namespace App\Http\Controllers\Api;

use App\Domain\Memberships\Models\MembershipOrder;
use App\Domain\Memberships\Services\MembershipOrderService;
use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMembershipOrderRequest;
use App\Http\Resources\MembershipOrderResource;
use Illuminate\Http\JsonResponse;

class MembershipOrderController extends Controller
{
    public function __construct(
        private readonly MembershipOrderService $service,
    ) {}

    public function store(StoreMembershipOrderRequest $request): JsonResponse
    {
        try {
            $result = $this->service->createOrder($request->validated());

            return response()->json([
                'order_number' => $result['order']->order_number,
                'status'       => $result['order']->status->value,
                'approve_url'  => $result['approve_url'],
                'payment_id'   => $result['payment']->id,
            ], 201);
        } catch (PaymentProviderException $e) {
            return response()->json([
                'error'   => 'No se pudo crear la orden de pago.',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $orderNumber): JsonResponse
    {
        $order = MembershipOrder::query()
            ->where('order_number', $orderNumber)
            ->with('payment')
            ->first();

        if ($order === null) {
            return response()->json(['error' => 'Orden no encontrada.'], 404);
        }

        return (new MembershipOrderResource($order))->response();
    }
}
