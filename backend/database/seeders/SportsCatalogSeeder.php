<?php

namespace Database\Seeders;

use App\Models\{Brand, Category, Color, Inventory, Product, ProductImage, ProductVariant, Size, User};
use App\Support\SportsCatalogPhotoPool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SportsCatalogSeeder extends Seeder
{
    /** @var array<string, mixed> */
    private array $catalog;

    private int $pairs = 1000;

    public function __construct()
    {
        $this->catalog = require database_path('data/GenZSportsCatalog.php');
    }

    public function run(): void
    {
        $displayTotal = number_format($this->catalog['catalog_display_total']);
        $this->command?->info("VELORA Sport — resetting legacy catalog and seeding {$this->pairs} apparel + {$this->pairs} shoes ({$displayTotal}+ SKU catalog).");
        $this->purgeLegacyCatalog();

        $colors = collect($this->catalog['colorways'])->mapWithKeys(fn (array $row) => [
            $row[0] => Color::query()->updateOrCreate(
                ['slug' => Str::slug($row[0])],
                ['name' => $row[0], 'hex_code' => $row[1], 'status' => 'active']
            ),
        ]);

        $apparelSizes = collect(['XS', 'S', 'M', 'L', 'XL', 'XXL'])->map(fn ($name, $i) => Size::query()->updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'international_size' => $name, 'sort_order' => $i, 'status' => 'active']
        ));

        $shoeSizes = collect(['US 7', 'US 8', 'US 9', 'US 10', 'US 11', 'US 12', 'US 13'])->map(fn ($name, $i) => Size::query()->updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'international_size' => $name, 'us_size' => str_replace('US ', '', $name), 'sort_order' => 100 + $i, 'status' => 'active']
        ));

        $parent = Category::query()->updateOrCreate(
            ['slug' => 'mens-sport'],
            ['name' => "Men's Sport", 'description' => 'Gen Z athletic wear + matching kicks from top sports brands.', 'status' => 'active', 'is_featured' => true, 'is_trending' => true]
        );

        $allFamilies = array_merge($this->catalog['apparel_families'], $this->catalog['footwear_families']);
        $categories = collect($allFamilies)->mapWithKeys(function (string $name, int $index) use ($parent) {
            $bannerEntry = SportsCatalogPhotoPool::entryFor($name, $index + 1, 0);
            $banner = SportsCatalogPhotoPool::urlFor($bannerEntry, 1400);

            return [
                $name => Category::query()->updateOrCreate(
                    ['slug' => 'mens-sport-'.Str::slug($name)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $name,
                        'description' => "Men's {$name} — Gen Z sport drop.",
                        'banner_image_url' => $banner,
                        'status' => 'active',
                        'is_featured' => $index < 8,
                        'is_trending' => $index < 10,
                    ]
                ),
            ];
        });

        $brands = collect($this->catalog['sport_brands'])->map(fn (string $name, int $i) => Brand::query()->updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'description' => "{$name} sportswear & footwear for Gen Z athletes.", 'country' => ['USA', 'Germany', 'UK', 'Japan', 'China'][($i) % 5], 'status' => 'active', 'is_featured' => $i < 20]
        ));

        $supplierId = User::query()->where('email', 'supplier@velora.test')->value('id');
        $apparelFamilies = $this->catalog['apparel_families'];
        $footwearFamilies = $this->catalog['footwear_families'];
        $apparelCount = count($apparelFamilies);
        $footwearCount = count($footwearFamilies);

        for ($i = 1; $i <= $this->pairs; $i++) {
            $brand = $brands[($i - 1) % $brands->count()];
            $colorKeys = $colors->keys()->values();
            $primaryColor = $colors[$colorKeys[($i - 1) % $colorKeys->count()]];
            $secondaryColor = $colors[$colorKeys[($i + 3) % $colorKeys->count()]];

            $apparelFamily = $apparelFamilies[($i - 1) % $apparelCount];
            $footwearFamily = $footwearFamilies[($i - 1) % $footwearCount];

            $shoeName = $this->productName($footwearFamily, $i, $brand->name);
            $shoe = $this->createProduct(
                sku: "VLR-S-{$i}",
                name: $shoeName,
                family: $footwearFamily,
                brand: $brand,
                category: $categories[$footwearFamily],
                supplierId: $supplierId,
                ordinal: $i,
                line: 'footwear',
                price: 79 + ($i % 40) * 3,
                color: $primaryColor,
                sizes: $shoeSizes,
                i: $i,
            );

            $apparelName = $this->productName($apparelFamily, $i, $brand->name);
            $apparel = $this->createProduct(
                sku: "VLR-A-{$i}",
                name: $apparelName,
                family: $apparelFamily,
                brand: $brand,
                category: $categories[$apparelFamily],
                supplierId: $supplierId,
                ordinal: $i,
                line: 'apparel',
                price: 35 + ($i % 30) * 4,
                color: $primaryColor,
                sizes: $apparelSizes,
                i: $i,
                matchedProductId: $shoe->id,
            );

            $shoe->update(['matched_product_id' => $apparel->id]);
        }

        $this->command?->info("Done — {$this->pairs} matched outfit + shoe pairs from {$brands->count()} top sports brands.");
    }

    private function purgeLegacyCatalog(): void
    {
        $legacyProductIds = Product::query()
            ->where(fn ($query) => $query->whereNull('catalog_line')->orWhere('gender', '!=', 'men'))
            ->pluck('id');

        if ($legacyProductIds->isNotEmpty()) {
            \Illuminate\Support\Facades\DB::table('product_images')->whereIn('product_id', $legacyProductIds)->delete();
            \Illuminate\Support\Facades\DB::table('style_recommendation_items')->whereIn('product_id', $legacyProductIds)->delete();
            Product::query()->whereIn('id', $legacyProductIds)->delete();
            $this->command?->warn('Removed '.$legacyProductIds->count().' legacy modest/women products.');
        }

        Category::query()
            ->where(function ($query) {
                $query->where('slug', 'women')
                    ->orWhere('slug', 'like', 'women-%')
                    ->orWhere('slug', 'men')
                    ->orWhere(function ($men) {
                        $men->where('slug', 'like', 'men-%')->where('slug', 'not like', 'mens-sport%');
                    });
            })
            ->update(['status' => 'inactive']);
    }

    private function productName(string $family, int $index, string $brandName): string
    {
        $adj = $this->catalog['gen_z_adjectives'][$index % count($this->catalog['gen_z_adjectives'])];
        $suffix = $this->catalog['gen_z_suffixes'][intdiv($index, count($this->catalog['gen_z_adjectives'])) % count($this->catalog['gen_z_suffixes'])];

        return trim("{$brandName} {$adj} {$family} {$suffix}");
    }

    private function createProduct(
        string $sku,
        string $name,
        string $family,
        Brand $brand,
        Category $category,
        ?int $supplierId,
        int $ordinal,
        string $line,
        float $price,
        Color $color,
        $sizes,
        int $i,
        ?int $matchedProductId = null,
    ): Product {
        $isFootwear = SportsCatalogPhotoPool::isFootwear($family);
        $matchNote = $matchedProductId
            ? ' Includes AI-matched kicks in the same colorway.'
            : ($isFootwear ? ' Pairs with matching Gen Z sport top.' : '');

        $product = Product::query()->updateOrCreate(['sku' => $sku], [
            'slug' => Str::slug($name.'-'.$sku),
            'name' => $name,
            'short_description' => "Men's {$family} — {$brand->name} Gen Z sport edit.",
            'description' => "Built for ages 16–35. {$brand->name} {$family} engineered for gym, court, and street.{$matchNote}",
            'barcode' => strtoupper(str_replace('-', '', $sku)),
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'supplier_id' => $supplierId,
            'matched_product_id' => $matchedProductId,
            'price' => $price,
            'sale_price' => $i % 5 === 0 ? round($price * 0.85, 2) : null,
            'cost_price' => round($price * 0.42, 2),
            'stock_quantity' => 20 + $i % 80,
            'minimum_stock' => 5,
            'status' => 'published',
            'is_featured' => $i <= 48,
            'is_trending' => $i % 11 === 0,
            'is_new_arrival' => $i <= 120,
            'fabric' => $isFootwear ? 'Synthetic upper' : ['Dri-FIT', 'Polyester', 'Mesh', 'Cotton blend'][$i % 4],
            'material' => $isFootwear ? 'Rubber outsole' : 'Performance polyester',
            'fit_type' => ['Athletic', 'Relaxed', 'Compression', 'Oversized'][$i % 4],
            'coverage_level' => 'Sport',
            'care_instructions' => $isFootwear ? 'Wipe clean. Air dry.' : 'Machine wash cold. Do not tumble dry.',
            'gender' => 'men',
            'catalog_line' => $line,
            'season' => ['All season', 'Summer', 'Winter', 'Indoor'][$i % 4],
            'meta_title' => "{$name} | VELORA Sport",
            'meta_description' => "{$brand->name} men's {$family} for Gen Z athletes.",
            'keywords' => "sport,genz,men,{$brand->slug},{$family},velora-sport",
            'published_at' => now()->subDays($i % 90),
        ]);

        $selectedSizes = $sizes->slice($i % 3, 3)->values();
        $product->colors()->sync([$color->id]);
        $product->sizes()->sync($selectedSizes->pluck('id'));

        foreach (range(0, 3) as $j) {
            $entry = SportsCatalogPhotoPool::entryFor($family, $ordinal, $j);
            $url = SportsCatalogPhotoPool::urlFor($entry, 1080);
            $thumb = SportsCatalogPhotoPool::urlFor($entry, 540);
            ProductImage::query()->updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => $j],
                ['url' => $url, 'thumbnail_url' => $thumb, 'alt_text' => $name, 'is_primary' => $j === 0]
            );
            $variant = ProductVariant::query()->updateOrCreate(
                ['sku' => "{$sku}-{$j}"],
                ['product_id' => $product->id, 'color_id' => $color->id, 'size_id' => $selectedSizes[$j % $selectedSizes->count()]->id, 'stock_quantity' => 8 + $i % 25, 'status' => 'active']
            );
            Inventory::query()->updateOrCreate(
                ['product_id' => $product->id, 'product_variant_id' => $variant->id, 'location' => 'primary'],
                ['current_stock' => $variant->stock_quantity, 'reserved_stock' => 0, 'minimum_stock' => 5]
            );
        }

        return $product;
    }
}
