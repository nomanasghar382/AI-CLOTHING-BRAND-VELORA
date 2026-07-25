<?php

namespace App\Contracts;

interface InternationalShippingProviderInterface
{
    /** Returns a provider quote or null when external quoting is disabled. */
    public function quote(string $country, float $subtotal, string $currency): ?array;
}
