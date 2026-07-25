<?php

namespace App\Http\Requests\Style;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateAiProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'style_preferences' => ['nullable', 'array'], 'color_preferences' => ['nullable', 'array'],
            'avoidances' => ['nullable', 'array'], 'quiz_answers' => ['nullable', 'array'],
            'default_budget_currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
