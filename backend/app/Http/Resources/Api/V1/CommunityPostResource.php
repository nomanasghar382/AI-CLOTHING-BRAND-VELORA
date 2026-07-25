<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CommunityPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'body' => $this->body, 'media' => $this->media, 'status' => $this->status, 'likes_count' => $this->likes_count, 'comments_count' => $this->comments_count, 'author' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name, 'avatar_url' => $this->user->avatar_url]), 'created_at' => $this->created_at];
    }
}
