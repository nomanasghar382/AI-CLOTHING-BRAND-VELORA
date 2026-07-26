<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class CreatorCollection extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(CreatorProfile::class, 'creator_profile_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'creator_collection_products')->withPivot('sort_order')->orderBy('creator_collection_products.sort_order');
    }
}
