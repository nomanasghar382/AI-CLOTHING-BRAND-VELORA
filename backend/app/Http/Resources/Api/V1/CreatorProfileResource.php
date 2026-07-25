<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CreatorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'handle' => $this->handle, 'display_name' => $this->display_name, 'bio' => $this->bio, 'avatar_url' => $this->avatar_url, 'social_links' => $this->social_links, 'is_accepting_commissions' => $this->is_accepting_commissions, 'commission_rate' => $this->commission_rate, 'followers_count' => $this->when(isset($this->followers_count), $this->followers_count)];
    }
}
