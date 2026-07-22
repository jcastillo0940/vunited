<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CashPaymentController
{
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->where('payment_method', 'cash')
            ->where('status', 'pending_payment')
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->public_id,
                'email' => $order->email,
                'total' => (float) $order->total,
                'currency' => $order->currency,
                'created_at' => $order->created_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $orders]);
    }

    public function confirm(Request $r, string $publicId): JsonResponse
    {
        $order = Order::where('public_id', $publicId)->where('payment_method', 'cash')->firstOrFail();
        abort_unless($order->status === 'pending_payment', 409, 'Orden no esta pendiente de pago.');

        $response = Http::withHeaders([
            'X-Service-Token' => config('store.payments_token'),
            'X-Service-Audience' => 'store',
            'X-Service-Scopes' => 'payments.write',
        ])->withoutVerifying()->post(rtrim(config('store.payments_url'), '/').'/internal/v1/payment-intents/'.$order->payment_public_id.'/confirm-cash', [
            'confirmed_by' => $r->user()->email,
        ]);

        abort_unless($response->successful(), 502, 'Payments no confirmo el pago.');

        $order->update([
            'status' => 'paid',
            'cash_confirmed_by' => $r->user()->email,
            'cash_confirmed_at' => now(),
        ]);

        return response()->json(['order_status' => $order->status]);
    }
}
