<?php

namespace App\Support;

use Illuminate\Support\Str;

final class NikeCatalogMapper
{
    /** @var array<string, string> */
    private const SUBTITLE_TO_FAMILY = [
        'shoes' => 'Lifestyle Sneakers',
        'sneaker' => 'Lifestyle Sneakers',
        'basketball shoes' => 'Basketball Shoes',
        'running shoes' => 'Running Shoes',
        'tactical boot' => 'Training Shoes',
        'sandal' => 'Sport Slides',
        'slides' => 'Sport Slides',
        'hoodie' => 'Hoodie',
        'pullover hoodie' => 'Hoodie',
        'full-zip hoodie' => 'Hoodie',
        'track jacket' => 'Track Jacket',
        'soccer jacket' => 'Track Jacket',
        'jacket' => 'Track Jacket',
        'jersey' => 'Basketball Jersey',
        'football jersey' => 'Football Jersey',
        'baseball jersey' => 'Football Jersey',
        'shorts' => 'Training Shorts',
        'joggers' => 'Joggers',
        'tights' => 'Joggers',
        'leggings' => 'Joggers',
        't-shirt' => 'Training Tee',
        'tee' => 'Training Tee',
        'tank' => 'Running Tank',
        'tank top' => 'Running Tank',
        'polo' => 'Training Tee',
        'overalls' => 'Joggers',
        'crew' => 'Hoodie',
        'sweatshirt' => 'Hoodie',
        'compression' => 'Compression Top',
        'headband' => 'Training Tee',
        'cap' => 'Training Tee',
        'hat' => 'Training Tee',
        'beanie' => 'Hoodie',
        'backpack' => 'Training Tee',
        'boot' => 'Training Shoes',
    ];

    public static function slugFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $segment = trim((string) basename($path), '/');

        return Str::slug($segment ?: 'nike-product');
    }

    public static function genderFromSubtitle(string $subtitle): string
    {
        $text = self::normalizeText($subtitle);

        if (preg_match("/women'?s|womens\b/", $text)) {
            return 'women';
        }
        if (preg_match('/big kids|little kids|toddler|\bboys\b|\bgirls\b/', $text)) {
            return 'kids';
        }
        if (preg_match("/men'?s|mens\b/", $text)) {
            return 'men';
        }

        return 'unisex';
    }

    /** Men's Gen Z sport only — excludes women's, kids, and unisex listings. */
    public static function isMensCatalogRow(string $subtitle, string $url): bool
    {
        $text = self::normalizeText($subtitle.' '.$url);

        if (preg_match('/women|womens|big-kids|little-kids|toddler|\/boys-|girls-|-girls-|-boys-|-kids-|-kid-/', $text)) {
            return false;
        }

        return preg_match("/men'?s|mens\b|-mens-/", $text) === 1;
    }

    private static function normalizeText(string $text): string
    {
        $normalized = str_replace(["\u{2019}", "\u{2018}", '`'], "'", $text);

        return strtolower($normalized);
    }

    public static function catalogLineFromSubtitle(string $subtitle, string $name): string
    {
        $text = strtolower($subtitle.' '.$name);

        if (preg_match('/shoe|sneaker|boot|sandal|slide|slides|footwear/', $text)) {
            return 'footwear';
        }

        return 'apparel';
    }

    public static function familyFromSubtitle(string $subtitle, string $name): string
    {
        $text = strtolower($subtitle.' '.$name);

        foreach (self::SUBTITLE_TO_FAMILY as $needle => $family) {
            if (str_contains($text, $needle)) {
                if ($needle === 'shoes' || $needle === 'sneaker') {
                    if (str_contains($text, 'basketball')) {
                        return 'Basketball Shoes';
                    }
                    if (str_contains($text, 'running') || str_contains($text, 'pegasus') || str_contains($text, 'max')) {
                        return 'Running Shoes';
                    }
                    if (str_contains($text, 'jordan') || str_contains($text, 'air force') || str_contains($text, 'dunk')) {
                        return 'Lifestyle Sneakers';
                    }
                    if (str_contains($text, 'training') || str_contains($text, 'zion')) {
                        return 'Basketball Shoes';
                    }

                    return 'Lifestyle Sneakers';
                }

                return $family;
            }
        }

        if (str_contains($text, 'jersey')) {
            return str_contains($text, 'football') || str_contains($text, 'nfl')
              ? 'Football Jersey'
              : 'Basketball Jersey';
        }

        return 'Training Tee';
    }

    /** @return list<string> */
    public static function parseSizes(?string $sizes): array
    {
        if (! $sizes || trim($sizes) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\s*\|\s*/', $sizes) ?: [])));
    }

    public static function formatDescription(string $description): string
    {
        $text = trim($description);
        if ($text === '') {
            return '';
        }

        // Insert paragraph breaks before benefit-style headings in scraped copy.
        $text = preg_replace('/([.!?])\s*(Benefits|Product Details|Stay Dry|More Benefits)/', "$1\n\n$2", $text) ?? $text;

        return $text;
    }

    public static function shortDescription(string $description, string $name): string
    {
        $plain = trim($description);
        if ($plain === '') {
            return $name;
        }

        $first = preg_split('/\n+/', $plain)[0] ?? $plain;

        return Str::limit(trim($first), 160);
    }

    public static function skuFromUniqId(string $uniqId): string
    {
        return 'NK-'.strtoupper(substr(str_replace('-', '', $uniqId), 0, 12));
    }

    public static function colorSlug(string $color): string
    {
        $base = Str::slug(explode('/', $color)[0] ?: $color);

        return $base !== '' ? $base : 'multi';
    }
}
