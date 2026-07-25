<?php

return [
    'rewards' => [
        ['id' => 1, 'name' => 'Complimentary styling session', 'description' => 'A personalized AI stylist consultation.', 'points_cost' => 1200],
        ['id' => 2, 'name' => 'Early access preview', 'description' => 'Shop new arrivals 48 hours before public release.', 'points_cost' => 800],
        ['id' => 3, 'name' => '$25 store credit', 'description' => 'Apply credit toward your next considered purchase.', 'points_cost' => 2000],
        ['id' => 4, 'name' => 'Complimentary shipping', 'description' => 'One complimentary standard shipping redemption.', 'points_cost' => 600],
    ],
    'achievements' => [
        ['id' => 1, 'name' => 'First edit', 'description' => 'Complete your first purchase.', 'progress' => 0],
        ['id' => 2, 'name' => 'Style explorer', 'description' => 'Save three AI outfit recommendations.', 'progress' => 0],
        ['id' => 3, 'name' => 'Circle member', 'description' => 'Earn your first 500 loyalty points.', 'progress' => 0],
    ],
    'vip_tiers' => [
        ['slug' => 'member', 'label' => 'Circle', 'threshold' => 0],
        ['slug' => 'atelier', 'label' => 'Atelier', 'threshold' => 2500],
        ['slug' => 'maison', 'label' => 'Maison', 'threshold' => 10000],
    ],
    'performance' => [
        'slow_request_ms' => (int) env('VELORA_SLOW_REQUEST_MS', 750),
    ],
];
