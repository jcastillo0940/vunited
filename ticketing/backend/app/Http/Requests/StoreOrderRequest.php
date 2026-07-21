<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_email' => ['required', 'email'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.zone_id' => ['required', 'string'],
            'items.*.quantity' => ['required_without:items.*.seat_ids', 'integer', 'min:1', 'max:20'],
            'items.*.seat_ids' => ['array'],
            'items.*.seat_ids.*' => ['string'],
        ];
    }
}
