<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/** Men's gym-to-street niche — the only active VELORA catalog scope. */
final class GymToStreetCatalog
{
    public const FOCUS = 'gym-street';

    /** @return list<string> */
    public static function categorySlugs(): array
    {
        return [
            'mens-sport-training-tee',
            'mens-sport-compression-top',
            'mens-sport-hoodie',
            'mens-sport-joggers',
            'mens-sport-training-shorts',
            'mens-sport-track-jacket',
            'mens-sport-training-shoes',
            'mens-sport-lifestyle-sneakers',
        ];
    }

    /** @return list<string> */
    public static function apparelSlugs(): array
    {
        return [
            'mens-sport-training-tee',
            'mens-sport-compression-top',
            'mens-sport-hoodie',
            'mens-sport-joggers',
            'mens-sport-training-shorts',
            'mens-sport-track-jacket',
        ];
    }

    /** @return list<string> */
    public static function footwearSlugs(): array
    {
        return [
            'mens-sport-training-shoes',
            'mens-sport-lifestyle-sneakers',
        ];
    }

    /** @return list<string> */
    public static function priorityBrands(): array
    {
        return ['nike', 'gymshark', 'adidas', 'under-armour', 'puma', 'lululemon', 'new-balance'];
    }

    public static function applyScope(Builder $query): Builder
    {
        return $query->whereHas('category', fn (Builder $category) => $category->whereIn('slug', self::categorySlugs()));
    }
}
