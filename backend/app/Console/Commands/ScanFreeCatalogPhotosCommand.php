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
            ['Men brand model (all men\'s products)', '—', $counts['men_brand_model'] ? 'YES' : 'missing'],
        ]);

        $this->newLine();
        $this->line('Use YOUR photo on ALL men\'s clothing (free):');
        $this->line('  1. Save your photo as: backend/public/free-catalog/men/brand-model.jpg');
        $this->line('  2. Run: php artisan db:seed --class=CatalogSeeder');
        $this->line('Every men\'s product will show you wearing that look + garment detail shots.');

        return self::SUCCESS;
    }
}
