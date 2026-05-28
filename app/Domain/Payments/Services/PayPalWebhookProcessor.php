<?php

namespace App\Domain\Payments\Services;

use App\Domain\Memberships\Enums\MembershipOrderStatus;
use App\Domain\Memberships\Models\MembershipOrder;
use App\Domain\Payments\Data\PaymentProviderResult;
use App\Domain\Payments\Enums\PaymentEventProcessingStatus;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentEvent;
use App\Domain\Store\Enums\StoreOrderStatus;
use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\StoreOrder;
use App\Domain\Ticketing\Enums\TicketOrderStatus;
use App\Domain\Ticketing\Models\TicketOrder;
use App\Domain\Ticketing\Services\TicketIssuingService;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class PayPalWebhookProcessor
{
    public function __construct(
        private readonly PaymentLifecycleService $lifecycle,
        private readonly TicketIssuingService $ticketIssuingService,
    ) {}

    public function process(PaymentEvent $event): void
    {
        if ($event->processing_status === PaymentEventProcessingStatus::Processed) {
            return;
        }

        $payment = $this->findPayment($event);

        if ($payment === null) {
            $event->update([
                'processing_status' => PaymentEventProcessingStatus::Ignored,
                'error_message'     => 'No matching Payment found for this event.',
            ]);

            return;
        }

        match ($event->event_type) {
            'CHECKOUT.ORDER.APPROVED'    => $this->handleApproved($event, $payment),
            'PAYMENT.CAPTURE.COMPLETED'  => $this->handleCaptured($event, $payment),
            'PAYMENT.CAPTURE.DENIED'     => $this->handleDenied($event, $payment),
            'PAYMENT.CAPTURE.REFUNDED'   => $this->handleRefunded($event, $payment),
            default                      => $this->handleUnknown($event),
        };
    }

    private function findPayment(PaymentEvent $event): ?Payment
    {
        if (! empty($event->provider_capture_id)) {
            $payment = Payment::query()
                ->where('provider_capture_id', $event->provider_capture_id)
                ->first();

            if ($payment !== null) {
                return $payment;
            }
        }

        if (! empty($event->provider_order_id)) {
            return Payment::query()
                ->where('provider_order_id', $event->provider_order_id)
                ->first();
        }

        return null;
    }

    private function handleApproved(PaymentEvent $event, Payment $payment): void
    {
        $this->lifecycle->markApproved($payment, $event->payload ?? []);

        $event->update([
            'payment_id'        => $payment->id,
            'processing_status' => PaymentEventProcessingStatus::Processed,
            'processed_at'      => now(),
        ]);
    }

    private function handleCaptured(PaymentEvent $event, Payment $payment): void
    {
        try {
            $result = PaymentProviderResult::success(
                providerOrderId: $event->provider_order_id,
                providerCaptureId: $event->provider_capture_id,
                rawPayload: $event->payload ?? [],
            );

            $this->lifecycle->markCaptured($payment, $result);
            $this->syncPayable($payment->refresh(), PaymentStatus::Captured);

            $event->update([
                'payment_id'        => $payment->id,
                'processing_status' => PaymentEventProcessingStatus::Processed,
                'processed_at'      => now(),
            ]);
        } catch (InvalidArgumentException $e) {
            $event->update([
                'payment_id'        => $payment->id,
                'processing_status' => PaymentEventProcessingStatus::Failed,
                'error_message'     => $e->getMessage(),
            ]);
        }
    }

    private function handleDenied(PaymentEvent $event, Payment $payment): void
    {
        $this->lifecycle->markFailed(
            $payment,
            'Capture denied by PayPal.',
            $event->payload ?? [],
        );

        $this->syncPayable($payment->refresh(), PaymentStatus::Failed);

        $event->update([
            'payment_id'        => $payment->id,
            'processing_status' => PaymentEventProcessingStatus::Processed,
            'processed_at'      => now(),
        ]);
    }

    private function handleRefunded(PaymentEvent $event, Payment $payment): void
    {
        $this->lifecycle->markRefunded($payment, $event->payload ?? []);
        $this->syncPayable($payment->refresh(), PaymentStatus::Refunded);

        $event->update([
            'payment_id'        => $payment->id,
            'processing_status' => PaymentEventProcessingStatus::Processed,
            'processed_at'      => now(),
        ]);
    }

    private function handleUnknown(PaymentEvent $event): void
    {
        $event->update([
            'processing_status' => PaymentEventProcessingStatus::Ignored,
            'error_message'     => "Unrecognized event type: {$event->event_type}",
        ]);
    }

    private function syncPayable(Payment $payment, PaymentStatus $status): void
    {
        if (empty($payment->payable_type) || empty($payment->payable_id)) {
            return;
        }

        $payable = $payment->payable;

        if ($payable instanceof MembershipOrder) {
            $this->syncMembershipOrder($payable, $status);
            return;
        }

        if ($payable instanceof StoreOrder) {
            $this->syncStoreOrder($payable, $status);
            return;
        }

        if ($payable instanceof TicketOrder) {
            $this->syncTicketOrder($payable, $status);
        }
    }

    private function syncMembershipOrder(MembershipOrder $order, PaymentStatus $status): void
    {
        match ($status) {
            PaymentStatus::Captured => $order->update([
                'status'     => MembershipOrderStatus::Paid,
                'paid_at'    => now(),
                'starts_at'  => now(),
                'expires_at' => now()->addYear(),
            ]),
            PaymentStatus::Failed => $order->update([
                'status' => MembershipOrderStatus::Failed,
            ]),
            PaymentStatus::Cancelled,
            PaymentStatus::Refunded => $order->update([
                'status'       => MembershipOrderStatus::Cancelled,
                'cancelled_at' => now(),
            ]),
            default => null,
        };
    }

    private function syncStoreOrder(StoreOrder $order, PaymentStatus $status): void
    {
        match ($status) {
            PaymentStatus::Captured => $this->markStoreOrderPaid($order),
            PaymentStatus::Failed => $order->update([
                'status' => StoreOrderStatus::Failed,
            ]),
            PaymentStatus::Cancelled,
            PaymentStatus::Refunded => $order->update([
                'status' => StoreOrderStatus::Cancelled,
                'cancelled_at' => now(),
            ]),
            default => null,
        };
    }

    private function markStoreOrderPaid(StoreOrder $order): void
    {
        if ($order->status === StoreOrderStatus::Paid || $order->paid_at !== null) {
            return;
        }

        DB::transaction(function () use ($order): void {
            $order->loadMissing('items.product');

            foreach ($order->items as $item) {
                /** @var Product|null $product */
                $product = $item->product;

                if (! $product || ! $product->track_stock) {
                    continue;
                }

                $product->decrement('stock_quantity', $item->quantity);
            }

            $order->update([
                'status' => StoreOrderStatus::Paid,
                'paid_at' => now(),
            ]);
        });
    }

    private function syncTicketOrder(TicketOrder $order, PaymentStatus $status): void
    {
        match ($status) {
            PaymentStatus::Captured  => $this->markTicketOrderPaid($order),
            PaymentStatus::Failed    => $order->update(['status' => TicketOrderStatus::Failed]),
            PaymentStatus::Cancelled,
            PaymentStatus::Refunded  => $order->update([
                'status'       => TicketOrderStatus::Cancelled,
                'cancelled_at' => now(),
            ]),
            default => null,
        };
    }

    private function markTicketOrderPaid(TicketOrder $order): void
    {
        if ($order->status === TicketOrderStatus::Paid || $order->paid_at !== null) {
            return;
        }

        DB::transaction(function () use ($order): void {
            $order->loadMissing('items.ticketZone');

            foreach ($order->items as $item) {
                $zone = $item->ticketZone;

                if ($zone && $zone->available_quantity !== null) {
                    $zone->decrement('available_quantity', $item->quantity);
                }
            }

            $order->update([
                'status'  => TicketOrderStatus::Paid,
                'paid_at' => now(),
            ]);
        });

        $this->ticketIssuingService->issueForOrder($order->refresh());
    }
}
