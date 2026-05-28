<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain\Payments\Enums\PaymentEventProcessingStatus;
use App\Domain\Payments\Enums\PaymentEventVerificationStatus;
use App\Domain\Payments\Models\PaymentEvent;
use App\Domain\Payments\Services\PayPalWebhookProcessor;
use App\Domain\Payments\Services\PayPalWebhookVerificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayPalWebhookController extends Controller
{
    public function __construct(
        private readonly PayPalWebhookVerificationService $verificationService,
        private readonly PayPalWebhookProcessor $processor,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        if (empty($payload) || ! isset($payload['event_type'])) {
            return response()->json(['error' => 'Invalid or missing event_type in payload.'], 400);
        }

        $eventType      = $payload['event_type'];
        $providerEventId = $payload['id'] ?? null;

        // Idempotency: skip if we already received this event ID
        if ($providerEventId && $this->eventAlreadyReceived($providerEventId)) {
            return response()->json(['message' => 'Event already received.'], 200);
        }

        [$providerOrderId, $providerCaptureId] = $this->extractProviderIds($eventType, $payload['resource'] ?? []);

        $headers = $this->extractPayPalHeaders($request);

        $event = PaymentEvent::create([
            'provider'            => 'paypal',
            'provider_event_id'   => $providerEventId,
            'event_type'          => $eventType,
            'resource_type'       => $payload['resource_type'] ?? null,
            'provider_order_id'   => $providerOrderId,
            'provider_capture_id' => $providerCaptureId,
            'verification_status' => PaymentEventVerificationStatus::Pending,
            'processing_status'   => PaymentEventProcessingStatus::Received,
            'payload'             => $payload,
            'headers'             => $headers,
            'received_at'         => now(),
        ]);

        $verificationStatus = $this->verificationService->verify($headers, $payload);

        $event->update([
            'verification_status' => $verificationStatus,
            'verified_at' => $verificationStatus === PaymentEventVerificationStatus::Verified ? now() : null,
        ]);

        if ($this->shouldProcess($verificationStatus)) {
            $this->processor->process($event->refresh());
        }

        return response()->json(['received' => true], 200);
    }

    private function eventAlreadyReceived(string $providerEventId): bool
    {
        return PaymentEvent::query()
            ->where('provider', 'paypal')
            ->where('provider_event_id', $providerEventId)
            ->exists();
    }

    private function extractProviderIds(string $eventType, array $resource): array
    {
        $orderId   = null;
        $captureId = null;

        if (str_starts_with($eventType, 'CHECKOUT.ORDER')) {
            $orderId = $resource['id'] ?? null;
        } elseif (str_starts_with($eventType, 'PAYMENT.CAPTURE')) {
            $captureId = $resource['id'] ?? null;
            $orderId   = data_get($resource, 'supplementary_data.related_ids.order_id');
        }

        return [$orderId, $captureId];
    }

    private function extractPayPalHeaders(Request $request): array
    {
        $keys   = [
            'paypal-auth-algo',
            'paypal-cert-url',
            'paypal-transmission-id',
            'paypal-transmission-sig',
            'paypal-transmission-time',
            'paypal-webhook-id',
        ];
        $stored = [];

        foreach ($keys as $key) {
            $value = $request->header($key);
            if ($value !== null) {
                $stored[$key] = $value;
            }
        }

        return $stored;
    }

    private function shouldProcess(PaymentEventVerificationStatus $status): bool
    {
        return in_array($status, [
            PaymentEventVerificationStatus::Verified,
            PaymentEventVerificationStatus::Skipped,
        ], strict: true);
    }
}
