<?php

namespace App\Http\Requests\Admin\Sports;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatchGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->hasPermission('match_goals.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'club_id'      => ['required', 'exists:clubs,id'],
            'player_id'    => ['nullable', 'exists:players,id'],
            'scorer_name'  => ['nullable', 'string', 'max:150'],
            'minute'       => ['nullable', 'integer', 'min:1', 'max:120'],
            'is_own_goal'  => ['boolean'],
            'is_penalty'   => ['boolean'],
            'sort_order'   => ['integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_own_goal' => $this->boolean('is_own_goal'),
            'is_penalty'  => $this->boolean('is_penalty'),
        ]);
    }
}
