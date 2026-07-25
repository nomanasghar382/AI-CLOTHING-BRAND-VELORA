<?php

namespace App\Jobs;

use App\Models\SupplierProfile;
use App\Services\Supplier\InventorySyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSupplierInventory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public function __construct(public int $supplierId, public array $lines, public string $source = 'api') {}

    public function handle(InventorySyncService $sync): void
    {
        $sync->sync(SupplierProfile::query()->findOrFail($this->supplierId), $this->lines, $this->source);
    }
}
