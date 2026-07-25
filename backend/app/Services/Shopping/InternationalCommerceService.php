<?php

namespace App\Services\Shopping;

use App\Models\Country;
use App\Models\CurrencyRate;
use App\Models\DeliveryRule;
use App\Models\DutyRule;
use App\Models\ProductAvailability;
use App\Models\ShippingEstimate;
use App\Models\ShippingZone;
use App\Models\TaxRule;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class InternationalCommerceService
{
    public function locale(string $country, ?string $currency = null, ?string $language = null): array
    {
        $country = Country::query()->where('code', strtoupper($country))->where('is_active', true)->first();
        if (! $country) {
            throw ValidationException::withMessages(['country' => ['This country is not supported.']]);
        }
        $currency ??= $country->default_currency ?? 'USD';
        $rate = strtoupper($currency) === 'USD' ? 1.0 : (float) CurrencyRate::query()
            ->whereHas('currency', fn ($query) => $query->where('code', strtoupper($currency))->where('is_active', true))
            ->where('effective_at', '<=', now())->latest('effective_at')->value('rate');
        if (! $rate) {
            throw ValidationException::withMessages(['currency' => ['This currency is not supported.']]);
        }

        return ['country' => $country, 'currency' => strtoupper($currency), 'language' => $language ?? $country->default_language ?? 'en', 'exchange_rate' => $rate];
    }

    public function estimate(array $attributes, float $subtotal, Collection $products): array
    {
        $context = $this->locale($attributes['country'], $attributes['currency'] ?? null, $attributes['locale'] ?? null);
        $blocked = ProductAvailability::query()->whereIn('product_id', $products->pluck('id'))
            ->where('country_id', $context['country']->id)->where('is_available', false)->exists();
        if ($blocked) {
            throw ValidationException::withMessages(['country' => ['One or more cart products cannot be shipped to this country.']]);
        }
        $zone = ShippingZone::query()->where('is_active', true)->whereHas('countries', fn ($query) => $query->whereKey($context['country']->id))->first();
        $rule = DeliveryRule::query()->with('carrier')->where('is_active', true)
            ->when($zone, fn ($query) => $query->where('shipping_zone_id', $zone->id), fn ($query) => $query->whereNull('shipping_zone_id'))
            ->where(fn ($query) => $query->whereNull('minimum_order_amount')->orWhere('minimum_order_amount', '<=', $subtotal))->orderBy('base_amount')->first();
        $amount = $rule ? (float) $rule->base_amount : 0.0;
        $taxRate = (float) TaxRule::query()->where('is_active', true)->where('country', $context['country']->code)->value('rate');
        $duty = DutyRule::query()->where('country_id', $context['country']->id)->where('is_active', true)->where('threshold', '<=', $subtotal)->max('rate') ?? 0;
        $warehouse = Warehouse::query()->where('is_active', true)->where('country', $context['country']->code)->first()
            ?? Warehouse::query()->where('is_active', true)->first();

        return [
            ...$context, 'shipping_total' => round($amount * $context['exchange_rate'], 2),
            'tax_total' => round($subtotal * ($taxRate / 100), 2),
            'duty_total' => round($subtotal * ((float) $duty / 100), 2),
            'min_days' => $rule?->min_days ?? 7, 'max_days' => $rule?->max_days ?? 14,
            'carrier' => $rule?->carrier, 'warehouse' => $warehouse,
        ];
    }

    public function persistEstimate(array $estimate, ?int $orderId = null): ShippingEstimate
    {
        return ShippingEstimate::query()->create([
            'order_id' => $orderId, 'shipping_carrier_id' => $estimate['carrier']?->id, 'country' => $estimate['country']->code,
            'amount' => $estimate['shipping_total'], 'currency' => $estimate['currency'], 'min_days' => $estimate['min_days'],
            'max_days' => $estimate['max_days'], 'payload' => ['tax_total' => $estimate['tax_total'], 'duty_total' => $estimate['duty_total']],
        ]);
    }
}
