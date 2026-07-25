<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['is_featured' => 'boolean', 'is_trending' => 'boolean']; }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id'); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
}
