<?php

namespace App\Support;

final class FreeCatalogPhotoPool
{
    /** @var list<string> */
    private const MEN_BRAND_MODEL_NAMES = [
        'brand-model.jpg',
        'brand-model.jpeg',
        'brand-model.png',
        'brand-model.webp',
        'noman-asghar.jpg',
        'noman-asghar.jpeg',
        'noman-asghar.png',
    ];

    public static function menBrandModelPath(): ?string
    {
        foreach (self::MEN_BRAND_MODEL_NAMES as $name) {
            if (is_file(public_path("free-catalog/men/{$name}"))) {
                return '/free-catalog/men/'.$name;
            }
        }

        return null;
    }

    public static function usesMenBrandModel(): bool
    {
        return self::menBrandModelPath() !== null;
    }

    public static function usesWomenCatalogPhotos(): bool
    {
        return count(self::womenCatalogPhotos()) > 0;
    }

    /** Your uploaded women looks — niqab, abaya, khimar, jilbab (excludes README). */
    /** @return list<string> */
    public static function womenCatalogPhotos(): array
    {
        return self::localPhotos('women');
    }

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

        if ($gender === 'women' && self::usesWomenCatalogPhotos()) {
            return $pool;
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

    /** @return list<string> Public URL paths e.g. /free-catalog/women/01.jpg */
    public static function localPhotos(string $gender): array
    {
        $dir = public_path("free-catalog/{$gender}");
        if (! is_dir($dir)) {
            return [];
        }

        $reserved = $gender === 'men' ? self::MEN_BRAND_MODEL_NAMES : [];
        $paths = [];

        foreach (glob($dir.'/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [] as $file) {
            $name = basename($file);
            if (in_array(strtolower($name), array_map('strtolower', $reserved), true)) {
                continue;
            }
            $paths[] = '/free-catalog/'.$gender.'/'.$name;
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

    public static function menGarmentPhoto(int $ordinal, int $galleryIndex): string
    {
        $catalog = require database_path('data/GenZCuratedCatalog.php');
        $pool = array_merge($catalog['men_editorial'], $catalog['men_product']);
        $index = $ordinal + ($galleryIndex * 13);

        return $pool[$index % count($pool)];
    }

    public static function womenGarmentPhoto(int $ordinal, int $galleryIndex): string
    {
        $catalog = require database_path('data/GenZCuratedCatalog.php');
        $pool = array_merge($catalog['women_editorial'], $catalog['women_product']);
        $index = $ordinal + ($galleryIndex * 11);

        return $pool[$index % count($pool)];
    }

    /** @return array{type: string, id?: string, path?: string} */
    public static function entryFor(string $gender, int $ordinal, int $galleryIndex, array $fallbackPool): array
    {
        if ($gender === 'men' && $galleryIndex === 0 && ($brand = self::menBrandModelPath())) {
            return ['type' => 'local', 'path' => $brand];
        }

        if ($gender === 'men' && $galleryIndex > 0 && self::usesMenBrandModel()) {
            return ['type' => 'unsplash', 'id' => self::menGarmentPhoto($ordinal, $galleryIndex)];
        }

        $womenPhotos = self::womenCatalogPhotos();
        if ($gender === 'women' && $womenPhotos !== []) {
            $path = $womenPhotos[($ordinal + $galleryIndex) % count($womenPhotos)];

            return ['type' => 'local', 'path' => $path];
        }

        $index = $ordinal + ($galleryIndex * 97);

        return $fallbackPool[$index % count($fallbackPool)];
    }

    public static function counts(): array
    {
        return [
            'women_local' => count(self::womenCatalogPhotos()),
            'men_local' => count(self::localPhotos('men')),
            'women_total' => count(self::forGender('women')),
            'men_total' => count(self::forGender('men')),
            'men_brand_model' => self::usesMenBrandModel(),
            'women_catalog_photos' => self::usesWomenCatalogPhotos(),
        ];
    }
}
