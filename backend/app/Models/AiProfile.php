<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class AiProfile extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['style_preferences' => 'array', 'color_preferences' => 'array', 'avoidances' => 'array', 'quiz_answers' => 'array'];
    }
}
