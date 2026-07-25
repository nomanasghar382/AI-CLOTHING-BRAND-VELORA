<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class VisualSearch extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['results' => 'array'];
    }
}
