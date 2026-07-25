<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['email_marketing' => 'boolean', 'price_alerts' => 'boolean', 'restock_alerts' => 'boolean', 'loyalty_updates' => 'boolean', 'referral_updates' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
