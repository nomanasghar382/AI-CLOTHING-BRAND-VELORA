<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class CreatorProfile extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['social_links' => 'array', 'is_accepting_commissions' => 'boolean', 'commission_rate' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(CreatorCollection::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'creator_follows')->withTimestamps();
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(CreatorWithdrawal::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(CreatorCommission::class);
    }
}
