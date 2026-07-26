<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchSynonymSeeder extends Seeder
{
    public function run(): void
    {
        $pairs = [
            ['sneakers', 'trainers'],
            ['trainers', 'sneakers'],
            ['kicks', 'sneakers'],
            ['joggers', 'track pants'],
            ['track pants', 'joggers'],
            ['hoodie', 'sweatshirt'],
            ['sweatshirt', 'hoodie'],
            ['gym', 'training'],
            ['training', 'gym'],
            ['run', 'running'],
            ['running', 'run'],
            ['basketball', 'court'],
            ['football', 'soccer'],
            ['soccer', 'football'],
            ['dri-fit', 'moisture wicking'],
            ['compression', 'base layer'],
        ];

        foreach ($pairs as [$term, $synonym]) {
            DB::table('search_synonyms')->updateOrInsert(
                ['term' => $term, 'synonym' => $synonym],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
