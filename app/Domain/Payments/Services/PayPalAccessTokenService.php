<?php

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Exceptions\PaymentProviderException;
use App\Domain\Payments\Models\PaymentSetting;
use Illuminate\Support\Facades\Http;

class PayPalAccessTokenService
{
    public function getToken(PaymentSetting $setting): string
    {
        $baseUrl = $this->baseUrl($setting);

        $response = Http::withBasicAuth($setting->client_id, $setting->client_secret)
            ->asForm()
            ->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful()) {
            throw new PaymentProviderException('Failed to obtain PayPal access token.');
        }

        $token = $response->json('access_token');

        if (empty($token)) {
            throw new PaymentProviderException('PayPal returned an empty access token.');
        }

        return $token;
    }

    public function baseUrl(PaymentSetting $setting): string
    {
        return $setting->mode === 'live'
            ? config('payments.paypal.live_base_url')
            : config('payments.paypal.sandbox_base_url');
    }
}
