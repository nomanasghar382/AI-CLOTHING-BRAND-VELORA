<?php

namespace Tests\Feature\Api\V1\Shopping;

use App\Models\Country;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\DeliveryRule;
use App\Models\Product;
use App\Models\ShippingCarrier;
use App\Models\ShippingZone;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InternationalCommerceTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_receives_localized_shipping_tax_and_duty_estimate(): void
    {
        $user = User::factory()->create();
        $country = Country::query()->create(['code' => 'CA', 'name' => 'Canada', 'default_currency' => 'CAD', 'default_language' => 'fr']);
        $currency = Currency::query()->create(['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => '$']);
        CurrencyRate::query()->create(['currency_id' => $currency->id, 'rate' => 1.35, 'effective_at' => now()]);
        $zone = ShippingZone::query()->create(['name' => 'Canada']);
        $zone->countries()->attach($country);
        $carrier = ShippingCarrier::query()->create(['name' => 'Demo', 'code' => 'demo']);
        DeliveryRule::query()->create(['shipping_zone_id' => $zone->id, 'shipping_carrier_id' => $carrier->id, 'name' => 'Ground', 'base_amount' => 10, 'min_days' => 2, 'max_days' => 5]);
        Warehouse::query()->create(['code' => 'CA-1', 'name' => 'Toronto', 'country' => 'CA']);
        $product = Product::factory()->create(['status' => 'published', 'price' => 20, 'stock_quantity' => 3]);
        Sanctum::actingAs($user);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 2])->assertCreated();

        $this->postJson('/api/v1/international/shipping-estimates', ['country' => 'CA', 'currency' => 'CAD'])
            ->assertOk()
            ->assertJsonPath('data.currency', 'CAD')
            ->assertJsonPath('data.shipping_total', 13.5)
            ->assertJsonPath('data.delivery_days.min', 2)
            ->assertJsonPath('data.warehouse.code', 'CA-1');
    }
}
