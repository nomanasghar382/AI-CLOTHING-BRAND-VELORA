<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class ResetSportCatalogCommand extends Command
{
    protected $signature = 'velora:reset-sport-catalog';

    protected $description = 'Remove legacy modest/women catalog data and re-seed men\'s sportswear + matching shoes.';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\SportsCatalogSeeder', '--force' => true]);
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\SearchSynonymSeeder', '--force' => true]);
        $this->info('VELORA Sport catalog reset complete.');

        return self::SUCCESS;
    }
}
