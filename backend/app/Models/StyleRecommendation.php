<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class StyleRecommendation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['request_context' => 'array', 'weather' => 'array', 'response' => 'array', 'generated_at' => 'datetime'];
    }
}
