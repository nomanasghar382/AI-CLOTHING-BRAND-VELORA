<?php

namespace App\Support;

final class FreeCatalogPhotoPool
{
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

    /** Brand portrait only when it lives in the kurta folder (kurta photo ≠ thobe/trouser). */
    public static function familyUsesBrandModel(string $familyName): bool
    {
        if ($familyName !== 'Kurta') {
            return false;
        }

        foreach (self::familyFolderPhotos('men', 'kurta') as $path) {
            if (str_contains(strtolower($path), 'brand-model')) {
                return true;
            }
        }

        return false;
    }

    public static function usesMenBrandModel(): bool
    {
        return self::familyUsesBrandModel('Kurta');
    }

    public static function usesWomenCatalogPhotos(): bool
    {
        return count(self::familyFolderPhotos('women', 'niqab')) > 0
            || count(self::familyFolderPhotos('women', 'abaya')) > 0;
    }

    /** Photos in free-catalog/{gender}/{familyKey}/ — strict garment match. */
    /** @return list<string> */
    public static function familyFolderPhotos(string $gender, string $familyKey): array
    {
        $dir = public_path("free-catalog/{$gender}/{$familyKey}");
        if (! is_dir($dir)) {
            return [];
        }

        $paths = [];
        foreach (glob($dir.'/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [] as $file) {
            $paths[] = '/free-catalog/'.$gender.'/'.$familyKey.'/'.basename($file);
        }

        sort($paths);

        return $paths;
    }

    /** @return list<array{type: string, id?: string, path?: string}>} */
    public static function forGender(string $gender): array
    {
        $catalog = self::catalog();
        $familyKeys = array_unique(array_values($catalog['family_keys'][$gender] ?? []));
        $entries = [];

        foreach ($familyKeys as $familyKey) {
            foreach (self::familyFolderPhotos($gender, $familyKey) as $path) {
                $entries[] = ['type' => 'local', 'path' => $path];
            }
        }

        return $entries;
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
    private static function unsplashPool(string $gender, string $familyKey): array
    {
        $catalog = self::catalog();
        $pool = $catalog["{$gender}_pools"][$familyKey] ?? [];

        return array_values(array_filter($pool, fn (string $id) => ! in_array($id, $catalog['blacklist'], true)));
    }

    /**
     * STRICT: image must match product garment family.
     * Kurta card → kurta folder only. Trouser card → trousers flat-lay only. Never cross-garment.
     *
     * @return array{type: string, id?: string, path?: string}
     */
    public static function entryFor(string $gender, string $familyName, int $ordinal, int $galleryIndex, array $fallbackPool): array
    {
        $familyKey = self::familyKey($gender, $familyName);
        $local = self::familyFolderPhotos($gender, $familyKey);

        if ($local !== []) {
            $path = $local[($ordinal + $galleryIndex) % count($local)];

            return ['type' => 'local', 'path' => $path];
        }

        $pool = self::unsplashPool($gender, $familyKey);
        if ($pool !== []) {
            $id = $pool[($ordinal + ($galleryIndex * 11)) % count($pool)];

            return ['type' => 'unsplash', 'id' => $id];
        }

        // Last resort: same-family unsplash only (never random cross-family fallback).
        $id = $pool[0] ?? null;
        if ($id) {
            return ['type' => 'unsplash', 'id' => $id];
        }

        return $fallbackPool[0] ?? ['type' => 'unsplash', 'id' => 'photo-1774527929835-282b1b85cd3a'];
    }

    public static function counts(): array
    {
        $women = 0;
        $men = 0;
        $catalog = self::catalog();

        foreach (array_unique(array_values($catalog['family_keys']['women'] ?? [])) as $key) {
            $women += count(self::familyFolderPhotos('women', $key));
        }
        foreach (array_unique(array_values($catalog['family_keys']['men'] ?? [])) as $key) {
            $men += count(self::familyFolderPhotos('men', $key));
        }

        return [
            'women_local' => $women,
            'men_local' => $men,
            'women_total' => count(self::forGender('women')),
            'men_total' => count(self::forGender('men')),
            'men_brand_model' => self::usesMenBrandModel(),
            'women_catalog_photos' => self::usesWomenCatalogPhotos(),
        ];
    }
}
