<?php

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Data\PaymentProviderResult;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use InvalidArgumentException;

class PaymentLifecycleService
{
    public function createPendingPayment(array $data): Payment
    {
        $amount = $data['amount'] ?? 0;

        if ((float) $amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        return Payment::create([
            'payable_type'  => $data['payable_type'] ?? null,
            'payable_id'    => $data['payable_id'] ?? null,
            'provider'      => $data['provider'] ?? 'paypal',
            'currency'      => $data['currency'] ?? 'USD',
            'amount'        => $amount,
            'status'        => PaymentStatus::Pending,
            'description'   => $data['description'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'customer_name'  => $data['customer_name'] ?? null,
            'metadata'       => $data['metadata'] ?? null,
        ]);
    }

    public function markProviderCreated(Payment $payment, PaymentProviderResult $result): Payment
    {
        $updates = ['status' => PaymentStatus::ProviderCreated];

        if ($result->providerOrderId !== null) {
            $updates['provider_order_id'] = $result->providerOrderId;
        }

        if ($result->rawPayload) {
            $updates['provider_payload'] = $result->rawPayload;
        }

        $payment->update($updates);

        return $payment->refresh();
    }

    public function markApproved(Payment $payment, array $payload = []): Payment
    {
        $updates = [
            'status'      => PaymentStatus::Approved,
            'approved_at' => now(),
        ];

        if ($payload) {
            $updates['provider_payload'] = $payload;
        }

        $payment->update($updates);

        return $payment->refresh();
    }

    public function markCaptured(Payment $payment, PaymentProviderResult|array $result): Payment
    {
        if ((float) $payment->amount <= 0) {
            throw new InvalidArgumentException('Cannot capture a payment with amount <= 0.');
        }

        $updates = [
            'status'      => PaymentStatus::Captured,
            'captured_at' => now(),
        ];

        if ($result instanceof PaymentProviderResult) {
            if ($result->providerCaptureId !== null) {
                $updates['provider_capture_id'] = $result->providerCaptureId;
            }
            if ($result->rawPayload) {
                $updates['provider_payload'] = $result->rawPayload;
            }
        } elseif ($result) {
            $updates['provider_payload'] = $result;
        }

        $payment->update($updates);

        return $payment->refresh();
    }

    public function markFailed(Payment $payment, ?string $message = null, array $payload = []): Payment
    {
        $updates = [
            'status'    => PaymentStatus::Failed,
            'failed_at' => now(),
        ];

        if ($payload) {
            $updates['provider_payload'] = $payload;
        }

        if ($message !== null) {
            $current = $payment->metadata ?? [];
            $updates['metadata'] = array_merge($current, ['failure_reason' => $message]);
        }

        $payment->update($updates);

        return $payment->refresh();
    }

    public function markCancelled(Payment $payment, array $payload = []): Payment
    {
        $updates = [
            'status'       => PaymentStatus::Cancelled,
            'cancelled_at' => now(),
        ];

        if ($payload) {
            $updates['provider_payload'] = $payload;
        }

        $payment->update($updates);

        return $payment->refresh();
    }

    public function markRefunded(Payment $payment, array $payload = []): Payment
    {
        $updates = [
            'status'      => PaymentStatus::Refunded,
            'refunded_at' => now(),
        ];

        if ($payload) {
            $updates['provider_payload'] = $payload;
        }

        $payment->update($updates);

        return $payment->refresh();
    }
}
