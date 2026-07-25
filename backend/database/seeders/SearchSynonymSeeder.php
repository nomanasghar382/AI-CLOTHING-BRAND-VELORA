<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchSynonymSeeder extends Seeder
{
    public function run(): void
    {
        $pairs = [
            ['abaya', 'robe'],
            ['hijab', 'scarf'],
            ['scarf', 'hijab'],
            ['modest', 'covered'],
            ['linen', 'breathable'],
            ['kaftan', 'dress'],
            ['thobe', 'abaya'],
            ['jilbab', 'abaya'],
            ['niqab', 'veil'],
            ['chador', 'cloak'],
        ];

        foreach ($pairs as [$term, $synonym]) {
            DB::table('search_synonyms')->updateOrInsert(
                ['term' => $term, 'synonym' => $synonym],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
