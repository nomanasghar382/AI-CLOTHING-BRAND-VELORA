<?php

namespace App\Http\Controllers\Api\V1\Shopping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shopping\InternationalEstimateRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\DeliveryRule;
use App\Models\DutyRule;
use App\Models\Language;
use App\Models\ProductAvailability;
use App\Models\RegionalSize;
use App\Models\ShipmentTracking;
use App\Models\ShippingCarrier;
use App\Models\ShippingZone;
use App\Models\State;
use App\Models\Warehouse;
use App\Services\Shopping\InternationalCommerceService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class InternationalCommerceController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly InternationalCommerceService $international) {}

    public function countries(): JsonResponse
    {
        return $this->success(Country::query()->where('is_active', true)->orderBy('name')->get());
    }

    public function states(Country $country): JsonResponse
    {
        return $this->success(State::query()->where('country_id', $country->id)->orderBy('name')->get());
    }

    public function cities(Country $country, Request $request): JsonResponse
    {
        return $this->success(City::query()->where('country_id', $country->id)->when($request->integer('state_id'), fn ($q, $state) => $q->where('state_id', $state))->orderBy('name')->get());
    }

    public function currencies(): JsonResponse
    {
        return $this->success(Currency::query()->where('is_active', true)->orderBy('code')->get());
    }

    public function locale(Request $request): JsonResponse
    {
        $data = $request->validate(['country' => ['required', 'string', 'size:2'], 'currency' => ['nullable', 'string', 'size:3'], 'locale' => ['nullable', 'string', 'max:10']]);
        $result = $this->international->locale($data['country'], $data['currency'] ?? null, $data['locale'] ?? null);

        return $this->success(['country' => $result['country'], 'currency' => $result['currency'], 'locale' => $result['language'], 'exchange_rate' => $result['exchange_rate']]);
    }

    public function estimate(InternationalEstimateRequest $request): JsonResponse
    {
        $cart = $request->user()->cart()->with('items.product')->firstOrFail();
        $products = $cart->items->pluck('product')->filter();
        $subtotal = (float) $cart->items->sum(fn ($item) => $item->quantity * $item->unit_price);
        $estimate = $this->international->estimate($request->validated(), $subtotal, $products);
        $stored = $this->international->persistEstimate($estimate);

        return $this->success(['id' => $stored->id, 'currency' => $estimate['currency'], 'shipping_total' => $estimate['shipping_total'], 'tax_total' => $estimate['tax_total'], 'duty_total' => $estimate['duty_total'], 'delivery_days' => ['min' => $estimate['min_days'], 'max' => $estimate['max_days']], 'warehouse' => $estimate['warehouse'], 'carrier' => $estimate['carrier']]);
    }

    public function tracking(ShipmentTracking $tracking): JsonResponse
    {
        abort_unless($tracking->order->user_id === request()->user()->id, 404);

        return $this->success($tracking);
    }

    public function adminIndex(string $resource): JsonResponse
    {
        return $this->success($this->adminModel($resource)::query()->latest()->paginate(50));
    }

    public function adminStore(Request $request, string $resource): JsonResponse
    {
        return $this->success($this->adminModel($resource)::query()->create($this->adminData($request, $resource)), 'International resource created.', 201);
    }

    public function adminUpdate(Request $request, string $resource, int $id): JsonResponse
    {
        $model = $this->adminModel($resource)::query()->findOrFail($id);
        $model->update($this->adminData($request, $resource));

        return $this->success($model, 'International resource updated.');
    }

    public function adminDestroy(string $resource, int $id): JsonResponse
    {
        $this->adminModel($resource)::query()->findOrFail($id)->delete();

        return $this->success(null, 'International resource deleted.');
    }

    private function adminModel(string $resource): string
    {
        return match ($resource) {
            'countries' => Country::class, 'currencies' => Currency::class, 'languages' => Language::class,
            'regional-sizes' => RegionalSize::class, 'shipping-zones' => ShippingZone::class, 'shipping-carriers' => ShippingCarrier::class,
            'delivery-rules' => DeliveryRule::class, 'duty-rules' => DutyRule::class, 'product-availabilities' => ProductAvailability::class,
            'warehouses' => Warehouse::class, 'tracking' => ShipmentTracking::class, default => abort(404),
        };
    }

    private function adminData(Request $request, string $resource): array
    {
        $rules = match ($resource) {
            'countries' => ['code' => ['required', 'string', 'size:2'], 'name' => ['required', 'string'], 'default_currency' => ['nullable', 'size:3'], 'default_language' => ['nullable', 'max:10'], 'is_active' => ['required', 'boolean']],
            'currencies' => ['code' => ['required', 'string', 'size:3'], 'name' => ['required', 'string'], 'symbol' => ['required', 'string', 'max:8'], 'decimal_places' => ['nullable', 'integer', 'min:0', 'max:4'], 'is_active' => ['required', 'boolean']],
            default => ['data' => ['required', 'array']],
        };
        $data = $request->validate($rules);

        return $resource === 'countries' || $resource === 'currencies' ? $data : $data['data'];
    }
}
