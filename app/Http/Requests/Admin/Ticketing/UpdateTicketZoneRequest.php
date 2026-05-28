<?php

namespace App\Http\Requests\Admin\Ticketing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateTicketZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null
            && $this->user('admin')->hasPermission('ticket_zones.manage');
    }

    public function rules(): array
    {
        $matchEvent = $this->route('matchEvent');
        $ticketZone = $this->route('ticketZone');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('ticket_zones', 'slug')
                    ->ignore($ticketZone?->id)
                    ->where(fn ($query) => $query->where('match_event_id', $matchEvent?->id)),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'available_quantity' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = (string) $this->input('name');
        $slug = (string) $this->input('slug');

        $this->merge([
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($name),
            'currency' => strtoupper((string) $this->input('currency')),
            'is_active' => $this->boolean('is_active'),
            'metadata' => $this->normalizeJson($this->input('metadata')),
        ]);
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
