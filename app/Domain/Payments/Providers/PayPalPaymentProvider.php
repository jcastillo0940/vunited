<?php

namespace App\Domain\Payments\Providers;

use App\Domain\Payments\Contracts\PaymentProvider;
use App\Domain\Payments\Data\PaymentProviderResult;
use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentSetting;
use App\Domain\Payments\Services\PayPalAccessTokenService;
use Illuminate\Support\Facades\Http;
use Throwable;

class PayPalPaymentProvider implements PaymentProvider
{
    public function __construct(
        private readonly PayPalAccessTokenService $tokenService,
    ) {}

    public function createOrder(Payment $payment): PaymentProviderResult
    {
        try {
            $setting = $this->loadSetting();
            $token   = $this->tokenService->getToken($setting);
            $baseUrl = $this->tokenService->baseUrl($setting);

            $response = Http::withToken($token)
                ->post("{$baseUrl}/v2/checkout/orders", $this->buildOrderPayload($payment));

            if (! $response->successful()) {
                return PaymentProviderResult::failure(
                    $response->json('message') ?? 'PayPal createOrder failed.',
                    $response->json() ?? [],
                );
            }

            $data        = $response->json();
            $approveLink = collect($data['links'] ?? [])->firstWhere('rel', 'approve');

            if (! $approveLink) {
                return PaymentProviderResult::failure(
                    'PayPal response is missing the approve link.',
                    $data,
                );
            }

            return PaymentProviderResult::success(
                providerOrderId: $data['id'],
                redirectUrl: $approveLink['href'],
                rawPayload: $data,
                status: $data['status'] ?? null,
            );
        } catch (PaymentProviderException $e) {
            return PaymentProviderResult::failure($e->getMessage());
        } catch (Throwable $e) {
            return PaymentProviderResult::failure('PayPal createOrder request failed.');
        }
    }

    public function captureOrder(Payment $payment, array $payload = []): PaymentProviderResult
    {
        if (empty($payment->provider_order_id)) {
            return PaymentProviderResult::failure(
                'Payment has no provider_order_id to capture.',
            );
        }

        try {
            $setting = $this->loadSetting();
            $token   = $this->tokenService->getToken($setting);
            $baseUrl = $this->tokenService->baseUrl($setting);

            $response = Http::withToken($token)
                ->post("{$baseUrl}/v2/checkout/orders/{$payment->provider_order_id}/capture");

            if (! $response->successful()) {
                return PaymentProviderResult::failure(
                    $response->json('message') ?? 'PayPal captureOrder failed.',
                    $response->json() ?? [],
                );
            }

            $data      = $response->json();
            $captureId = data_get($data, 'purchase_units.0.payments.captures.0.id');

            return PaymentProviderResult::success(
                providerOrderId: $data['id'] ?? $payment->provider_order_id,
                providerCaptureId: $captureId,
                rawPayload: $data,
                status: $data['status'] ?? null,
            );
        } catch (PaymentProviderException $e) {
            return PaymentProviderResult::failure($e->getMessage());
        } catch (Throwable $e) {
            return PaymentProviderResult::failure('PayPal captureOrder request failed.');
        }
    }

    public function refund(Payment $payment, array $payload = []): PaymentProviderResult
    {
        return PaymentProviderResult::failure(
            'Refunds are not yet supported. Implement in a future phase.',
        );
    }

    private function loadSetting(): PaymentSetting
    {
        $setting = PaymentSetting::query()->where('provider', 'paypal')->first();

        if (! $setting) {
            throw new PaymentProviderException('PayPal payment settings not found.');
        }

        if (! $setting->is_enabled) {
            throw new PaymentProviderException('PayPal is not enabled.');
        }

        if (empty($setting->client_id) || empty($setting->client_secret)) {
            throw new PaymentProviderException(
                'PayPal credentials (client_id or client_secret) are not configured.',
            );
        }

        return $setting;
    }

    private function buildOrderPayload(Payment $payment): array
    {
        $unit = [
            'reference_id' => 'payment-' . $payment->id,
            'amount'       => [
                'currency_code' => $payment->currency,
                'value'         => $payment->amount,
            ],
        ];

        if (! empty($payment->description)) {
            $unit['description'] = $payment->description;
        }

        return [
            'intent'              => 'CAPTURE',
            'purchase_units'      => [$unit],
            'application_context' => [
                'brand_name' => 'Veraguas United FC',
                'locale'     => 'es-PA',
                'user_action' => 'PAY_NOW',
                'return_url' => $payment->metadata['paypal_return_url'] ?? config('payments.paypal.return_url'),
                'cancel_url' => $payment->metadata['paypal_cancel_url'] ?? config('payments.paypal.cancel_url'),
            ],
        ];
    }
}
