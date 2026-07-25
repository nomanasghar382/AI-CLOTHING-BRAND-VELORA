<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class StyleFeedback extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
