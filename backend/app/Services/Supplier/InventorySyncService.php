<?php

namespace App\Services\Supplier;

use App\Models\SupplierInventory;
use App\Models\SupplierProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class InventorySyncService
{
    public function sync(SupplierProfile $supplier, array $lines, string $source = 'api'): array
    {
        return DB::transaction(function () use ($supplier, $lines, $source): array {
            $jobId = DB::table('inventory_sync_jobs')->insertGetId([
                'supplier_profile_id' => $supplier->id, 'source' => $source, 'status' => 'processing',
                'payload' => json_encode($lines), 'attempts' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $updated = 0;
            foreach ($lines as $line) {
                $inventory = SupplierInventory::query()->where('supplier_profile_id', $supplier->id)
                    ->where('product_id', $line['product_id'])->lockForUpdate()->first();
                if ($inventory && isset($line['version']) && (int) $line['version'] !== (int) $inventory->version) {
                    $this->log($jobId, 'warning', 'Inventory version conflict.', ['product_id' => $line['product_id'], 'expected' => $inventory->version, 'received' => $line['version']]);
                    throw ValidationException::withMessages(['lines' => ["Inventory conflict for product {$line['product_id']}. Refresh and retry."]]);
                }
                $onHand = (int) $line['on_hand'];
                $reserved = $inventory?->reserved ?? 0;
                $inventory = SupplierInventory::query()->updateOrCreate(
                    ['supplier_profile_id' => $supplier->id, 'product_id' => $line['product_id']],
                    ['sku' => $line['sku'], 'on_hand' => $onHand, 'reserved' => $reserved, 'available' => max(0, $onHand - $reserved), 'version' => ($inventory?->version ?? 0) + 1, 'synced_at' => now()]
                );
                $updated++;
            }
            DB::table('inventory_sync_jobs')->where('id', $jobId)->update(['status' => 'completed', 'processed_at' => now(), 'updated_at' => now()]);
            $this->log($jobId, 'info', 'Inventory synchronization completed.', ['updated' => $updated]);

            return ['job_id' => $jobId, 'updated' => $updated];
        });
    }

    public function adjust(SupplierInventory $inventory, int $delta, string $reason, ?string $idempotencyKey, int $actorId): SupplierInventory
    {
        return DB::transaction(function () use ($inventory, $delta, $reason, $idempotencyKey, $actorId): SupplierInventory {
            if ($idempotencyKey && DB::table('inventory_adjustments')->where('idempotency_key', $idempotencyKey)->exists()) {
                return $inventory->fresh();
            }
            $locked = SupplierInventory::query()->lockForUpdate()->findOrFail($inventory->id);
            if ($locked->on_hand + $delta < $locked->reserved) {
                throw ValidationException::withMessages(['quantity_delta' => ['Adjustment would consume reserved inventory.']]);
            }
            DB::table('inventory_adjustments')->insert(['supplier_inventory_id' => $locked->id, 'actor_id' => $actorId, 'quantity_delta' => $delta, 'reason' => $reason, 'idempotency_key' => $idempotencyKey, 'created_at' => now(), 'updated_at' => now()]);
            $locked->update(['on_hand' => $locked->on_hand + $delta, 'available' => $locked->on_hand + $delta - $locked->reserved, 'version' => $locked->version + 1]);

            return $locked->fresh();
        });
    }

    private function log(int $jobId, string $level, string $message, array $context = []): void
    {
        DB::table('inventory_sync_logs')->insert(['inventory_sync_job_id' => $jobId, 'level' => $level, 'message' => $message, 'context' => json_encode($context), 'created_at' => now(), 'updated_at' => now()]);
    }
}
