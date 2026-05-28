<?php

namespace App\Http\Requests\Api;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMembershipOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'             => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255'],
            'identification_number' => ['nullable', 'string', 'max:50'],
            'birth_date'            => ['nullable', 'date'],
            'age'                   => ['nullable', 'integer', 'min:1', 'max:120'],
            'address'               => ['nullable', 'string', 'max:500'],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'membership_plan'       => [
                'required',
                'string',
                Rule::exists('membership_plans', 'code')->where(
                    fn (Builder $query): Builder => $query->where('is_active', true),
                ),
            ],
            'accept_terms'          => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'membership_plan.exists' => 'No hay un plan de membresia activo disponible para esta seleccion.',
        ];
    }
}
