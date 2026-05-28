<?php

namespace App\Http\Requests\Admin\Ticketing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateMatchEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null
            && $this->user('admin')->hasPermission('match_events.manage');
    }

    public function rules(): array
    {
        $matchEvent = $this->route('matchEvent');

        return [
            'code' => ['nullable', 'string', 'max:255', Rule::unique('match_events', 'code')->ignore($matchEvent?->id)],
            'home_team' => ['required', 'string', 'max:255'],
            'away_team' => ['required', 'string', 'max:255'],
            'competition' => ['nullable', 'string', 'max:255'],
            'round_label' => ['nullable', 'string', 'max:255'],
            'match_date' => ['required', 'date'],
            'stadium_name' => ['nullable', 'string', 'max:255'],
            'stadium_location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['scheduled', 'live', 'finished', 'postponed', 'cancelled'])],
            'home_score' => ['nullable', 'integer', 'min:0'],
            'away_score' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $code = trim((string) $this->input('code'));
        $generated = Str::upper(Str::slug(
            trim((string) $this->input('competition')) . '-' .
            trim((string) $this->input('round_label')) . '-' .
            trim((string) $this->input('home_team')) . '-' .
            trim((string) $this->input('away_team')) . '-' .
            date('Ymd', strtotime((string) $this->input('match_date') ?: 'now'))
        ));

        $this->merge([
            'code' => $code !== '' ? Str::upper(Str::slug($code)) : $generated,
            'status' => strtolower((string) $this->input('status')),
            'is_featured' => $this->boolean('is_featured'),
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
