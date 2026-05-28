<?php

namespace App\Http\Requests\Admin\Payments;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null
            && $this->user('admin')->hasPermission('payment_settings.update');
    }

    public function rules(): array
    {
        return [
            'mode'          => ['required', 'string', 'in:sandbox,live'],
            'client_id'     => ['nullable', 'string', 'max:255'],
            'client_secret' => ['nullable', 'string', 'max:500'],
            'webhook_id'    => ['nullable', 'string', 'max:255'],
            'currency'      => ['required', 'string', 'size:3'],
            'is_enabled'    => ['sometimes', 'boolean'],
        ];
    }
}
