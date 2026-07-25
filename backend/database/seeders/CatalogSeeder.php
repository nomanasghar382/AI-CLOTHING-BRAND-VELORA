<?php

namespace Database\Seeders;

use App\Models\{Brand, Category, Color, Inventory, Product, ProductImage, ProductVariant, Size, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /** @var array<string, list<string>> Face-covered niqab + mannequin editorial shots. */
    private const WOMEN_FAMILY_PHOTOS = [
        'Niqab' => ['photo-1744727811425-e1c0af8b4022', 'photo-1559730775-67f621597262', 'photo-1618297655311-ab851e7045d6'],
        'Hijab' => ['photo-1771162766051-c330f1d664ea', 'photo-1744727811425-e1c0af8b4022'],
        'Khimar' => ['photo-1744727811425-e1c0af8b4022', 'photo-1559730775-67f621597262'],
        'Abaya' => ['photo-1618297655311-ab851e7045d6', 'photo-1744727811425-e1c0af8b4022', 'photo-1559730775-67f621597262'],
        'Jilbab' => ['photo-1559730775-67f621597262', 'photo-1744727811425-e1c0af8b4022'],
        'Burqa' => ['photo-1744727811425-e1c0af8b4022', 'photo-1618297655311-ab851e7045d6'],
        'Maxi Dress' => ['photo-1771162766051-c330f1d664ea', 'photo-1744727811425-e1c0af8b4022'],
        'Modest Top' => ['photo-1771162766051-c330f1d664ea', 'photo-1618297655311-ab851e7045d6'],
        'Islamic Trouser' => ['photo-1771162766051-c330f1d664ea', 'photo-1744727811425-e1c0af8b4022'],
        'Wide Leg Pant' => ['photo-1771162766051-c330f1d664ea', 'photo-1559730775-67f621597262'],
        'Modest Skirt' => ['photo-1771162766051-c330f1d664ea', 'photo-1744727811425-e1c0af8b4022'],
        'Scarf' => ['photo-1771162766051-c330f1d664ea', 'photo-1618297655311-ab851e7045d6'],
    ];

    /** @var array<string, list<string>> Young Gen Z men's modest wear — kurta, thobe, shalwar. */
    private const MEN_FAMILY_PHOTOS = [
        'Thobe' => ['photo-1756412066323-a336d2becc10', 'photo-1578507435314-e39e7852eddd', 'photo-1564289851149-a9f8940f795c'],
        'Jubba' => ['photo-1756412066323-a336d2becc10', 'photo-1774424420923-6936309c3c5e'],
        'Kandura' => ['photo-1578507435314-e39e7852eddd', 'photo-1756412066323-a336d2becc10'],
        'Shalwar Kameez' => ['photo-1774527929835-282b1b85cd3a', 'photo-1759567066672-4b9f48000096'],
        'Kurta' => ['photo-1774527929835-282b1b85cd3a', 'photo-1759567066672-4b9f48000096'],
        'Islamic T-Shirt' => ['photo-1759567066672-4b9f48000096', 'photo-1774527929835-282b1b85cd3a'],
        'Modest Shirt' => ['photo-1759567066672-4b9f48000096', 'photo-1774527929835-282b1b85cd3a'],
        'Islamic Trouser' => ['photo-1759567066672-4b9f48000096', 'photo-1774527929835-282b1b85cd3a'],
        'Chino Pant' => ['photo-1774527929835-282b1b85cd3a', 'photo-1759567066672-4b9f48000096'],
        'Kufi Cap' => ['photo-1774424420923-6936309c3c5e', 'photo-1757143137159-316220046829'],
        'Waistcoat' => ['photo-1759567066672-4b9f48000096', 'photo-1774527929835-282b1b85cd3a'],
        'Prayer Set' => ['photo-1578507435314-e39e7852eddd', 'photo-1756412066323-a336d2becc10'],
    ];

    private static function imageUrl(string $photoId, int $width): string
    {
        $quality = $width <= 480 ? 82 : 85;

        return "https://images.unsplash.com/{$photoId}?auto=format&fit=crop&w={$width}&q={$quality}&dpr=2";
    }

    /** @param array<string, list<string>> $familyPhotos */
    private static function photosForFamily(array $familyPhotos, string $familyName): array
    {
        return $familyPhotos[$familyName] ?? array_values($familyPhotos)[0];
    }

    public function run(): void
    {
        $colors = collect(['Black'=>'#111827','Ivory'=>'#FFFFF0','Emerald'=>'#047857','Plum'=>'#7E2253','Navy'=>'#1E3A8A','Sand'=>'#D6C5A2','Rose'=>'#E9A0B5','Olive'=>'#556B2F','Taupe'=>'#8B7D6B','Cocoa'=>'#6F4E37','Sky'=>'#87CEEB','Lilac'=>'#C8A2C8','Stone'=>'#78716C','Sage'=>'#9CAF88','Burgundy'=>'#800020','Teal'=>'#0F766E','Mocha'=>'#967969','Coral'=>'#FF7F50','Silver'=>'#C0C0C0','Gold'=>'#D4AF37'])->map(fn ($hex, $name) => Color::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'hex_code'=>$hex,'status'=>'active']))->values();
        $sizes = collect(['XS','S','M','L','XL','XXL','EU 34','EU 36','EU 38','EU 40','UK 8','UK 10','US 4','US 6','One Size'])->map(fn ($name,$i) => Size::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'international_size'=>$name,'sort_order'=>$i,'status'=>'active']))->values();

        $womenFamilies = array_keys(self::WOMEN_FAMILY_PHOTOS);
        $menFamilies = array_keys(self::MEN_FAMILY_PHOTOS);

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
            $prefix = $isMen ? "Men's {$familyName}" : $familyName;
            $name = "Velora {$prefix} {$i}";
            $price = 35 + ($i % 25) * 6;
            $photoIds = $isMen
                ? self::photosForFamily(self::MEN_FAMILY_PHOTOS, $familyName)
                : self::photosForFamily(self::WOMEN_FAMILY_PHOTOS, $familyName);
            $categoryPool = $isMen ? $menCategories : $womenCategories;
            $coverage = $gender === 'women' ? 'Full' : ['Full', 'Modest', 'Layered'][$i % 3];
            $tagline = $gender === 'women'
                ? 'Full-coverage fit made for Gen Z modest wardrobes.'
                : 'Clean modest silhouette for school, Eid, and everyday.';

            $product = Product::query()->updateOrCreate(['sku' => "VLR-{$i}"], [
                'slug' => Str::slug($name),
                'name' => $name,
                'short_description' => $tagline,
                'description' => "The Velora {$prefix} is designed for young Muslim shoppers who want complete modest coverage without compromising on style. Premium fabric, relaxed Gen Z fit, and easy layering for everyday wear, Jummah, and Eid.",
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
                $photoId = $photoIds[($i + $j) % count($photoIds)];
                $url = self::imageUrl($photoId, 1080);
                $thumbnailUrl = self::imageUrl($photoId, 540);
                ProductImage::query()->updateOrCreate(['product_id' => $product->id, 'sort_order' => $j], ['url' => $url, 'thumbnail_url' => $thumbnailUrl, 'alt_text' => $name, 'is_primary' => $j === 0]);
                $variant = ProductVariant::query()->updateOrCreate(['sku' => "VLR-{$i}-{$j}"], ['product_id' => $product->id, 'color_id' => $selectedColors[$j % 2]->id, 'size_id' => $selectedSizes[$j % 3]->id, 'stock_quantity' => 5 + $i % 30, 'status' => 'active']);
                Inventory::query()->updateOrCreate(['product_id' => $product->id, 'product_variant_id' => $variant->id, 'location' => 'primary'], ['current_stock' => $variant->stock_quantity, 'reserved_stock' => 0, 'minimum_stock' => 5]);
            }
        }
    }
}
