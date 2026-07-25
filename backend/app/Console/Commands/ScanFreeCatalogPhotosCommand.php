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
            ['Your uploaded photos', $counts['women_local'], $counts['men_local']],
            ['Total pool', $counts['women_total'], $counts['men_total']],
            ['Brand model active', $counts['women_catalog_photos'] ? 'YOUR LOOKS' : 'add photos', $counts['men_brand_model'] ? 'YES' : 'add photo'],
        ]);

        $this->newLine();
        $this->line('WOMEN: copy your niqab/abaya images to backend/public/free-catalog/women/');
        $this->line('MEN: save your portrait as backend/public/free-catalog/men/brand-model.jpg');
        $this->line('Then: php artisan db:seed --class=CatalogSeeder');

        return self::SUCCESS;
    }
}
