<?php

namespace App\Support;

final class FreeCatalogPhotoPool
{
    /** @return list<array{type: string, id?: string, path?: string}> */
    public static function forGender(string $gender): array
    {
        $catalog = require database_path('data/GenZCuratedCatalog.php');
        $blacklist = array_flip($catalog['blacklist']);
        $editorial = $gender === 'men' ? $catalog['men_editorial'] : $catalog['women_editorial'];
        $product = $gender === 'men' ? $catalog['men_product'] : $catalog['women_product'];

        $pool = [];

        foreach (self::localPhotos($gender) as $path) {
            $pool[] = ['type' => 'local', 'path' => $path];
        }

        foreach ($editorial as $id) {
            if (! isset($blacklist[$id])) {
                $pool[] = ['type' => 'unsplash', 'id' => $id];
            }
        }

        foreach ($product as $id) {
            if (! isset($blacklist[$id])) {
                $pool[] = ['type' => 'unsplash', 'id' => $id];
            }
        }

        return $pool;
    }

    /** @return list<string> Public URL paths e.g. /free-catalog/women/look-01.jpg */
    public static function localPhotos(string $gender): array
    {
        $dir = public_path("free-catalog/{$gender}");
        if (! is_dir($dir)) {
            return [];
        }

        $paths = [];
        foreach (glob($dir.'/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [] as $file) {
            $paths[] = '/free-catalog/'.$gender.'/'.basename($file);
        }

        sort($paths);

        return $paths;
    }

    public static function urlFor(array $entry, int $width, int $variant = 0): string
    {
        if ($entry['type'] === 'local') {
            return url($entry['path']);
        }

        $quality = $width <= 480 ? 82 : 85;
        $photoId = $entry['id'];
        $catalog = require database_path('data/GenZCuratedCatalog.php');
        $crop = $catalog['crop_variants'][$variant] ?? '';
        $host = str_starts_with($photoId, 'premium_photo') ? 'plus.unsplash.com' : 'images.unsplash.com';

        return "https://{$host}/{$photoId}?auto=format&fit=crop&w={$width}&h=".($width <= 540 ? 900 : 1350)."&q={$quality}&dpr=2{$crop}";
    }

    public static function counts(): array
    {
        return [
            'women_local' => count(self::localPhotos('women')),
            'men_local' => count(self::localPhotos('men')),
            'women_total' => count(self::forGender('women')),
            'men_total' => count(self::forGender('men')),
        ];
    }
}
