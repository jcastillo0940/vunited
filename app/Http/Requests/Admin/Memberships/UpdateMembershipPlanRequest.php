<?php

namespace App\Http\Requests\Admin\Memberships;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMembershipPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null
            && $this->user('admin')->hasPermission('membership_plans.manage');
    }

    public function rules(): array
    {
        $membershipPlan = $this->route('membershipPlan');

        return [
            'code' => ['required', 'string', 'max:100', Rule::unique('membership_plans', 'code')->ignore($membershipPlan?->id)],
            'name' => ['required', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'duration_months' => ['required', 'integer', 'min:1'],
            'benefits' => ['nullable', 'array'],
            'kit_items' => ['nullable', 'array'],
            'partner_discounts' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'benefits' => $this->normalizeList($this->input('benefits')),
            'kit_items' => $this->normalizeList($this->input('kit_items')),
            'partner_discounts' => $this->normalizeList($this->input('partner_discounts')),
            'metadata' => $this->normalizeJson($this->input('metadata')),
            'is_active' => $this->boolean('is_active'),
            'currency' => strtoupper((string) $this->input('currency')),
        ]);
    }

    private function normalizeList(mixed $value): ?array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value), fn ($item) => $item !== ''));
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value) ?: [])));
    }

    private function normalizeJson(mixed $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }
}
