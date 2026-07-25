<?php

namespace Database\Seeders;

use App\Models\{Brand, Category, Color, Inventory, Product, ProductImage, ProductVariant, Size, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /** @var array{crop_variants: list<string>, women: list<string>, men: list<string>, gen_z_adjectives: list<string>, gen_z_suffixes: list<string>} */
    private array $library;

    /** @var list<array{photo_id: string, variant: int}> */
    private array $womenImageSlots = [];

    /** @var list<array{photo_id: string, variant: int}> */
    private array $menImageSlots = [];

    public function __construct()
    {
        $this->library = require database_path('data/ModestFashionImageLibrary.php');
        $this->womenImageSlots = $this->buildImageSlots($this->library['women']);
        $this->menImageSlots = $this->buildImageSlots($this->library['men']);
    }

    /** @param list<string> $photoIds */
    private function buildImageSlots(array $photoIds): array
    {
        $slots = [];

        foreach ($photoIds as $photoId) {
            foreach (array_keys($this->library['crop_variants']) as $variant) {
                $slots[] = ['photo_id' => $photoId, 'variant' => $variant];
            }
        }

        return $slots;
    }

    private static function imageUrl(string $photoId, int $width, int $variant = 0): string
    {
        $quality = $width <= 480 ? 82 : 85;

        if (str_starts_with($photoId, 'pexels:')) {
            $id = substr($photoId, 7);

            return "https://images.pexels.com/photos/{$id}/pexels-photo-{$id}.jpeg?auto=compress&fit=crop&w={$width}&q={$quality}&dpr=2";
        }

        $host = str_starts_with($photoId, 'premium_photo') ? 'plus.unsplash.com' : 'images.unsplash.com';
        $library = require database_path('data/ModestFashionImageLibrary.php');
        $crop = $library['crop_variants'][$variant] ?? '';

        return "https://{$host}/{$photoId}?auto=format&fit=crop&w={$width}&q={$quality}&dpr=2{$crop}";
    }

    /** @param array{photo_id: string, variant: int} $slot */
    private static function urlFromSlot(array $slot, int $width): string
    {
        return self::imageUrl($slot['photo_id'], $width, $slot['variant']);
    }

    /** @return array{photo_id: string, variant: int} */
    private function slotForProduct(int $productIndex, string $gender, int $offset = 0): array
    {
        $slots = $gender === 'men' ? $this->menImageSlots : $this->womenImageSlots;
        $ordinal = intdiv($productIndex - 1, 2) + $offset;

        return $slots[$ordinal % count($slots)];
    }

    private function genZProductName(string $familyName, int $productIndex, string $gender): string
    {
        $adjectives = $this->library['gen_z_adjectives'];
        $suffixes = $this->library['gen_z_suffixes'];
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
            $familyIndex = intdiv($i - 1, 2) % $familyCount;
            $familyName = $isMen ? $menFamilies[$familyIndex] : $womenFamilies[$familyIndex];
            $name = $this->genZProductName($familyName, $i, $gender);
            $price = 35 + ($i % 25) * 6;
            $categoryPool = $isMen ? $menCategories : $womenCategories;
            $coverage = $gender === 'women' ? 'Full' : ['Full', 'Modest', 'Layered'][$i % 3];
            $tagline = $gender === 'women'
                ? 'Full-coverage fit made for Gen Z modest wardrobes.'
                : 'Clean modest silhouette for school, Eid, and everyday.';

            $product = Product::query()->updateOrCreate(['sku' => "VLR-{$i}"], [
                'slug' => Str::slug($name.'-'.$i),
                'name' => $name,
                'short_description' => $tagline,
                'description' => "Built for ages 16–35 who want modest Islamic style that still feels current. The {$name} uses premium fabric, relaxed tailoring, and easy layering for campus, Jummah, and weekend fits.",
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
                'published_at' => now()->subDays($i % 120),
            ]);

            $selectedColors = $colors->slice($i % 10, 2)->values();
            $selectedSizes = $sizes->slice($i % 10, 3)->values();
            $product->colors()->sync($selectedColors->pluck('id'));
            $product->sizes()->sync($selectedSizes->pluck('id'));

            foreach (range(0, 4) as $j) {
                $slot = $this->slotForProduct($i, $gender, $j * 17);
                $url = self::urlFromSlot($slot, 1080);
                $thumbnailUrl = self::urlFromSlot($slot, 540);
                ProductImage::query()->updateOrCreate(['product_id' => $product->id, 'sort_order' => $j], ['url' => $url, 'thumbnail_url' => $thumbnailUrl, 'alt_text' => $name, 'is_primary' => $j === 0]);
                $variant = ProductVariant::query()->updateOrCreate(['sku' => "VLR-{$i}-{$j}"], ['product_id' => $product->id, 'color_id' => $selectedColors[$j % 2]->id, 'size_id' => $selectedSizes[$j % 3]->id, 'stock_quantity' => 5 + $i % 30, 'status' => 'active']);
                Inventory::query()->updateOrCreate(['product_id' => $product->id, 'product_variant_id' => $variant->id, 'location' => 'primary'], ['current_stock' => $variant->stock_quantity, 'reserved_stock' => 0, 'minimum_stock' => 5]);
            }
        }
    }
}
