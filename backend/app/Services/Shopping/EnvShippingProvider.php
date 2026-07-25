<?php

namespace App\Services\Shopping;

use App\Contracts\InternationalShippingProviderInterface;

/**
 * Explicit opt-in provider seam. No carrier calls occur without environment configuration.
 */
final class EnvShippingProvider implements InternationalShippingProviderInterface
{
    public function quote(string $country, float $subtotal, string $currency): ?array
    {
        if (! config('services.international_shipping.enabled')) {
            return null;
        }

        return null;
    }
}
