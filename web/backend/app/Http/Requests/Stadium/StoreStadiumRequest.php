<?php

namespace App\Http\Requests\Admin\Stadium;

use Illuminate\Foundation\Http\FormRequest;

class StoreStadiumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->hasPermission('stadium.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:200'],
            'subtitle'        => ['nullable', 'string', 'max:300'],
            'location'        => ['nullable', 'string', 'max:200'],
            'address'         => ['nullable', 'string', 'max:300'],
            'capacity'        => ['nullable', 'string', 'max:50'],
            'venue_type'      => ['nullable', 'string', 'max:150'],
            'hero_image_path' => ['nullable', 'string', 'max:500'],
            'map_embed_url'   => ['nullable', 'url', 'max:1000'],
            'zones'           => ['nullable', 'array'],
            'matchday'        => ['nullable', 'array'],
            'rules'           => ['nullable', 'array'],
            'is_active'       => ['boolean'],
            'metadata'        => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'zones'     => $this->normalizeJson($this->input('zones')),
            'matchday'  => $this->normalizeJson($this->input('matchday')),
            'rules'     => $this->normalizeJson($this->input('rules')),
            'metadata'  => $this->normalizeJson($this->input('metadata')),
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
