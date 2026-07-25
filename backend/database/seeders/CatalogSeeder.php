<?php

namespace Database\Seeders;

use App\Models\{Brand, Category, Color, Inventory, Product, ProductImage, ProductVariant, Size, User};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /** Face-covered only: niqab and mannequin product shots (no hijab portraits). */
    private const WOMEN_PHOTO_IDS = [
        'photo-1559730775-67f621597262',
        'photo-1744727811425-e1c0af8b4022',
        'photo-1618297655311-ab851e7045d6',
        'photo-1771162766051-c330f1d664ea',
    ];

    private const MEN_PHOTO_IDS = [
        'photo-1564289851149-a9f8940f795c',
        'photo-1578507435314-e39e7852eddd',
        'photo-1756412066323-a336d2becc10',
        'photo-1761475048588-e00acbdce66f',
        'photo-1774424420923-6936309c3c5e',
        'photo-1757143137159-316220046829',
    ];

    private static function imageUrl(string $photoId, int $width): string
    {
        $quality = $width <= 320 ? 65 : 72;

        return "https://images.unsplash.com/{$photoId}?auto=format&fit=crop&w={$width}&q={$quality}";
    }

    public function run(): void
    {
        $colors = collect(['Black'=>'#111827','Ivory'=>'#FFFFF0','Emerald'=>'#047857','Plum'=>'#7E2253','Navy'=>'#1E3A8A','Sand'=>'#D6C5A2','Rose'=>'#E9A0B5','Olive'=>'#556B2F','Taupe'=>'#8B7D6B','Cocoa'=>'#6F4E37','Sky'=>'#87CEEB','Lilac'=>'#C8A2C8','Stone'=>'#78716C','Sage'=>'#9CAF88','Burgundy'=>'#800020','Teal'=>'#0F766E','Mocha'=>'#967969','Coral'=>'#FF7F50','Silver'=>'#C0C0C0','Gold'=>'#D4AF37'])->map(fn ($hex, $name) => Color::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'hex_code'=>$hex,'status'=>'active']))->values();
        $sizes = collect(['XS','S','M','L','XL','XXL','EU 34','EU 36','EU 38','EU 40','UK 8','UK 10','US 4','US 6','One Size'])->map(fn ($name,$i) => Size::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'international_size'=>$name,'sort_order'=>$i,'status'=>'active']))->values();

        $womenFamilies = ['Abaya', 'Niqab', 'Burqa', 'Hijab Set', 'Khimar', 'Jilbab', 'Maxi Dress', 'Modest Top', 'Wide Leg Pant', 'Scarf'];
        $menFamilies = ['Thobe', 'Kandura', 'Shalwar Kameez', 'Kurta', 'Jubba', 'Modest Shirt', 'Sirwal', 'Kufi Cap', 'Prayer Set', 'Waistcoat'];

        $womenParent = Category::query()->updateOrCreate(
            ['slug' => 'women'],
            ['name' => 'Women', 'description' => 'Islamic modest fashion for women — abayas, niqabs, hijabs, and more.', 'status' => 'active', 'is_featured' => true, 'is_trending' => true]
        );

        $menParent = Category::query()->updateOrCreate(
            ['slug' => 'men'],
            ['name' => 'Men', 'description' => 'Islamic modest fashion for men — thobes, kanduras, shalwar kameez, and more.', 'status' => 'active', 'is_featured' => true, 'is_trending' => true]
        );

        $womenCategories = collect($womenFamilies)->values()->map(function ($name, $index) use ($womenParent) {
            return Category::query()->updateOrCreate(
                ['slug' => 'women-'.Str::slug($name)],
                ['parent_id' => $womenParent->id, 'name' => $name, 'description' => "Women's {$name} collection.", 'status' => 'active', 'is_featured' => $index < 4, 'is_trending' => $index < 6]
            );
        });

        $menCategories = collect($menFamilies)->values()->map(function ($name, $index) use ($menParent) {
            return Category::query()->updateOrCreate(
                ['slug' => 'men-'.Str::slug($name)],
                ['parent_id' => $menParent->id, 'name' => $name, 'description' => "Men's {$name} collection.", 'status' => 'active', 'is_featured' => $index < 4, 'is_trending' => $index < 6]
            );
        });

        $brands = collect(range(1, 100))->map(fn ($i) => Brand::query()->updateOrCreate(['slug' => "velora-studio-{$i}"], ['name' => "Velora Studio {$i}", 'description' => 'Islamic modest fashion for men and women.', 'country' => ['Pakistan', 'UAE', 'Turkey', 'UK', 'Indonesia'][($i - 1) % 5], 'status' => 'active', 'is_featured' => $i <= 12]));
        $supplierId = User::query()->where('email', 'supplier@velora.test')->value('id');

        foreach (range(1, 1000) as $i) {
            $isMen = $i % 2 === 0;
            $gender = $isMen ? 'men' : 'women';
            $familyIndex = intdiv($i - 1, 2) % 10;
            $familyName = $isMen ? $menFamilies[$familyIndex] : $womenFamilies[$familyIndex];
            $prefix = $isMen ? "Men's {$familyName}" : $familyName;
            $name = "Velora {$prefix} {$i}";
            $price = 40 + ($i % 20) * 8;
            $photoIds = $isMen ? self::MEN_PHOTO_IDS : self::WOMEN_PHOTO_IDS;
            $categoryPool = $isMen ? $menCategories : $womenCategories;
            $coverage = $gender === 'women' ? 'Full' : ['Full', 'Modest', 'Layered'][$i % 3];

            $product = Product::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'short_description' => "A refined {$prefix} for every day.",
                'description' => "Made for comfort and confidence, this {$prefix} offers complete modest coverage with contemporary Islamic styling.",
                'sku' => "VLR-{$i}",
                'barcode' => '890'.str_pad((string) $i, 9, '0', STR_PAD_LEFT),
                'brand_id' => $brands[$i % 100]->id,
                'category_id' => $categoryPool[$familyIndex]->id,
                'supplier_id' => $supplierId,
                'price' => $price,
                'sale_price' => $i % 4 === 0 ? $price * .8 : null,
                'cost_price' => $price * .4,
                'stock_quantity' => 10 + $i % 75,
                'minimum_stock' => 5,
                'status' => 'published',
                'is_featured' => $i <= 24,
                'is_trending' => $i % 9 === 0,
                'is_new_arrival' => $i <= 48,
                'fabric' => ['Linen', 'Cotton', 'Jersey', 'Satin', 'Viscose'][$i % 5],
                'material' => ['Organic Cotton', 'Premium Viscose', 'Woven Linen', 'Soft Crepe'][$i % 4],
                'fit_type' => ['Relaxed', 'Tailored', 'Flowing'][$i % 3],
                'coverage_level' => $coverage,
                'care_instructions' => 'Machine wash cold. Dry flat. Warm iron if needed.',
                'gender' => $gender,
                'season' => ['Spring', 'Summer', 'Autumn', 'Winter'][$i % 4],
                'published_at' => now()->subDays($i % 120),
            ]);

            $selectedColors = $colors->slice($i % 10, 2)->values();
            $selectedSizes = $sizes->slice($i % 10, 3)->values();
            $product->colors()->sync($selectedColors->pluck('id'));
            $product->sizes()->sync($selectedSizes->pluck('id'));

            foreach (range(0, 4) as $j) {
                $photoId = $photoIds[($i + $j) % count($photoIds)];
                $url = self::imageUrl($photoId, 720);
                $thumbnailUrl = self::imageUrl($photoId, 320);
                ProductImage::query()->updateOrCreate(['product_id' => $product->id, 'sort_order' => $j], ['url' => $url, 'thumbnail_url' => $thumbnailUrl, 'alt_text' => $name, 'is_primary' => $j === 0]);
                $variant = ProductVariant::query()->updateOrCreate(['sku' => "VLR-{$i}-{$j}"], ['product_id' => $product->id, 'color_id' => $selectedColors[$j % 2]->id, 'size_id' => $selectedSizes[$j % 3]->id, 'stock_quantity' => 5 + $i % 30, 'status' => 'active']);
                Inventory::query()->updateOrCreate(['product_id' => $product->id, 'product_variant_id' => $variant->id, 'location' => 'primary'], ['current_stock' => $variant->stock_quantity, 'reserved_stock' => 0, 'minimum_stock' => 5]);
            }
        }
    }
}
