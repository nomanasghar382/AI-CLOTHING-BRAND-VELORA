<?php

namespace Database\Seeders;

use App\Models\{Brand, Category, Color, Inventory, Product, ProductImage, ProductVariant, Size, User};
use App\Support\FreeCatalogPhotoPool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /** @var array<string, mixed> */
    private array $catalog;

    /** @var list<array{type: string, id?: string, path?: string}>} */
    private array $womenPool = [];

    /** @var list<array{type: string, id?: string, path?: string}>} */
    private array $menPool = [];

    public function __construct()
    {
        $this->catalog = require database_path('data/GenZCuratedCatalog.php');
        $this->womenPool = FreeCatalogPhotoPool::forGender('women');
        $this->menPool = FreeCatalogPhotoPool::forGender('men');
    }

    /** @return array{type: string, id?: string, path?: string} */
    private function entryFor(string $gender, int $ordinal, int $galleryIndex): array
    {
        if ($gender === 'men' && $galleryIndex === 0 && ($brand = FreeCatalogPhotoPool::menBrandModelPath())) {
            return ['type' => 'local', 'path' => $brand];
        }

        if ($gender === 'men' && $galleryIndex > 0 && FreeCatalogPhotoPool::usesMenBrandModel()) {
            return ['type' => 'unsplash', 'id' => FreeCatalogPhotoPool::menGarmentPhoto($ordinal, $galleryIndex)];
        }

        $pool = $gender === 'men' ? $this->menPool : $this->womenPool;
        $index = $ordinal + ($galleryIndex * 97);

        return $pool[$index % count($pool)];
    }

    private function genZProductName(string $familyName, int $productIndex, string $gender): string
    {
        $adjectives = $this->catalog['gen_z_adjectives'];
        $suffixes = $this->catalog['gen_z_suffixes'];
        $adj = $adjectives[$productIndex % count($adjectives)];
        $suffix = $suffixes[intdiv($productIndex, count($adjectives)) % count($suffixes)];
        $prefix = $gender === 'men' ? "Men's" : '';

        return trim("{$adj} {$prefix} {$familyName} {$suffix}");
    }

    public function run(): void
    {
        $counts = FreeCatalogPhotoPool::counts();
        $this->command?->info("Free photos: {$counts['women_local']} women + {$counts['men_local']} men from your phone. Pool totals: {$counts['women_total']} women, {$counts['men_total']} men.");
        if ($counts['men_brand_model']) {
            $this->command?->info('Men brand model detected — your photo will be used on ALL men\'s products.');
        } else {
            $this->command?->warn('Tip: save your photo as backend/public/free-catalog/men/brand-model.jpg for all men\'s products.');
        }

        $colors = collect(['Black'=>'#111827','Ivory'=>'#FFFFF0','Emerald'=>'#047857','Plum'=>'#7E2253','Navy'=>'#1E3A8A','Sand'=>'#D6C5A2','Rose'=>'#E9A0B5','Olive'=>'#556B2F','Taupe'=>'#8B7D6B','Cocoa'=>'#6F4E37','Sky'=>'#87CEEB','Lilac'=>'#C8A2C8','Stone'=>'#78716C','Sage'=>'#9CAF88','Burgundy'=>'#800020','Teal'=>'#0F766E','Mocha'=>'#967969','Coral'=>'#FF7F50','Silver'=>'#C0C0C0','Gold'=>'#D4AF37'])->map(fn ($hex, $name) => Color::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'hex_code'=>$hex,'status'=>'active']))->values();
        $sizes = collect(['XS','S','M','L','XL','XXL','EU 34','EU 36','EU 38','EU 40','UK 8','UK 10','US 4','US 6','One Size'])->map(fn ($name,$i) => Size::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'international_size'=>$name,'sort_order'=>$i,'status'=>'active']))->values();

        $womenFamilies = ['Niqab', 'Hijab', 'Khimar', 'Abaya', 'Jilbab', 'Burqa', 'Maxi Dress', 'Modest Top', 'Islamic Trouser', 'Wide Leg Pant', 'Modest Skirt', 'Scarf'];
        $menFamilies = ['Thobe', 'Jubba', 'Kandura', 'Shalwar Kameez', 'Kurta', 'Islamic T-Shirt', 'Modest Shirt', 'Islamic Trouser', 'Chino Pant', 'Kufi Cap', 'Waistcoat', 'Prayer Set'];

        $womenParent = Category::query()->updateOrCreate(
            ['slug' => 'women'],
            ['name' => 'Women', 'description' => 'Gen Z modest edits — niqab, hijab, abaya, jilbab, trousers & more.', 'status' => 'active', 'is_featured' => true, 'is_trending' => true]
        );

        $menParent = Category::query()->updateOrCreate(
            ['slug' => 'men'],
            ['name' => 'Men', 'description' => 'Gen Z Islamic streetwear — thobe, kurta, shalwar kameez, jubba & more.', 'status' => 'active', 'is_featured' => true, 'is_trending' => true]
        );

        $womenCategories = collect($womenFamilies)->values()->map(function ($name, $index) use ($womenParent) {
            return Category::query()->updateOrCreate(
                ['slug' => 'women-'.Str::slug($name)],
                ['parent_id' => $womenParent->id, 'name' => $name, 'description' => "Women's {$name} — full coverage, Gen Z fits.", 'status' => 'active', 'is_featured' => $index < 6, 'is_trending' => $index < 8]
            );
        });

        $menCategories = collect($menFamilies)->values()->map(function ($name, $index) use ($menParent) {
            return Category::query()->updateOrCreate(
                ['slug' => 'men-'.Str::slug($name)],
                ['parent_id' => $menParent->id, 'name' => $name, 'description' => "Men's {$name} — modest, modern, everyday.", 'status' => 'active', 'is_featured' => $index < 6, 'is_trending' => $index < 8]
            );
        });

        $genZBrands = ['Velora Noor', 'Haya Edit', 'Noor Street', 'Sakinah Studio', 'Barakah Wear', 'Ummah Edit', 'Modest Core', 'Khimar Co', 'Thobe Lab', 'Kurta Club', 'Niqab Noir', 'Jubba House'];
        $brands = collect(range(1, 100))->map(function ($i) use ($genZBrands) {
            $label = $i <= 12 ? $genZBrands[$i - 1] : 'Velora Studio '.$i;

            return Brand::query()->updateOrCreate(['slug' => 'velora-studio-'.$i], ['name' => $label, 'description' => 'Gen Z modest fashion — full coverage, premium fabrics, everyday fits.', 'country' => ['Pakistan', 'UAE', 'Turkey', 'UK', 'Indonesia'][($i - 1) % 5], 'status' => 'active', 'is_featured' => $i <= 12]);
        });

        $supplierId = User::query()->where('email', 'supplier@velora.test')->value('id');
        $familyCount = count($womenFamilies);

        foreach (range(1, 1000) as $i) {
            $isMen = $i % 2 === 0;
            $gender = $isMen ? 'men' : 'women';
            $ordinal = intdiv($i - 1, 2);
            $familyIndex = $ordinal % $familyCount;
            $familyName = $isMen ? $menFamilies[$familyIndex] : $womenFamilies[$familyIndex];
            $name = $this->genZProductName($familyName, $i, $gender);
            $price = 35 + ($i % 25) * 6;
            $categoryPool = $isMen ? $menCategories : $womenCategories;
            $coverage = $gender === 'women' ? 'Full' : ['Full', 'Modest', 'Layered'][$i % 3];

            $modeledBy = $gender === 'men' && FreeCatalogPhotoPool::usesMenBrandModel() ? ' Modeled by Noman Asghar.' : '';
            $product = Product::query()->updateOrCreate(['sku' => "VLR-{$i}"], [
                'slug' => Str::slug($name.'-'.$i),
                'name' => $name,
                'short_description' => $gender === 'men' && FreeCatalogPhotoPool::usesMenBrandModel()
                    ? "Worn by Noman Asghar — {$familyName} edit."
                    : '100% free photos — your pics or Unsplash stock.',
                'description' => "Built for ages 16–35. The {$name} is styled for campus, Jummah, and weekend fits.{$modeledBy}",
                'barcode' => '890'.str_pad((string) $i, 9, '0', STR_PAD_LEFT),
                'brand_id' => $brands[$i % 100]->id,
                'category_id' => $categoryPool[$familyIndex]->id,
                'supplier_id' => $supplierId,
                'price' => $price,
                'sale_price' => $i % 4 === 0 ? $price * .82 : null,
                'cost_price' => $price * .4,
                'stock_quantity' => 10 + $i % 75,
                'minimum_stock' => 5,
                'status' => 'published',
                'is_featured' => $i <= 24,
                'is_trending' => $i % 7 === 0,
                'is_new_arrival' => $i <= 48,
                'fabric' => ['Cotton', 'Jersey', 'Linen', 'Satin', 'Viscose', 'Crepe'][$i % 6],
                'material' => ['Organic Cotton', 'Premium Viscose', 'Soft Crepe', 'Woven Linen'][$i % 4],
                'fit_type' => ['Relaxed', 'Tailored', 'Oversized'][$i % 3],
                'coverage_level' => $coverage,
                'care_instructions' => 'Machine wash cold. Dry flat. Warm iron if needed.',
                'gender' => $gender,
                'season' => ['All season', 'Summer', 'Winter', 'Eid edit'][$i % 4],
                'meta_title' => "{$name} | Velora",
                'meta_description' => "Gen Z {$familyName} — modest fashion for ages 16–35.",
                'keywords' => "free-photos, {$familyName}, modest-fashion, velora",
                'published_at' => now()->subDays($i % 120),
            ]);

            $selectedColors = $colors->slice($i % 10, 2)->values();
            $selectedSizes = $sizes->slice($i % 10, 3)->values();
            $product->colors()->sync($selectedColors->pluck('id'));
            $product->sizes()->sync($selectedSizes->pluck('id'));

            foreach (range(0, 4) as $j) {
                $entry = $this->entryFor($gender, $ordinal, $j);
                $variant = $entry['type'] === 'local' ? 0 : $j;
                $url = FreeCatalogPhotoPool::urlFor($entry, 1080, $variant);
                $thumbnailUrl = FreeCatalogPhotoPool::urlFor($entry, 540, $variant);
                ProductImage::query()->updateOrCreate(
                    ['product_id' => $product->id, 'sort_order' => $j],
                    [
                        'url' => $url,
                        'thumbnail_url' => $thumbnailUrl,
                        'alt_text' => $name,
                        'is_primary' => $j === 0,
                    ]
                );
                $productVariant = ProductVariant::query()->updateOrCreate(['sku' => "VLR-{$i}-{$j}"], ['product_id' => $product->id, 'color_id' => $selectedColors[$j % 2]->id, 'size_id' => $selectedSizes[$j % 3]->id, 'stock_quantity' => 5 + $i % 30, 'status' => 'active']);
                Inventory::query()->updateOrCreate(['product_id' => $product->id, 'product_variant_id' => $productVariant->id, 'location' => 'primary'], ['current_stock' => $productVariant->stock_quantity, 'reserved_stock' => 0, 'minimum_stock' => 5]);
            }
        }
    }
}
