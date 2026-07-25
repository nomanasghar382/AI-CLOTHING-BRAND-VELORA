<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EthicalPassport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['claims' => 'array', 'verified_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(EthicalPassportEvidence::class);
    }

    public function isPubliclyVerified(): bool
    {
        return $this->status === 'verified' && $this->verified_at->isPast() && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
