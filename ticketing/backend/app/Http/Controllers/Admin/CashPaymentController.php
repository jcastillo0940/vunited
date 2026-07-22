<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Services\OrderService;
use App\Jobs\IssueTicketsForOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CashPaymentController
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->where('payment_method', 'cash')
            ->where('status', 'pending_payment')
            ->with('event:id,home_team,away_team')
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->public_id,
                'order_number' => $order->order_number,
                'customer_email' => $order->customer_email,
                'total' => (float) $order->total,
                'event' => $order->event ? "{$order->event->home_team} vs {$order->event->away_team}" : null,
                'created_at' => $order->created_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $orders]);
    }

    public function confirm(Request $r, string $publicId): JsonResponse
    {
        $order = Order::query()->where('public_id', $publicId)->where('payment_method', 'cash')->firstOrFail();
        abort_unless($order->status === 'pending_payment', 409, 'Orden no esta pendiente de pago.');

        $response = Http::withHeaders([
            'X-Service-Token' => config('services.payments.service_token'),
            'X-Service-Audience' => 'ticketing',
            'X-Service-Scopes' => 'payments.write',
        ])->withoutVerifying()->post(rtrim(config('services.payments.base_url'), '/').'/internal/v1/payment-intents/'.$order->payment_intent_id.'/confirm-cash', [
            'confirmed_by' => $r->user()->email,
        ]);

        abort_unless($response->successful(), 502, 'Payments no confirmo el pago.');

        $order->update(['cash_confirmed_by' => $r->user()->email, 'cash_confirmed_at' => now()]);
        $order = $this->orders->markPaid($order);
        IssueTicketsForOrder::dispatch($order->id);

        return response()->json(['order_status' => $order->status]);
    }
}
