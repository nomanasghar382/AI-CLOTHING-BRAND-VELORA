<?php

namespace App\Listeners;

use App\Events\SupplierInventorySynced;
use App\Models\AdminNotification;

class RecordSupplierInventorySync
{
    public function handle(SupplierInventorySynced $event): void
    {
        AdminNotification::query()->create(['user_id' => $event->supplier->user_id, 'type' => 'supplier.inventory_synced', 'title' => 'Inventory synchronized', 'body' => "{$event->updated} inventory records updated.", 'data' => ['supplier_id' => $event->supplier->id]]);
    }
}
