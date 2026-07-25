<?php

namespace Database\Seeders;

use App\Models\{Brand, Category, Color, Inventory, Product, ProductImage, ProductVariant, Size, User};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /** @var list<string> */
    private const DEMO_IMAGE_URLS = [
        'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1551803091-e20673f15770?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1506629905607-d405b7a30db6?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1483985988355-763728e3685b?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = collect(['Black'=>'#111827','Ivory'=>'#FFFFF0','Emerald'=>'#047857','Plum'=>'#7E2253','Navy'=>'#1E3A8A','Sand'=>'#D6C5A2','Rose'=>'#E9A0B5','Olive'=>'#556B2F','Taupe'=>'#8B7D6B','Cocoa'=>'#6F4E37','Sky'=>'#87CEEB','Lilac'=>'#C8A2C8','Stone'=>'#78716C','Sage'=>'#9CAF88','Burgundy'=>'#800020','Teal'=>'#0F766E','Mocha'=>'#967969','Coral'=>'#FF7F50','Silver'=>'#C0C0C0','Gold'=>'#D4AF37'])->map(fn ($hex, $name) => Color::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'hex_code'=>$hex,'status'=>'active']))->values();
        $sizes = collect(['XS','S','M','L','XL','XXL','EU 34','EU 36','EU 38','EU 40','UK 8','UK 10','US 4','US 6','One Size'])->map(fn ($name,$i) => Size::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'international_size'=>$name,'sort_order'=>$i,'status'=>'active']))->values();
        $families = ['Abaya','Hijab','Maxi Dress','Modest Top','Wide Leg Pant','Cardigan','Tunic','Scarf','Bag','Shoe'];
        $categories = collect(range(1, 50))->map(function ($i) use ($families) { $name = $families[($i-1)%10]." {$i}"; $category = Category::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'description'=>"Contemporary {$name}.",'status'=>'active','is_featured'=>$i<=8,'is_trending'=>$i<=12]); foreach(range(1,3) as $child) Category::query()->updateOrCreate(['slug'=>Str::slug("{$name} edit {$child}")], ['parent_id'=>$category->id,'name'=>"{$name} Edit {$child}",'status'=>'active']); return $category; });
        $brands = collect(range(1,100))->map(fn ($i) => Brand::query()->updateOrCreate(['slug'=>"velora-studio-{$i}"], ['name'=>"Velora Studio {$i}",'description'=>'Modern modest fashion.','country'=>['Pakistan','UAE','Turkey','UK','Indonesia'][($i-1)%5],'status'=>'active','is_featured'=>$i<=12]));
        $supplierId = User::query()->where('email','supplier@velora.test')->value('id');
        foreach (range(1, 1000) as $i) {
            $name = "Velora ".$families[($i-1)%10]." {$i}"; $price = 40 + ($i % 20)*8;
            $product = Product::query()->updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'short_description'=>"A refined {$name} for every day.",'description'=>"Made for movement and confidence, this {$name} balances contemporary lines with comfortable coverage.",'sku'=>"VLR-{$i}",'barcode'=>'890'.str_pad((string)$i,9,'0',STR_PAD_LEFT),'brand_id'=>$brands[$i%100]->id,'category_id'=>$categories[$i%50]->id,'supplier_id'=>$supplierId,'price'=>$price,'sale_price'=>$i%4===0?$price*.8:null,'cost_price'=>$price*.4,'stock_quantity'=>10+$i%75,'minimum_stock'=>5,'status'=>'published','is_featured'=>$i<=24,'is_trending'=>$i%9===0,'is_new_arrival'=>$i<=48,'fabric'=>['Linen','Cotton','Jersey','Satin','Viscose'][$i%5],'material'=>['Organic Cotton','Premium Viscose','Woven Linen','Soft Crepe'][$i%4],'fit_type'=>['Relaxed','Tailored','Flowing'][$i%3],'coverage_level'=>['Full','Modest','Layered'][$i%3],'care_instructions'=>'Machine wash cold. Dry flat. Warm iron if needed.','gender'=>'women','season'=>['Spring','Summer','Autumn','Winter'][$i%4],'published_at'=>now()->subDays($i%120)]);
            $selectedColors = $colors->slice($i%10,2)->values(); $selectedSizes=$sizes->slice($i%10,3)->values(); $product->colors()->sync($selectedColors->pluck('id')); $product->sizes()->sync($selectedSizes->pluck('id'));
            foreach (range(0, 4) as $j) {
                $url = self::DEMO_IMAGE_URLS[($i + $j) % count(self::DEMO_IMAGE_URLS)];
                ProductImage::query()->updateOrCreate(['product_id' => $product->id, 'sort_order' => $j], ['url' => $url, 'thumbnail_url' => $url, 'alt_text' => $name, 'is_primary' => $j === 0]); $variant=ProductVariant::query()->updateOrCreate(['sku'=>"VLR-{$i}-{$j}"],['product_id'=>$product->id,'color_id'=>$selectedColors[$j%2]->id,'size_id'=>$selectedSizes[$j%3]->id,'stock_quantity'=>5+$i%30,'status'=>'active']); Inventory::query()->updateOrCreate(['product_id'=>$product->id,'product_variant_id'=>$variant->id,'location'=>'primary'],['current_stock'=>$variant->stock_quantity,'reserved_stock'=>0,'minimum_stock'=>5]); }
        }
    }
}
