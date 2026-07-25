<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ShippingZone extends Model
{
    protected $guarded = [];

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'shipping_zone_countries');
    }
}
