<?php

namespace App\Domain\Ticketing\Services;

use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Domain\Payments\Providers\PayPalPaymentProvider;
use App\Domain\Payments\Services\PaymentLifecycleService;
use App\Domain\Ticketing\Enums\TicketOrderStatus;
use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketOrder;
use App\Domain\Ticketing\Models\TicketOrderItem;
use App\Domain\Ticketing\Models\TicketZone;

class TicketOrderService
{
    public function __construct(
        private readonly PaymentLifecycleService $lifecycle,
        private readonly PayPalPaymentProvider $provider,
    ) {}

    public function createOrder(array $data): array
    {
        $match = MatchEvent::query()->where('code', $data['match_event_code'])->first();

        if (! $match || ! $match->is_active) {
            throw new PaymentProviderException('El partido no está disponible para compra de boletos.');
        }

        $zone = TicketZone::query()->find($data['ticket_zone_id']);

        if (! $zone || ! $zone->is_active || $zone->match_event_id !== $match->id) {
            throw new PaymentProviderException('La zona seleccionada no está disponible.');
        }

        $quantity = (int) $data['quantity'];

        if ($zone->available_quantity !== null && $zone->available_quantity < $quantity) {
            throw new PaymentProviderException(
                "Disponibilidad insuficiente. Solo quedan {$zone->available_quantity} boletos en esta zona.",
            );
        }

        $unitPrice  = (float) $zone->price;
        $lineTotal  = round($unitPrice * $quantity, 2);
        $subtotal   = $lineTotal;
        $total      = $subtotal;
        $orderNumber = $this->generateOrderNumber();

        $order = TicketOrder::create([
            'order_number'   => $orderNumber,
            'match_event_id' => $match->id,
            'status'         => TicketOrderStatus::PendingPayment,
            'customer_name'  => $data['customer_name'] ?? null,
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'subtotal'       => number_format($subtotal, 2, '.', ''),
            'discount_total' => '0.00',
            'tax_total'      => '0.00',
            'total'          => number_format($total, 2, '.', ''),
            'currency'       => $zone->currency ?? 'USD',
            'metadata'       => [
                'match_code'       => $match->code,
                'zone_snapshot'    => [
                    'id'       => $zone->id,
                    'name'     => $zone->name,
                    'slug'     => $zone->slug,
                    'price'    => $zone->price,
                    'currency' => $zone->currency,
                ],
            ],
        ]);

        TicketOrderItem::create([
            'ticket_order_id' => $order->id,
            'ticket_zone_id'  => $zone->id,
            'zone_name'       => $zone->name,
            'unit_price'      => number_format($unitPrice, 2, '.', ''),
            'quantity'        => $quantity,
            'line_total'      => number_format($lineTotal, 2, '.', ''),
            'metadata'        => [
                'zone_id'        => $zone->id,
                'zone_slug'      => $zone->slug,
                'match_event_id' => $match->id,
                'match_code'     => $match->code,
            ],
        ]);

        $returnUrl = url('/orden-boletos-confirmada') . '?order=' . $orderNumber;

        $payment = $this->lifecycle->createPendingPayment([
            'payable_type'   => TicketOrder::class,
            'payable_id'     => $order->id,
            'provider'       => 'paypal',
            'currency'       => $order->currency,
            'amount'         => $order->total,
            'description'    => "Boletos Veraguas United – {$match->home_team} vs {$match->away_team} – {$orderNumber}",
            'customer_email' => $order->customer_email,
            'customer_name'  => $order->customer_name,
            'metadata'       => [
                'paypal_return_url' => $returnUrl,
                'paypal_cancel_url' => url('/boletos') . '?cancelled=1&order=' . $orderNumber,
                'ticket_order_number' => $orderNumber,
            ],
        ]);

        $result = $this->provider->createOrder($payment);

        if (! $result->success) {
            $this->lifecycle->markFailed($payment, $result->message ?? 'PayPal order creation failed.');
            $order->update(['status' => TicketOrderStatus::Failed]);

            throw new PaymentProviderException(
                $result->message ?? 'No se pudo crear la orden de boletos en PayPal.',
            );
        }

        $this->lifecycle->markProviderCreated($payment, $result);

        return [
            'order'       => $order->refresh()->load(['items', 'matchEvent']),
            'payment'     => $payment->refresh(),
            'approve_url' => $result->redirectUrl,
        ];
    }

    private function generateOrderNumber(): string
    {
        $year  = now()->year;
        $count = TicketOrder::query()->whereYear('created_at', $year)->count() + 1;

        return sprintf('TICKET-%d-%04d', $year, $count);
    }
}
