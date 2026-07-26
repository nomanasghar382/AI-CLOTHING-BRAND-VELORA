<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use App\Support\NikeCatalogMapper;
use App\Support\SportsCatalogPhotoPool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NikeCatalogSeeder extends Seeder
{
    private string $dataPath;

    public function __construct()
    {
        $this->dataPath = database_path('data/nike_catalog.json');
    }

    public function run(): void
    {
        if (! is_file($this->dataPath)) {
            $this->command?->warn('Nike catalog data not found at database/data/nike_catalog.json — skipping.');

            return;
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = json_decode((string) file_get_contents($this->dataPath), true, 512, JSON_THROW_ON_ERROR);

        $this->command?->info('Importing '.count($rows).' Nike catalog products from dataset…');

        $parent = Category::query()->firstOrCreate(
            ['slug' => 'mens-sport'],
            ['name' => "Men's Sport", 'description' => 'Training fits and sneakers from top sports brands.', 'status' => 'active', 'is_featured' => true]
        );

        $supplierId = User::query()->where('email', 'supplier@velora.test')->value('id');
        $imported = 0;

        foreach ($rows as $index => $row) {
            $subtitle = (string) ($row['sub_title'] ?? '');
            $name = trim((string) ($row['name'] ?? 'Nike Product'));
            $fullName = $subtitle !== '' ? "{$name} — {$subtitle}" : $name;
            $gender = NikeCatalogMapper::genderFromSubtitle($subtitle);
            $line = NikeCatalogMapper::catalogLineFromSubtitle($subtitle, $name);
            $family = NikeCatalogMapper::familyFromSubtitle($subtitle, $name);
            $slug = NikeCatalogMapper::slugFromUrl((string) ($row['url'] ?? ''));
            $uniqId = (string) ($row['uniq_id'] ?? Str::uuid());
            $sku = NikeCatalogMapper::skuFromUniqId($uniqId);
            $colorName = (string) ($row['color'] ?? 'Black');
            $description = NikeCatalogMapper::formatDescription((string) ($row['description'] ?? ''));
            $price = (float) ($row['price'] ?? 0);
            $availability = strtolower((string) ($row['availability'] ?? 'instock'));
            $inStock = $availability === 'instock' || $availability === '';
            $rating = isset($row['avg_rating']) && $row['avg_rating'] !== '' ? (float) $row['avg_rating'] : null;
            $reviewCount = isset($row['review_count']) && $row['review_count'] !== '' ? (int) $row['review_count'] : 0;

            $brand = Brand::query()->updateOrCreate(
                ['slug' => 'nike'],
                ['name' => 'Nike', 'description' => 'Just Do It — training, sport, and street.', 'country' => 'USA', 'status' => 'active', 'is_featured' => true]
            );

            $color = Color::query()->updateOrCreate(
                ['slug' => NikeCatalogMapper::colorSlug($colorName)],
                ['name' => $colorName, 'hex_code' => '#111827', 'status' => 'active']
            );

            $category = Category::query()->updateOrCreate(
                ['slug' => 'mens-sport-'.Str::slug($family)],
                [
                    'parent_id' => $parent->id,
                    'name' => $family,
                    'description' => "Nike {$family} — men's sport.",
                    'status' => 'active',
                    'is_featured' => $index < 12,
                ]
            );

            $product = Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'slug' => $slug.'-'.$sku,
                    'name' => $fullName,
                    'short_description' => NikeCatalogMapper::shortDescription($description, $fullName),
                    'description' => $description !== '' ? $description : "{$fullName}. Official-style Nike sport product.",
                    'barcode' => strtoupper(str_replace('-', '', $sku)),
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'supplier_id' => $supplierId,
                    'price' => max($price, 9.99),
                    'sale_price' => null,
                    'cost_price' => round(max($price, 9.99) * 0.45, 2),
                    'stock_quantity' => $inStock ? 24 : 0,
                    'minimum_stock' => 3,
                    'status' => 'published',
                    'is_featured' => $index < 24,
                    'is_trending' => $rating !== null && $rating >= 4.5,
                    'is_new_arrival' => $index < 36,
                    'fabric' => $line === 'footwear' ? 'Synthetic upper' : 'Performance polyester',
                    'material' => $line === 'footwear' ? 'Rubber outsole' : 'Dri-FIT blend',
                    'fit_type' => 'Standard',
                    'coverage_level' => 'Sport',
                    'care_instructions' => $line === 'footwear' ? 'Wipe clean. Air dry.' : 'Machine wash cold.',
                    'gender' => $gender,
                    'catalog_line' => $line,
                    'season' => 'All season',
                    'meta_title' => "{$fullName} | VELORA",
                    'meta_description' => NikeCatalogMapper::shortDescription($description, $fullName),
                    'keywords' => 'nike,sport,'.$gender.','.Str::slug($family),
                    'published_at' => now()->subDays($index % 60),
                    'views_count' => 100 + ($index * 17),
                    'sales_count' => $reviewCount > 0 ? $reviewCount * 3 : ($index % 40),
                ]
            );

            $sizeNames = NikeCatalogMapper::parseSizes((string) ($row['available_sizes'] ?? ''));
            if ($sizeNames === []) {
                $sizeNames = $line === 'footwear'
                  ? ['US 8', 'US 9', 'US 10', 'US 11', 'US 12']
                  : ['S', 'M', 'L', 'XL'];
            }

            $sizeIds = collect($sizeNames)->map(function (string $sizeName, int $sort) {
                return Size::query()->updateOrCreate(
                    ['slug' => Str::slug($sizeName)],
                    ['name' => $sizeName, 'international_size' => $sizeName, 'sort_order' => $sort, 'status' => 'active']
                )->id;
            });

            $product->colors()->sync([$color->id]);
            $product->sizes()->sync($sizeIds->all());

            ProductImage::query()->where('product_id', $product->id)->delete();
            ProductVariant::query()->where('product_id', $product->id)->delete();

            $imageCount = min(4, max(1, (int) ($row['image_count'] ?? 3)));
            for ($j = 0; $j < $imageCount; $j++) {
                $entry = SportsCatalogPhotoPool::entryFor($family, $index + 1, $j);
                $url = SportsCatalogPhotoPool::urlFor($entry, 1080);
                $thumb = SportsCatalogPhotoPool::urlFor($entry, 540);

                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'url' => $url,
                    'thumbnail_url' => $thumb,
                    'alt_text' => "{$fullName} — view ".($j + 1),
                    'sort_order' => $j,
                    'is_primary' => $j === 0,
                ]);
            }

            foreach ($sizeNames as $j => $sizeName) {
                $variantSku = "{$sku}-".Str::slug($sizeName);
                $variant = ProductVariant::query()->updateOrCreate(
                    ['sku' => $variantSku],
                    [
                        'product_id' => $product->id,
                        'color_id' => $color->id,
                        'size_id' => $sizeIds[$j],
                        'stock_quantity' => $inStock ? 12 : 0,
                        'status' => $inStock ? 'active' : 'inactive',
                    ]
                );

                Inventory::query()->updateOrCreate(
                    ['product_id' => $product->id, 'product_variant_id' => $variant->id, 'location' => 'primary'],
                    ['current_stock' => $variant->stock_quantity, 'reserved_stock' => 0, 'minimum_stock' => 2]
                );
            }

            $imported++;
        }

        $this->command?->info("Nike dataset import complete — {$imported} products with real names, prices, and descriptions.");
    }
}
