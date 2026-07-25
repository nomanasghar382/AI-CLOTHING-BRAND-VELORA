<?php

namespace App\Console\Commands;

use App\Support\FreeCatalogPhotoPool;
use Illuminate\Console\Command;

class ScanFreeCatalogPhotosCommand extends Command
{
    protected $signature = 'catalog:free-photos';

    protected $description = 'Show how many free catalog photos are available (your phone pics + Unsplash)';

    public function handle(): int
    {
        $counts = FreeCatalogPhotoPool::counts();

        $this->info('Free catalog photo pool');
        $this->table(['Source', 'Women', 'Men'], [
            ['Your phone photos (public/free-catalog/)', $counts['women_local'], $counts['men_local']],
            ['Total pool (local + free Unsplash)', $counts['women_total'], $counts['men_total']],
        ]);

        $this->newLine();
        $this->line('Add FREE photos from your phone:');
        $this->line('  1. Copy JPG/PNG into backend/public/free-catalog/women/ or .../men/');
        $this->line('  2. Run: php artisan db:seed --class=CatalogSeeder');
        $this->line('Your local photos are used FIRST — before any stock images.');

        return self::SUCCESS;
    }
}
