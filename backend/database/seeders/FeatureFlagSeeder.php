<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        $flags = [
            ['key' => 'ai_stylist', 'name' => 'AI Stylist', 'description' => 'Enable AI styling experiences.', 'enabled' => true],
            ['key' => 'visual_search', 'name' => 'Visual Search', 'description' => 'Enable visual product search.', 'enabled' => true],
            ['key' => 'supplier_portal', 'name' => 'Supplier Portal', 'description' => 'Enable supplier operations portal.', 'enabled' => true],
            ['key' => 'maintenance_banner', 'name' => 'Maintenance Banner', 'description' => 'Show maintenance messaging in storefront.', 'enabled' => false],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::query()->updateOrCreate(['key' => $flag['key']], $flag);
        }
    }
}
