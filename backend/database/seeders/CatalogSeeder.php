<?php

namespace Database\Seeders;

use App\Models\{Brand, Category, Color, Inventory, Product, ProductImage, ProductVariant, Size, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /** @var array<string, mixed> */
    private array $catalog;

    public function __construct()
    {
        $this->catalog = require database_path('data/GenZCuratedCatalog.php');
    }

    private static function imageUrl(string $photoId, int $width, int $variant = 0): string
    {
        $quality = $width <= 480 ? 82 : 85;
        $host = str_starts_with($photoId, 'premium_photo') ? 'plus.unsplash.com' : 'images.unsplash.com';
        $library = require database_path('data/GenZCuratedCatalog.php');
        $crop = $library['crop_variants'][$variant] ?? '';

        return "https://{$host}/{$photoId}?auto=format&fit=crop&w={$width}&h=".($width <= 540 ? 900 : 1350)."&q={$quality}&dpr=2{$crop}";
    }

  /** @return array{photo: string, unique_face: bool} */
    private function assignPhoto(string $gender, int $ordinal, int $galleryIndex): array
    {
        $editorial = $gender === 'men' ? $this->catalog['men_editorial'] : $this->catalog['women_editorial'];
        $product = $gender === 'men' ? $this->catalog['men_product'] : $this->catalog['women_product'];

        if ($galleryIndex === 0) {
            if ($ordinal < count($editorial)) {
                return ['photo' => $editorial[$ordinal], 'unique_face' => true];
            }

            return ['photo' => $product[($ordinal - count($editorial)) % count($product)], 'unique_face' => false];
        }

        $base = $ordinal < count($editorial) ? $editorial[$ordinal] : $product[($ordinal - count($editorial)) % count($product)];

        return ['photo' => $base, 'unique_face' => $ordinal < count($editorial)];
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

            $product = Product::query()->updateOrCreate(['sku' => "VLR-{$i}"], [
                'slug' => Str::slug($name.'-'.$i),
                'name' => $name,
                'short_description' => 'Gen Z modest fit — young editorial campaign styling.',
                'description' => "Built for ages 16–35. The {$name} is styled like a modern modest fashion drop — premium fabric, relaxed tailoring, and coverage made for campus, Jummah, and weekend edits.",
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
                'keywords' => "gen-z, {$familyName}, modest-fashion, velora",
                'published_at' => now()->subDays($i % 120),
            ]);

            $selectedColors = $colors->slice($i % 10, 2)->values();
            $selectedSizes = $sizes->slice($i % 10, 3)->values();
            $product->colors()->sync($selectedColors->pluck('id'));
            $product->sizes()->sync($selectedSizes->pluck('id'));

            foreach (range(0, 4) as $j) {
                $assignment = $this->assignPhoto($gender, $ordinal, $j === 0 ? 0 : 1);
                $variant = $j === 0 ? 0 : $j - 1;
                $url = self::imageUrl($assignment['photo'], 1080, $variant);
                $thumbnailUrl = self::imageUrl($assignment['photo'], 540, $variant);
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
