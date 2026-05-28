<?php

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Enums\PaymentEventVerificationStatus;
use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Domain\Payments\Models\PaymentSetting;
use Illuminate\Support\Facades\Http;
use Throwable;

class PayPalWebhookVerificationService
{
    public function __construct(
        private readonly PayPalAccessTokenService $tokenService,
    ) {}

    public function verify(array $headers, array $payload): PaymentEventVerificationStatus
    {
        $setting = PaymentSetting::query()->where('provider', 'paypal')->first();

        if (! $setting || ! $setting->is_enabled) {
            return PaymentEventVerificationStatus::Skipped;
        }

        if (empty($setting->webhook_id)) {
            return PaymentEventVerificationStatus::Skipped;
        }

        try {
            $token   = $this->tokenService->getToken($setting);
            $baseUrl = $this->tokenService->baseUrl($setting);

            $response = Http::withToken($token)
                ->post("{$baseUrl}/v1/notifications/verify-webhook-signature", [
                    'auth_algo'         => $headers['paypal-auth-algo'] ?? '',
                    'cert_url'          => $headers['paypal-cert-url'] ?? '',
                    'transmission_id'   => $headers['paypal-transmission-id'] ?? '',
                    'transmission_sig'  => $headers['paypal-transmission-sig'] ?? '',
                    'transmission_time' => $headers['paypal-transmission-time'] ?? '',
                    'webhook_id'        => $setting->webhook_id,
                    'webhook_event'     => $payload,
                ]);

            if (! $response->successful()) {
                return PaymentEventVerificationStatus::Failed;
            }

            return $response->json('verification_status') === 'SUCCESS'
                ? PaymentEventVerificationStatus::Verified
                : PaymentEventVerificationStatus::Failed;
        } catch (PaymentProviderException) {
            return PaymentEventVerificationStatus::Failed;
        } catch (Throwable) {
            return PaymentEventVerificationStatus::Failed;
        }
    }
}
