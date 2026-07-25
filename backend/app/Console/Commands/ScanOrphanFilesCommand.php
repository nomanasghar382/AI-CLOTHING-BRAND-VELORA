<?php

namespace App\Console\Commands;

use App\Services\Enterprise\FileManagementService;
use Illuminate\Console\Command;

final class ScanOrphanFilesCommand extends Command
{
    protected $signature = 'velora:scan-orphan-files';

    protected $description = 'Scan and mark orphaned file assets.';

    public function handle(FileManagementService $files): int
    {
        $count = $files->scanOrphans();
        $this->info("Marked {$count} orphaned assets.");

        return self::SUCCESS;
    }
}
