<?php

namespace App\Console\Commands;

use Database\Seeders\NikeCatalogSeeder;
use Illuminate\Console\Command;

final class ResetSportCatalogCommand extends Command
{
    protected $signature = 'velora:reset-sport-catalog';

    protected $description = 'Remove legacy modest/women catalog data and re-seed men\'s sportswear + matching shoes.';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\SportsCatalogSeeder', '--force' => true]);

        if (class_exists(NikeCatalogSeeder::class)) {
            $this->call('db:seed', ['--class' => NikeCatalogSeeder::class, '--force' => true]);
        } else {
            $this->warn('NikeCatalogSeeder not found — run git pull, then: composer dump-autoload');
        }

        $this->call('db:seed', ['--class' => 'Database\\Seeders\\SearchSynonymSeeder', '--force' => true]);
        $this->info('VELORA Sport catalog reset complete (synthetic + Nike dataset).');

        return self::SUCCESS;
    }
}
