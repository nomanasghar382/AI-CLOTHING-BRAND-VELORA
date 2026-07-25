<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class BodyProfile extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['measurements' => 'array', 'size_preferences' => 'array'];
    }
}
