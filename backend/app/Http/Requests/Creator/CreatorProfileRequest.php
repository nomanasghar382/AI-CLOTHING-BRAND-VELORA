<?php

namespace App\Http\Requests\Creator;

use Illuminate\Foundation\Http\FormRequest;

final class CreatorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['handle' => ['required', 'alpha_dash', 'max:50', 'unique:creator_profiles,handle,'.optional($this->user()->creatorProfile)->id], 'display_name' => ['required', 'string', 'max:100'], 'bio' => ['nullable', 'string', 'max:2000'], 'avatar_url' => ['nullable', 'url', 'max:2048'], 'social_links' => ['nullable', 'array'], 'is_accepting_commissions' => ['boolean'], 'commission_rate' => ['nullable', 'numeric', 'between:0,100']];
    }
}
