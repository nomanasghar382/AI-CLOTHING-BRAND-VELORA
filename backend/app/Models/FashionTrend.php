<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class FashionTrend extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['tags' => 'array', 'confidence' => 'decimal:2', 'is_active' => 'boolean'];
    }
}
