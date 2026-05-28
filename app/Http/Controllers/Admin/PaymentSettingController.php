<?php

namespace App\Http\Controllers\Admin;

use App\Support\Audit\RecordsAdminAudit;
use App\Domain\Payments\Models\PaymentSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payments\UpdatePaymentSettingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.payment-settings.edit', [
            'setting' => $this->setting(),
        ]);
    }

    public function update(UpdatePaymentSettingRequest $request): RedirectResponse
    {
        $setting = $this->setting();
        $before  = $this->maskSecret($setting->attributesToArray());

        $validated = $request->validated();

        // Only overwrite client_secret when a new non-empty value is explicitly submitted.
        if (empty($validated['client_secret'])) {
            unset($validated['client_secret']);
        }

        $setting->update($validated);
        $setting->refresh();

        $after = $this->maskSecret($setting->attributesToArray());

        RecordsAdminAudit::updated('payment_settings', $setting, $request, $before, $after);

        return redirect()->route('admin.payment-settings.edit');
    }

    private function setting(): PaymentSetting
    {
        return PaymentSetting::query()->firstOrCreate(
            ['provider' => 'paypal'],
            [
                'mode'          => 'sandbox',
                'client_id'     => null,
                'client_secret' => null,
                'webhook_id'    => null,
                'currency'      => 'USD',
                'is_enabled'    => false,
                'metadata'      => null,
            ],
        );
    }

    private function maskSecret(array $attributes): array
    {
        if (! empty($attributes['client_secret'])) {
            $attributes['client_secret'] = '***';
        }

        return $attributes;
    }
}
