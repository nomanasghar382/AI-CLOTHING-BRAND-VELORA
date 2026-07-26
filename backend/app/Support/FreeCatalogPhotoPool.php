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

    /** @var array<string, mixed>|null */
    private static ?array $catalog = null;

    /** @return array<string, mixed> */
    private static function catalog(): array
    {
        return self::$catalog ??= require database_path('data/GenZCuratedCatalog.php');
    }

    public static function familyKey(string $gender, string $familyName): string
    {
        $catalog = self::catalog();

        return $catalog['family_keys'][$gender][$familyName] ?? 'default';
    }

    public static function familyUsesBrandModel(string $familyName): bool
    {
        if (! self::usesMenBrandModel()) {
            return false;
        }

        return in_array($familyName, self::catalog()['men_brand_model_families'], true);
    }

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

    /** @return list<string> */
    public static function womenCatalogPhotos(): array
    {
        return self::localPhotos('women');
    }

    /** Local women photos that match this garment family (niqab photo never on trousers). */
    /** @return list<string> */
    public static function localPhotosForFamily(string $gender, string $familyKey): array
    {
        if ($gender !== 'women') {
            return [];
        }

        $catalog = self::catalog();
        $tags = $catalog['women_local_family_tags'] ?? [];
        $matched = [];

        foreach (self::localPhotos('women') as $path) {
            $basename = pathinfo($path, PATHINFO_FILENAME);
            foreach ($tags as $prefix => $familyKeys) {
                if (! str_starts_with($basename, $prefix)) {
                    continue;
                }
                if (in_array($familyKey, $familyKeys, true)) {
                    $matched[] = $path;
                }
            }
        }

        return $matched;
    }

    /** @return list<array{type: string, id?: string, path?: string}>} */
    public static function forGender(string $gender): array
    {
        $catalog = self::catalog();
        $pools = $catalog["{$gender}_pools"] ?? [];
        $blacklist = array_flip($catalog['blacklist']);
        $entries = [];

        foreach (self::localPhotos($gender) as $path) {
            $entries[] = ['type' => 'local', 'path' => $path];
        }

        foreach ($pools as $pool) {
            foreach ($pool as $id) {
                if (! isset($blacklist[$id])) {
                    $entries[] = ['type' => 'unsplash', 'id' => $id];
                }
            }
        }

        return $entries;
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
        $catalog = self::catalog();
        $crop = $catalog['crop_variants'][$variant] ?? '';
        $host = str_starts_with($photoId, 'premium_photo') ? 'plus.unsplash.com' : 'images.unsplash.com';

        return "https://{$host}/{$photoId}?auto=format&fit=crop&w={$width}&h=".($width <= 540 ? 900 : 1350)."&q={$quality}&dpr=2{$crop}";
    }

    /** @return list<string> */
    private static function unsplashPool(string $gender, string $familyKey, bool $productShot): array
    {
        $catalog = self::catalog();
        $poolKey = $productShot ? "{$gender}_product_pools" : "{$gender}_pools";
        $pool = $catalog[$poolKey][$familyKey] ?? $catalog[$poolKey]['bottoms'] ?? [];

        return array_values(array_filter($pool, fn (string $id) => ! in_array($id, $catalog['blacklist'], true)));
    }

    /** @return array{type: string, id?: string, path?: string} */
    public static function entryFor(string $gender, string $familyName, int $ordinal, int $galleryIndex, array $fallbackPool): array
    {
        $familyKey = self::familyKey($gender, $familyName);

        if ($gender === 'men' && $galleryIndex === 0 && self::familyUsesBrandModel($familyName) && ($brand = self::menBrandModelPath())) {
            return ['type' => 'local', 'path' => $brand];
        }

        if ($gender === 'women') {
            $local = self::localPhotosForFamily('women', $familyKey);
            if ($local !== []) {
                $path = $local[($ordinal + $galleryIndex) % count($local)];

                return ['type' => 'local', 'path' => $path];
            }
        }

        $productShot = $galleryIndex > 0 || $familyKey === 'bottoms' || $familyKey === 'accessory';
        $pool = self::unsplashPool($gender, $familyKey, $productShot);

        if ($pool === []) {
            $index = $ordinal + ($galleryIndex * 97);

            return $fallbackPool[$index % max(1, count($fallbackPool))];
        }

        $id = $pool[($ordinal + ($galleryIndex * 11)) % count($pool)];

        return ['type' => 'unsplash', 'id' => $id];
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
