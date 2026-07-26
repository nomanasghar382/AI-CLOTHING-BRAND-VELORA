<?php

namespace App\Support;

final class SportsCatalogPhotoPool
{
    /** @var array<string, mixed>|null */
    private static ?array $catalog = null;

    /** @return array<string, mixed> */
    private static function catalog(): array
    {
        return self::$catalog ??= require database_path('data/GenZSportsCatalog.php');
    }

    public static function familyKey(string $familyName): string
    {
        return self::catalog()['family_keys'][$familyName] ?? 'training_tee';
    }

    public static function isFootwear(string $familyName): bool
    {
        return in_array($familyName, self::catalog()['footwear_families'], true);
    }

    /** @return list<string> */
    public static function familyFolderPhotos(string $familyKey): array
    {
        $dir = public_path("free-catalog/men/sport/{$familyKey}");
        if (! is_dir($dir)) {
            return [];
        }

        $paths = [];
        foreach (glob($dir.'/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [] as $file) {
            $paths[] = '/free-catalog/men/sport/'.$familyKey.'/'.basename($file);
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

        return "https://images.unsplash.com/{$photoId}?auto=format&fit=crop&w={$width}&h=".($width <= 540 ? 900 : 1350)."&q={$quality}&dpr=2";
    }

    /** @return array{type: string, id?: string, path?: string} */
    public static function entryFor(string $familyName, int $ordinal, int $galleryIndex): array
    {
        $familyKey = self::familyKey($familyName);
        $local = self::familyFolderPhotos($familyKey);

        if ($local !== []) {
            return ['type' => 'local', 'path' => $local[($ordinal + $galleryIndex) % count($local)]];
        }

        $catalog = self::catalog();
        $pool = array_values(array_filter(
            $catalog['pools'][$familyKey] ?? [],
            fn (string $id) => ! in_array($id, $catalog['blacklist'], true)
        ));

        if ($pool === []) {
            return ['type' => 'unsplash', 'id' => 'photo-1542291026-7eec264c27ff'];
        }

        return ['type' => 'unsplash', 'id' => $pool[($ordinal + ($galleryIndex * 7)) % count($pool)]];
    }
}
