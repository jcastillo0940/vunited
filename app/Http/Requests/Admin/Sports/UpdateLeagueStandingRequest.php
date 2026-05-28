<?php

namespace App\Http\Requests\Admin\Sports;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeagueStandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->hasPermission('standings.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'club_id'         => ['required', 'exists:clubs,id'],
            'competition'     => ['required', 'string', 'max:50'],
            'season'          => ['required', 'string', 'max:10'],
            'position'        => ['required', 'integer', 'min:1'],
            'played'          => ['required', 'integer', 'min:0'],
            'won'             => ['required', 'integer', 'min:0'],
            'drawn'           => ['required', 'integer', 'min:0'],
            'lost'            => ['required', 'integer', 'min:0'],
            'goals_for'       => ['required', 'integer', 'min:0'],
            'goals_against'   => ['required', 'integer', 'min:0'],
            'goal_difference' => ['required', 'integer'],
            'points'          => ['required', 'integer', 'min:0'],
            'is_active'       => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
