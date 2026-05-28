<?php

namespace App\Http\Requests\Admin\Sports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->hasPermission('clubs.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:150'],
            'short_name'      => ['nullable', 'string', 'max:10'],
            'slug'            => ['nullable', 'string', 'max:150', 'unique:clubs,slug'],
            'logo_path'       => ['nullable', 'string', 'max:500'],
            'city'            => ['nullable', 'string', 'max:100'],
            'primary_color'   => ['nullable', 'string', 'max:7'],
            'secondary_color' => ['nullable', 'string', 'max:7'],
            'is_active'       => ['boolean'],
            'sort_order'      => ['integer', 'min:0'],
            'metadata'        => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slug = trim((string) $this->input('slug'));
        $this->merge([
            'slug'      => $slug !== '' ? Str::slug($slug) : Str::slug((string) $this->input('name')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
