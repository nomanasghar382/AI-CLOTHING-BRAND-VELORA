<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class FashionTrendSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['slug' => 'tailored-utility', 'title' => 'Tailored Utility', 'description' => 'Structured layers and practical details for everyday dressing.', 'tags' => ['tailoring', 'utility', 'layers'], 'season' => 'all'],
            ['slug' => 'soft-neutrals', 'title' => 'Soft Neutrals', 'description' => 'Warm, versatile neutral tones that make outfit building easier.', 'tags' => ['neutral', 'minimal'], 'season' => 'all'],
            ['slug' => 'textured-layers', 'title' => 'Textured Layers', 'description' => 'Mix knits, woven fabrics, and light outerwear for visual depth.', 'tags' => ['texture', 'layering'], 'season' => 'autumn'],
        ] as $trend) {
            DB::table('fashion_trends')->updateOrInsert(['slug' => $trend['slug']], array_merge($trend, ['tags' => json_encode($trend['tags']), 'confidence' => 0.80, 'source' => 'editorial', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}
