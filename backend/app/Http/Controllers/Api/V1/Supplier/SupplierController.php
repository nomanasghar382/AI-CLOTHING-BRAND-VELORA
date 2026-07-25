<?php

namespace App\Http\Controllers\Api\V1\Supplier;

use App\Events\SupplierInventorySynced;
use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\InventoryAdjustmentRequest;
use App\Http\Requests\Supplier\SupplierProfileRequest;
use App\Http\Resources\Api\V1\SupplierProfileResource;
use App\Jobs\SyncSupplierInventory;
use App\Models\SupplierInventory;
use App\Models\SupplierProfile;
use App\Services\Supplier\InventorySyncService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class SupplierController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly InventorySyncService $inventory) {}

    public function profile(Request $request): JsonResponse
    {
        $profile = SupplierProfile::query()->firstOrCreate(['user_id' => $request->user()->id], ['company_name' => $request->user()->name, 'slug' => Str::slug($request->user()->name).'-'.$request->user()->id]);

        return $this->success(new SupplierProfileResource($profile->load(['addresses', 'documents', 'certifications'])));
    }

    public function updateProfile(SupplierProfileRequest $request): JsonResponse
    {
        $profile = SupplierProfile::query()->firstOrCreate(['user_id' => $request->user()->id], ['slug' => Str::slug($request->validated('company_name')).'-'.$request->user()->id] + $request->validated());
        $profile->update($request->validated());

        return $this->success(new SupplierProfileResource($profile->fresh()), 'Supplier profile updated.');
    }

    public function nested(Request $request, string $resource, ?int $id = null): JsonResponse
    {
        $supplier = $this->supplier($request);
        $definitions = [
            'addresses' => ['supplier_addresses', ['type', 'line1', 'line2', 'city', 'region', 'postal_code', 'country', 'is_default']],
            'documents' => ['supplier_documents', ['type', 'name', 'url', 'status', 'expires_at']],
            'certifications' => ['supplier_certifications', ['name', 'issuer', 'reference', 'status', 'issued_at', 'expires_at']],
            'messages' => ['supplier_messages', ['subject', 'body']],
            'webhooks' => ['webhook_endpoints', ['url', 'events', 'is_active']],
        ];
        abort_unless(isset($definitions[$resource]), 404);
        [$table, $fields] = $definitions[$resource];
        if ($request->isMethod('get')) {
            return $this->success(DB::table($table)->where('supplier_profile_id', $supplier->id)->latest()->get());
        }
        $data = $request->validate(array_fill_keys($fields, ['sometimes']));
        if ($resource === 'webhooks') {
            $data['events'] = $data['events'] ?? [];
            $data['secret'] = Str::random(48);
        }
        if ($resource === 'messages') {
            $data['sender_id'] = $request->user()->id;
        }
        if ($id) {
            $row = DB::table($table)->where('supplier_profile_id', $supplier->id)->where('id', $id);
            abort_unless($row->exists(), 404);
            $row->update($data + ['updated_at' => now()]);

            return $this->success($row->first(), ucfirst(rtrim($resource, 's')).' updated.');
        }
        $created = $data + ['supplier_profile_id' => $supplier->id, 'created_at' => now(), 'updated_at' => now()];
        $newId = DB::table($table)->insertGetId($created);

        return $this->success(DB::table($table)->find($newId), ucfirst(rtrim($resource, 's')).' created.', 201);
    }

    public function create(Request $request, string $resource): JsonResponse
    {
        return in_array($resource, ['addresses', 'documents', 'certifications', 'messages', 'webhooks'], true)
            ? $this->nested($request, $resource)
            : $this->operations($request, $resource);
    }

    public function read(Request $request, string $resource): JsonResponse
    {
        return in_array($resource, ['addresses', 'documents', 'certifications', 'messages', 'webhooks'], true)
            ? $this->nested($request, $resource)
            : $this->operations($request, $resource);
    }

    public function inventory(Request $request): JsonResponse
    {
        return $this->success(SupplierInventory::query()->with('product:id,name,sku')->where('supplier_profile_id', $this->supplier($request)->id)->paginate(min(100, max(1, $request->integer('per_page', 20)))));
    }

    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate(['lines' => ['required', 'array', 'min:1', 'max:1000'], 'lines.*.product_id' => ['required', 'exists:products,id'], 'lines.*.sku' => ['required', 'string', 'max:100'], 'lines.*.on_hand' => ['required', 'integer', 'min:0'], 'lines.*.version' => ['nullable', 'integer', 'min:1'], 'async' => ['sometimes', 'boolean']]);
        $supplier = $this->supplier($request);
        if ($data['async'] ?? false) {
            SyncSupplierInventory::dispatch($supplier->id, $data['lines']);

            return $this->success(['queued' => true], 'Inventory sync queued.', 202);
        }
        $result = $this->inventory->sync($supplier, $data['lines']);
        SupplierInventorySynced::dispatch($supplier, $result['updated']);

        return $this->success($result, 'Inventory synchronized.');
    }

    public function adjustment(InventoryAdjustmentRequest $request, SupplierInventory $inventory): JsonResponse
    {
        abort_unless($inventory->supplier_profile_id === $this->supplier($request)->id || $request->user()->hasRole('admin', 'super-admin'), 404);

        return $this->success($this->inventory->adjust($inventory, $request->integer('quantity_delta'), $request->input('reason'), $request->input('idempotency_key'), $request->user()->id), 'Inventory adjusted.');
    }

    public function reserve(Request $request, SupplierInventory $inventory): JsonResponse
    {
        $supplier = $this->supplier($request);
        abort_unless($inventory->supplier_profile_id === $supplier->id, 404);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1'], 'reference' => ['required', 'string', 'max:100'], 'expires_at' => ['nullable', 'date', 'after:now']]);

        return DB::transaction(function () use ($inventory, $data) {
            $locked = SupplierInventory::query()->lockForUpdate()->findOrFail($inventory->id);
            abort_if($locked->available < $data['quantity'], 422, 'Insufficient available inventory.');
            $id = DB::table('inventory_reservations')->insertGetId(['supplier_inventory_id' => $locked->id, 'reference' => $data['reference'], 'quantity' => $data['quantity'], 'expires_at' => $data['expires_at'] ?? null, 'created_at' => now(), 'updated_at' => now()]);
            $locked->update(['reserved' => $locked->reserved + $data['quantity'], 'available' => $locked->available - $data['quantity'], 'version' => $locked->version + 1]);

            return $this->success(DB::table('inventory_reservations')->find($id), 'Inventory reserved.', 201);
        });
    }

    public function operations(Request $request, string $resource, ?int $id = null): JsonResponse
    {
        $supplier = $this->supplier($request);
        $tables = ['purchase-orders' => 'purchase_orders', 'shipments' => 'supplier_shipments', 'returns' => 'supplier_returns', 'settlements' => 'supplier_settlements', 'payments' => 'supplier_payments', 'performance' => 'supplier_performance_snapshots', 'sync-jobs' => 'inventory_sync_jobs'];
        abort_unless(isset($tables[$resource]), 404);
        $table = $tables[$resource];
        $query = DB::table($table);
        if ($table === 'supplier_payments') {
            $query->whereIn('supplier_settlement_id', DB::table('supplier_settlements')->where('supplier_profile_id', $supplier->id)->select('id'));
        } else {
            $query->where('supplier_profile_id', $supplier->id);
        }
        if ($request->isMethod('get')) {
            return $this->success($id ? $query->where('id', $id)->first() : $query->latest()->paginate(50));
        }
        abort_unless(in_array($resource, ['purchase-orders', 'shipments', 'returns', 'settlements', 'payments'], true), 405);
        $data = $request->validate(['status' => ['sometimes', 'string', 'max:50'], 'items' => ['nullable', 'array'], 'total' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'purchase_order_id' => ['nullable', 'integer', Rule::exists('purchase_orders', 'id')->where(fn ($q) => $q->where('supplier_profile_id', $supplier->id))], 'tracking_number' => ['nullable', 'string', 'max:100'], 'carrier' => ['nullable', 'string', 'max:100'], 'reason' => ['nullable', 'string', 'max:2000'], 'amount' => ['nullable', 'numeric', 'min:0'], 'gross_amount' => ['nullable', 'numeric', 'min:0'], 'fee_amount' => ['nullable', 'numeric', 'min:0'], 'net_amount' => ['nullable', 'numeric', 'min:0'], 'supplier_settlement_id' => ['nullable', 'integer', Rule::exists('supplier_settlements', 'id')->where(fn ($q) => $q->where('supplier_profile_id', $supplier->id))], 'method' => ['nullable', 'string', 'max:100']]);
        if (isset($data['items'])) {
            $data['items'] = json_encode($data['items']);
        }
        $data += ['supplier_profile_id' => $supplier->id, 'created_at' => now(), 'updated_at' => now()];
        if ($resource === 'purchase-orders') {
            $data['number'] = 'PO-'.strtoupper(Str::random(10));
        }
        if ($resource === 'returns') {
            $data['number'] = 'RET-'.strtoupper(Str::random(10));
        }
        if ($resource === 'settlements') {
            $data['number'] = 'SET-'.strtoupper(Str::random(10));
        }
        if ($resource === 'payments') {
            $data['reference'] = 'PAY-'.strtoupper(Str::random(10));
            unset($data['supplier_profile_id']);
        }
        $newId = DB::table($table)->insertGetId($data);

        return $this->success(DB::table($table)->find($newId), 'Supplier operation created.', 201);
    }

    public function token(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'abilities' => ['sometimes', 'array'],
            'abilities.*' => ['string', Rule::in(['supplier:read', 'supplier:write'])],
        ]);
        $abilities = $data['abilities'] ?? ['supplier:read', 'supplier:write'];
        if ($abilities === []) {
            $abilities = ['supplier:read', 'supplier:write'];
        }
        $token = $request->user()->createToken($data['name'], $abilities);

        return $this->success(['id' => $token->accessToken->id, 'token' => $token->plainTextToken], 'API token created.', 201);
    }

    private function supplier(Request $request): SupplierProfile
    {
        if ($request->user()->hasRole('admin', 'super-admin') && $request->filled('supplier_id')) {
            return SupplierProfile::query()->findOrFail($request->integer('supplier_id'));
        }

        return SupplierProfile::query()->firstOrCreate(['user_id' => $request->user()->id], ['company_name' => $request->user()->name, 'slug' => Str::slug($request->user()->name).'-'.$request->user()->id]);
    }
}
