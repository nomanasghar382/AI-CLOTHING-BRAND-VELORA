<?php

namespace App\Http\Requests\Style;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateBodyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['fit_preference' => ['nullable', 'string', 'max:50'], 'height_unit' => ['nullable', 'in:cm,in'],
            'height' => ['nullable', 'numeric', 'min:1', 'max:300'], 'measurements' => ['nullable', 'array'], 'size_preferences' => ['nullable', 'array']];
    }
}
