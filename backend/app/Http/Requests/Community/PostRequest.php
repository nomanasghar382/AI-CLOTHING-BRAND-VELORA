<?php

namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;

final class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['body' => ['required', 'string', 'max:2000'], 'media' => ['nullable', 'array', 'max:8'], 'media.*' => ['url', 'max:2048']];
    }
}
