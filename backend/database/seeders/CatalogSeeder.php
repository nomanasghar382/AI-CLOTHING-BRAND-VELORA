<?php

namespace Database\Seeders;

use App\Models\{Brand, Category, Color, Inventory, Product, ProductImage, ProductVariant, Size, User};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /** @var list<string> */
    private const WOMEN_PHOTO_IDS = [
        'photo-1559730775-67f621597262',
        'photo-1771162766051-c330f1d664ea',
        'photo-1770367358711-b42cf1a6c2b1',
        'photo-1588594509615-62de3570d696',
        'photo-1750190321796-c749877df841',
        'photo-1767766277273-a53443ab8639',
        'photo-1752794674886-fb12817a5e96',
        'photo-1560350530-a12ec1414cf5',
    ];

    /** @var list<string> */
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

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = collect(['Black'=>'#111827','Ivory'=>'#FFFFF0','Emerald'=>'#047857','Plum'=>'#7E2253','Navy'=>'#1E3A8A','Sand'=>'#D6C5A2','Rose'=>'#E9A0B5','Olive'=>'#556B2F','Taupe'=>'#8B7D6B','Cocoa'=>'#6F4E37','Sky'=>'#87CEEB','Lilac'=>'#C8A2C8','Stone'=>'#78716C','Sage'=>'#9CAF88','Burgundy'=>'#800020','Teal'=>'#0F766E','Mocha'=>'#967969','Coral'=>'#FF7F50','Silver'=>'#C0C0C0','Gold'=>'#D4AF37'])->map(fn ($hex, $name) => Color::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'hex_code'=>$hex,'status'=>'active']))->values();
        $sizes = collect(['XS','S','M','L','XL','XXL','EU 34','EU 36','EU 38','EU 40','UK 8','UK 10','US 4','US 6','One Size'])->map(fn ($name,$i) => Size::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'international_size'=>$name,'sort_order'=>$i,'status'=>'active']))->values();

        $womenFamilies = ['Abaya', 'Niqab', 'Burqa', 'Hijab Set', 'Khimar', 'Jilbab', 'Maxi Dress', 'Modest Top', 'Wide Leg Pant', 'Scarf'];
        $menFamilies = ['Thobe', 'Kandura', 'Shalwar Kameez', 'Kurta', 'Jubba', 'Modest Shirt', 'Sirwal', 'Kufi Cap', 'Prayer Set', 'Waistcoat'];
        $catalogFamilies = collect($womenFamilies)->zip($menFamilies)->flatMap(fn ($pair) => [
            ['gender' => 'women', 'name' => $pair[0]],
            ['gender' => 'men', 'name' => $pair[1]],
        ])->values();

        $categories = collect(range(1, 50))->map(function ($i) use ($catalogFamilies) {
            $family = $catalogFamilies[($i - 1) % $catalogFamilies->count()];
            $label = $family['gender'] === 'men' ? "Men's {$family['name']}" : $family['name'];
            $name = "{$label} {$i}";
            $category = Category::query()->updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => "Contemporary Islamic {$label}.", 'status' => 'active', 'is_featured' => $i <= 8, 'is_trending' => $i <= 12]);
            foreach (range(1, 3) as $child) {
                Category::query()->updateOrCreate(['slug' => Str::slug("{$name} edit {$child}")], ['parent_id' => $category->id, 'name' => "{$name} Edit {$child}", 'status' => 'active']);
            }

            return $category;
        });

        $brands = collect(range(1, 100))->map(fn ($i) => Brand::query()->updateOrCreate(['slug' => "velora-studio-{$i}"], ['name' => "Velora Studio {$i}", 'description' => 'Islamic modest fashion for men and women.', 'country' => ['Pakistan', 'UAE', 'Turkey', 'UK', 'Indonesia'][($i - 1) % 5], 'status' => 'active', 'is_featured' => $i <= 12]));
        $supplierId = User::query()->where('email', 'supplier@velora.test')->value('id');

        foreach (range(1, 1000) as $i) {
            $family = $catalogFamilies[($i - 1) % $catalogFamilies->count()];
            $gender = $family['gender'];
            $familyName = $family['name'];
            $prefix = $gender === 'men' ? "Men's {$familyName}" : $familyName;
            $name = "Velora {$prefix} {$i}";
            $price = 40 + ($i % 20) * 8;
            $photoIds = $gender === 'men' ? self::MEN_PHOTO_IDS : self::WOMEN_PHOTO_IDS;
            $coverage = $gender === 'women' ? 'Full' : ['Full', 'Modest', 'Layered'][$i % 3];

            $product = Product::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'short_description' => "A refined {$prefix} for every day.",
                'description' => "Made for comfort and confidence, this {$prefix} offers complete modest coverage with contemporary Islamic styling.",
                'sku' => "VLR-{$i}",
                'barcode' => '890'.str_pad((string) $i, 9, '0', STR_PAD_LEFT),
                'brand_id' => $brands[$i % 100]->id,
                'category_id' => $categories[$i % 50]->id,
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
