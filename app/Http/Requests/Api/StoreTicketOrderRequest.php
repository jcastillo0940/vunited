<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'match_event_code' => ['required', 'string', Rule::exists('match_events', 'code')],
            'ticket_zone_id'   => ['required', 'integer', Rule::exists('ticket_zones', 'id')],
            'quantity'         => ['required', 'integer', 'min:1', 'max:6'],
            'customer_name'    => ['nullable', 'string', 'max:255'],
            'customer_email'   => ['required', 'email', 'max:255'],
            'customer_phone'   => ['nullable', 'string', 'max:30'],
            'accept_terms'     => ['required', 'accepted'],
        ];
    }
}
